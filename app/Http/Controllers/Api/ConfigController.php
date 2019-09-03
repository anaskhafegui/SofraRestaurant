<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Notification;

use App\Http\Controllers\Controller;
use App\Config;

class NotificationController extends Controller
{
    public function config(){

          $config = Config::all();

          return responsejson(1,'تم الاضافة للطلب بنجاح',$config);

    }
   
    public function contactus(){

            $validator = validator()->make($request->all(), [
                'name' => 'required',
                'cemail' => 'required',
                'cphone' => 'required:digits',
                'title'  => 'required',
                'content' => 'required',
            ]);
            if ($validator->fails()) {
                return JsonResponse(0, $validator->errors()->first(), $validator->errors());
            }
            $contact = Setting::create($request->all());
            return responsejson(1, 'successfully', $contact);
        
    }
       
    
}
