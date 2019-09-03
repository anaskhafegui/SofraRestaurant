<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\BloodType;
use App\Client;
use App\Favorite;
use App\Governorate;
use App\Notification;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Resources\DonationResource;
use App\Http\Resources\getTarget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\DB;


class NotificationController extends Controller
{

  public function select(Request $request){

    $request->user()->governorates()->sync($request->governorate_id);
    $request->user()->blood_types()->sync($request->blood_type_id);

    return responsejson(1,'تم الاضافة للطلب بنجاح');

    }


  public function getnotifications(Request $request){

        $govs       = $request->user()->governorates()->pluck('governorates.id')->toArray();
        $bloddTypes = $request->user()->blood_types()->pluck('blood_types.id')->toArray();

        return responsejson(1,'success');
      }

     public function count_unreading() {
        $notifications = Auth::user()->notifications()->where('read_statue','=',0)->get();
        return $notifications->count();
    }
    public function read($id) {
        $notification = Notification::findOrFail($id);
        if ($notification) {
            Auth::User()->notifications()->updateExistingPivot($notification,['read_statue'=>1]);
            return  new DonationResource($notification->donation);
        } else {
            return new Resource(['status' => 'غير موجود']);
        }
    }

   
}
   /*
   //get all the notfiication
   public function index() {
       $notifications =  Auth::user()->notifications()->get();
       return  NotificationResource::collection($notifications);
   }
   public function read($id) {
       $notification = Notification::find($id);
       if ($notification) {
           Auth::User()->notifications()->updateExistingPivot($notification,['read_statue'=>1]);
           return  new DonationResource($notification->donation);
       } else {
           return new Resource(['status' => 'غير موجود']);
       }
   }
   // get the count of unreading notification
   public function count_unreading() {
       $notifications = Auth::user()->notifications()->where('read_statue','=',0)->get();
       return $notifications->count();
   }
   //get the targets values
   public function getTarget(Request $request) {
       $govIds = $request->user()->governorate_target()->pluck('governorates.id')->toArray();
       $bloodIds = $request->user()->blood_type_target()->pluck('blood_types.id')->toArray();
       return responseJson(1,'الاشعارات المستهدفة',['govIds' => $govIds,'bloodIds' => $bloodIds]);
   }
   public function target(Request $request) {
       //delete all other favourite and add new favourite
       if (
       Auth::user()->blood_type_target()->sync($request->blood_type)
       &&
       Auth::user()->governorate_target()->sync($request->governorate)) {
           return responseJson(1,'تم تحديث القائمة المفضلة');
       };
   }
}
*/
