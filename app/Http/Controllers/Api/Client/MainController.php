<?php

namespace App\Http\Controllers\Api\Client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Validator;
use Response;
use Auth;
use App\Item;
use App\Restaurant;
use App\Order;
use App\Notification;

class MainController extends Controller
{

    public function newOrder(Request $request)
    {
    
        $validation = validator()->make($request->all(), [
            'restaurant_id'     => 'required|exists:restaurants,id',
            'items'             => 'required|array',
            'items.*'           => 'required|exists:items,id',
            'quantities'        => 'required|array',
            'notes'             => 'required|array',
            'address'           => 'required',
            'payment_method_id' => 'required|exists:payment_methods,id',
            //            'need_delivery_at' => 'required|date_format:Y-m-d',// H:i:s
        ]);
        if ($validation->fails()) {
            $data = $validation->errors();
            return responseJson(0, $validation->errors()->first(), $data);
        }
        $restaurant = Restaurant::find($request->restaurant_id);
        // restaurant closed
        if ($restaurant->availability == 'closed') {
            return responseJson(0, 'عذرا المطعم غير متاح في الوقت الحالي');
        }
      
          $order = $request->user()->orders()->create([

            'payment_method_id' => $request->payment_method_id,
            'address' => $request->user()->address,
            'restaurant_id' => $request->restaurant_id
        ]);

        $cost = 0;
        $delivery_cost = $restaurant->delivery_cost;
        
        if ($request->has('items')) {
            $counter = 0;
            foreach ($request->items as $itemId) {

                $item = Item::find($itemId);
                $order->items()->attach([
                $itemId => [
                    'quantity' => $request->quantities[$counter],
                    'price'    => $item->price,
                    'note'     => $request->notes[$counter],
                ]
               ]);
                $cost += ($item->price * $request->quantities[$counter]);
                $counter++;
            }
        }
        // minimum charge
        if ($cost >= $restaurant->minimum_charger) {
            $total = $cost + $delivery_cost; // 200 SAR
            
         /*   $commission = 
             $net = $total - */

            $update = $order->update([
                     'cost'          => $cost,
                     'delivery_cost' => $delivery_cost,
                     'total'         => $total
                 ]);

           $notification = Notification::create([
                    'client_id' => $request->user()->id,
                    'restaurant_id'  => $restaurant->id,
                    'title' =>'لديك طلب جديد',
                    'content' =>$request->user()->name   .  '  لديك طلب جديد من العميل ',
                   // 'action' =>  'new-order',
                    'order_id' => $order->id
                    
            ]);

            $tokens = $restaurant->tokens()->where('token', '!=' ,'null')->pluck('token')->toArray();
          // $tokens = Token::whereIn('client_id', $clientsIds)->where('token', '!=', null)->pluck('token')->toArray();

    
            if(count($tokens))
            {
                $title = $notification->title;
                $content = $notification->content;
                $data =[
                    'order_id' => $order->id,
                    'user_type' => 'restaurant',
                ];
                $send = notifyByFirebase($title , $content , $tokens,$data);
                info("firebase result: " . $send);
            }
            
            $data = [
                'order' => $order->fresh()->load('items','restaurant.city','restaurant.categories','client') // $order->fresh()  ->load (lazy eager loading) ->with('items')
            ];
            return responseJson(1, 'تم الطلب بنجاح', $data);
        } else {
            $order->items()->delete();
            $order->delete();
            return responseJson(0, 'الطلب لابد أن لا يكون أقل من ' . $restaurant->minimum_charger . ' ريال');
        }
    }



    public function myOrders(Request $request)
    {
        $orders = $request->user()->orders()->where(function ($order) use ($request) {
            if ($request->has('state') && $request->state == 'completed') {
                $order->where('state', '!=', 'pending');
            } elseif ($request->has('state') && $request->state == 'current') {
                $order->where('state', '=', 'pending');
            }
        })->with('items','restaurant.city','restaurant.categories','client')->latest()->paginate(20);
        return responseJson(1, 'تم التحميل', $orders);
    }

