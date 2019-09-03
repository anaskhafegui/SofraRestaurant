<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Notification;

use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function select(Request $request){

        $request->user()->governorates()->sync($request->governorate_id);
        $request->user()->blood_types()->sync($request->blood_type_id);
    
        return responsejson(1,'تم الاضافة للطلب بنجاح');
    
        }
    
    
      public function listnotifications(Request $request){
    
            $govs       = $request->user()->governorates()->pluck('governorates.id')->toArray();
            $bloddTypes = $request->user()->blood_types()->pluck('blood_types.id')->toArray();
    
            return responsejson(1,'success',[
                'govs'       =>  $govs,
                'bloodtype' => $bloddTypes
            ]);
          }

      public function count_unreading(Request $request) {

            $notifications = $request->user()->notifications()->where('read_statue','=',0)->get();
            return responsejson(1,'تم الاضافة للطلب بنجاح', $notifications->count() );
        }


        public function read(Request $request,$id) {

                $notification = Notification::findOrFail($id);
                $request->user()->notifications()->updateExistingPivot($notification,['read_statue'=>1]);
                return responsejson(1,'تم قراءه الاشعار بنجاح');
        }
    
       
    
}
