<?php

namespace App\Http\Controllers\Api;

use App\Restaurant;
use App\Item;
use App\Review;
use App\Cart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class RestaurantController extends Controller
{

    public function index()
    {
        
        $restaurants = Restaurant::all();
        return responsejson(1, 'success',$restaurants);
        
    }
    public function show($id)
    {
        
        $restaurant = Restaurant::findOrFail($id);
        return responsejson(1, 'success',$restaurant);
        
    }
    public function item($id)
    {  
        $items = Item::where('restaurant_id',$id)->paginate(20);

        return responsejson(1, 'success',$items);
        
    }
    public function review(Request $request,$id)
    {  
       // if auth token  
        
        $validation = validator()->make($request->all(), [
        'rate'          => 'required',
        'comment'       => 'required',
    ]);
    if ($validation->fails()) {
        return responseJson(0, $validation->errors()->first(), $validation->errors());
    }
    $clientid   = $request->user()->id;

       if($clientid)
       {
        $restaurant = Restaurant::find($id);  
        $review = $restaurant->reviews()->create(['comment'=>$request->comment,
        'rate' => $request->rate,
        'client_id' => $clientid,
        ]);
       

        return responsejson(1, 'success',$review);}

        else {return responsejson(1, 'please login first');}

        
    }

    public function addItemToCart(Request $request){

        $validation = validator()->make($request->all(), [
            'item_id'  => 'required|exists:items,id',
            'quantity' => 'required',
        ]);
        if ($validation->fails()) {
            $data = $validation->errors();
            return responseJson(0, $validation->errors()->first(), $data);
        }
        $item = Item::find($request->item_id);

        $cart = Cart::create([
           
            'note'      => $request->note,
            'quantity'  => $request->quantity,
            'client_id' => $request->user()->id,
            'item_id'   => $item->id,
            'price'     => $item->price
            
        ]);

        $request->user()->cart()->attach($cart->id);

        return responseJson(1, 'add to cart');

    }
    public function allClientCart(Request $request){

        $cart = DB::table('carts')->where('client_id', $request->user()->id)->get();

      //  $cart = Cart::Where($request->client_id);


        return responsejson(1, 'success',$cart);

    }
    public function updateItemToCart(Request $request,$id){

        $update = DB::table('carts')
                ->where('id', $id)
                ->update(['quantity' => $request->quantity]
            );
            return responsejson(1, 'Quantity was updated');
    }
    
    public function removeItemFromCart($id){

        $update = DB::table('carts')->where('id', $id);
        $update ->delete();
    return responsejson(1, 'Quantity was deleted');

    }
    public function ConfirmedCart(Request $request,$id){

        $cart = Cart::findOrFail($id);

        $request->user()->cart()->updateExistingPivot($cart,['confirmed'=>1]);

        return responsejson(1, 'The Order was Confirmed');
    
    }

    public function PaymentMethod(Request $request){

        
        
        return responsejson(1, 'Quantity was deleted');

    }


    public function filter(Request $request)
    {
        $restaurants = Restaurant::where(function ($query) use ($request) {

            if ($request->has('id') and $request->has('city_id')) {
                $query->where(
                             [
                                 'id'=> $request->id ,
                                 'city_id'=> $request->city_id
                             ]);
                
            }
            else{  $query->where('id',$request->id);}
        })->get()->all();


        if($restaurants){return responsejson(1, 'success', $restaurants);}

        else{
            return responsejson(0, 'Restaurant not exists');

    } }


}