    public function showOrder(Request $request)
    {
        $order = Order::with('items','restaurant.city','restaurant.categories','client')->find($request->order_id);
        return responseJson(1, 'تم التحميل', $order);
    }
    public function latestOrder(Request $request)
    {
        $order = $request->user()->orders()
                         ->with('restaurant', 'items')
                         ->latest()
                         ->first();
        if ($order) {
            return responseJson(1, 'تم التحميل', $order);
        }
        return responseJson(0, 'لا يوجد');
    }
    public function confirmOrder(Request $request)
    {
        $order = $request->user()->orders()->find($request->order_id);
        if (!$order) {
            return responseJson(0, 'لا يمكن الحصول على البيانات');
        }
       /*if ($order->state == 'rejected') {
            return responseJson(0, 'لا يمكن تأكيد استلام الطلب ، لم يتم قبول الطلب');
        }
        /*if ($order->delivery_confirmed_by_client == 1) {
            return responseJson(1, 'تم تأكيد الاستلام');
        }*/
        $order->update(['state' => 'delivered']);
        $restaurant = $order->restaurant;
      
        $restaurant->notifications()->create([
            'client_id'  => $request->user()->id,

                 'title'      => 'تم تأكيد توصيل طلبك من العميل',
                 //'title_en'   => 'Your order is delivered to client',
                 'content'    => 'تم تأكيد التوصيل للطلب رقم ' . $request->order_id . ' للعميل',
                // 'content_en' => 'Order no. ' . $request->order_id . ' is delivered to client',
                 'order_id'   => $request->order_id,
             ]);
        $tokens = $restaurant->tokens()->where('token', '!=', 'null')->pluck('token')->toArray();
        $audience = ['include_player_ids' => $tokens];
        $contents = [
            'en' => 'Order no. ' . $request->order_id . ' is delivered to client',
            'ar' => 'تم تأكيد التوصيل للطلب رقم ' . $request->order_id . ' للعميل',
        ];
        $send = notifyByOneSignal($audience, $contents, [
            'user_type' => 'restaurant',
            'action'    => 'confirm-order-delivery',
            'order_id'  => $request->order_id,
        ]);
        $send = json_decode($send);
        return responseJson(1, 'تم تأكيد الاستلام');
    }
    public function declineOrder(Request $request)
    {
        $order = $request->user()->orders()->find($request->order_id);
        if (!$order) {
            return responseJson(0, 'لا يمكن الحصول على البيانات');
        }
        /*if ($order->state != 'accepted') {
            return responseJson(0, 'لا يمكن رفض استلام الطلب ، لم يتم قبول الطلب');
        }
        if ($order->delivery_confirmed_by_client == -1) {
            return responseJson(1, 'تم رفض استلام الطلب');
        }*/
        $order->update(['state' => 'declined']);
        $restaurant = $order->restaurant;
       
        $restaurant->notifications()->create([
            'client_id'  => $request->user()->id,
             'title'      => 'تم رفض توصيل طلبك من العميل',
            //'title_en'   => 'Your order delivery is declined by client',
             'content'    => 'تم رفض التوصيل للطلب رقم ' . $request->order_id . ' للعميل',
            // 'content_en' => 'Delivery if order no. ' . $request->order_id . ' is declined by client',
             'order_id'   => $request->order_id,
         ]);
        $tokens = $restaurant->tokens()->where('token', '!=', 'null')->pluck('token')->toArray();
        $audience = ['include_player_ids' => $tokens];
        $contents = [
            'en' => 'Delivery if order no. ' . $request->order_id . ' is declined by client',
            'ar' => 'تم رفض التوصيل للطلب رقم ' . $request->order_id . ' للعميل',
        ];
        $send = notifyByOneSignal($audience, $contents, [
            'user_type' => 'restaurant',
            'action'    => 'decline-order-delivery',
            'order_id'  => $request->order_id,
        ]);
        $send = json_decode($send);
        return responseJson(1, 'تم رفض استلام الطلب');
    }
    public function review(Request $request)
    {
        $validation = validator()->make($request->all(), [
            'rate'          => 'required',
            'comment'       => 'required',
            'restaurant_id' => 'required|exists:restaurants,id',
        ]);
        if ($validation->fails()) {
            return responseJson(0, $validation->errors()->first(), $validation->errors());
        }
        $restaurant = Restaurant::find($request->restaurant_id);
        if (!$restaurant) {
            return responseJson(0, 'لا يمكن الحصول على البيانات');
        }
        $request->merge(['client_id' => $request->user()->id]);


        $clientOrdersCount = $request->user()->orders()
                                     ->where('restaurant_id', $restaurant->id)->count();
                                   
                           
        if ($clientOrdersCount == 0) {
            return responseJson(0, 'لا يمكن التقييم الا بعد تنفيذ طلب من المطعم');
        }
        $checkOrder = $request->user()->orders()
                              ->where('restaurant_id', $restaurant)
                              ->where('state', 'accepted')->OrWhere('state','delivered')->count();
                            
        if ($checkOrder == 0) {
            return responseJson(0, 'لا يمكن التقييم الا بعد بيان حالة استلام الطلب');
        }
        $review = $restaurant->reviews()->create($request->all());
        return responseJson(1, 'تم التقييم بنجاح', [
            'review' => $review->load('client','restaurant')
        ]);
    }

    public function notifications(Request $request)
    {
        //$notifications = $request->user()->notifications()->with('order.client.city','order.restaurant.city')->latest()->paginate(20); //
        //$notifications = $request->user()->notifications()->with('order.client.city','order.restaurant.city')->latest()->paginate(20);
        $ordersid = $request->user()->notifications()
            ->whereHas('order', function ($q) use ($request) {
                    $q->where('orders.delivery_confirmed_by_client', 1);
                })->pluck('id')->toArray();

                $notifications = Notification::where('order_id',$ordersid)->get();

                dd($notifications);
        return responseJson(1, 'تم التحميل', $notifications);
    }

}
