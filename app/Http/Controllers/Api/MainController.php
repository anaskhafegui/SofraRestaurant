<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Contact;
use App\Governorate;
use App\City;
use App\Category;
use App\PaymentMethod;
use App\Restaurant;
use App\Item;
use App\Offer;


class MainController extends Controller
{
    public function governorates(Request $request)
    {
        $governorates = Governorate::where(function($q) use($request){
            if ($request->has('name')){
                $q->where('name','LIKE','%'.$request->name.'%');
            }
        })->paginate(10);
        return responseJson(1,'تم التحميل',$governorates);
    }
    
    public function cities(Request $request)
    {
        $cities = City::where(function($q) use($request){
            if ($request->has('name')){
                $q->where('name','LIKE','%'.$request->name.'%');
            }
        })->where('governorate_id',$request->governorate_id)->paginate(10);

        return responseJson(1,'تم التحميل',$cities);
    }

    public function ajax_region(Request $request)
    {
        $cities = City::where('governorate_id',$request->governorate_id)->get();
        return responseJson(1,'تم التحميل',$cities);
    }
    
    public function paymentMethods()
    {
        $methods = PaymentMethod::all();
        return responseJson(1,'تم التحميل',$methods);
    }


    public function categories()
    {
        $categories = Category::all();
        return responseJson(1,'تم التحميل',$categories);
    }


    public function restaurants(Request $request)
    {
        
        $restaurants = Restaurant::where(function($q) use($request) {
            if ($request->has('keyword'))
            {
                $q->where(function($q2) use($request){
                    $q2->where('name','LIKE','%'.$request->keyword.'%');
                });
            }
            if ($request->has('city_id'))
            {
                $q->where('city_id',$request->city_id);
            }
            
        })->has('items')->with('city', 'categories')->activated()->paginate(10);
        return responseJson(1,'تم التحميل',$restaurants);

 }

    public function restaurant(Request $request)
    {
        $restaurant = Restaurant::with('city','categories')->activated()->findOrFail($request->restaurant_id);
        return responseJson(1,'تم التحميل',$restaurant);
    }

    public function items(Request $request)
    {
        $items = Item::where('restaurant_id',$request->restaurant_id)->enabled()->paginate(20);
        return responseJson(1,'تم التحميل',$items);
    }

    public function reviews(Request $request)
    {
        $restuarant = Restaurant::find($request->restaurant_id);
        if (!$restuarant)
        {
            return responseJson(0,'no data');
        }
        $reviews = $restuarant->reviews()->paginate(10);
        return responseJson(1,'',$reviews);
        
    }


    public function offers(Request $request)
    {
        $offers = Offer::where(function($offer) use($request){
            if($request->has('restaurant_id'))
            {
                $offer->where('restaurant_id',$request->restaurant_id);
            }
        })->has('restaurant')->with('restaurant')->latest()->paginate(20);
        return responseJson(1,'',$offers);
    }
    public function offer(Request $request)
    {
        $offer = Offer::with('restaurant')->find($request->offer_id);
        if (!$offer)
        {
            return responseJson(0,'no data');
        }
        return responseJson(1,'',$offer);
    }

    
    public function contact(Request $request)
    {
        $validation = validator()->make($request->all(), [
            'type' => 'required|in:complaint,suggestion,inquiry',
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'content' => 'required'
        ]);
        if ($validation->fails()) {
            $data = $validation->errors();
            return responseJson(0,$validation->errors()->first(),$data);
        }
        Contact::create($request->all());
        return responseJson(1,'تم الارسال بنجاح');
    }

    
}
