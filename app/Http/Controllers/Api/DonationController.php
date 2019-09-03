<?php

namespace App\Http\Controllers\Api;

use App\Donation;
use App\Governorate;
use App\BloodType;
use App\Notification;
use App\Client;
use App\Token;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DonationController extends Controller
{

                public function r_donate()
                {

                    $donate = Donation::paginate(10);
            
                    return responsejson(1, 'success', $donate);
                }

                public function showdonate(Donation $id)
                {
                    return responsejson(1, 'success', $id);
                }

                public function phonecall(Request $request,Donation $id)
                {
                    $call=$id->contact;

                    return responsejson(1, 'success', $call );
                }

                public function filterblood(Request $request){

                $bloodtype = Blood_type::where(function ($query) use ($request) {
                if ($request->has('id')) {$query->where('id',$request->id); }
                })->get();


                if($bloodtype){return responsejson(1, 'success', $bloodtype);}

                else{
                    return responsejson(0, 'No Requests on this BloodType');
                }
                    

                        }

                        public function filtergovern(Request $request){

                            $governotre = Governorate::where(function ($query) use ($request) {
                            if ($request->has('id')) {$query->where('id',$request->id); }
                            })->get();


                            if($governotre){return responsejson(1, 'success', $governotre);}

                            else{
                                return responsejson(0, 'No Requests on this Governotre');
                            }   
                        }
    
         public function createrequest(Request $request){
           
            $validator = validator()->make($request->all(), [
                'name'=>'required',
                'contact'=>'required',
                'hospital-name'=>'required',
                'address'=>'required',
                'blood_types_id' => 'required',
                'age' =>'required',
                'notes'=>'required',
                'longitude'=>'required',
                'latitude'=>'required',
                'nbags'=>'required',
                'city_id'=>'required']);

            if ($validator->fails()) {
                return responsejson(0, $validator->errors()->first(), $validator->errors());
            }

            $dontationRequest = $request->user()->donations()->create($request->all());

            $clientsIds = $dontationRequest->city->governorate->clients()
            ->whereHas('blood_types', function ($q) use ($request) {
                    $q->where('blood_types.id', $request->blood_types_id);
                })->pluck('clients.id')->toArray();

            if (count($clientsIds)) {
                $notification = $dontationRequest->notification()->create([
                    'title' => 'احتاج متبرع لفصيلة ',
                    'content' => $dontationRequest->blood_types . 'محتاج متبرع لفصيلة',
                ]);


            $notification->clients()->attach($clientsIds);


            $tokens = Token::whereIn('client_id', $clientsIds)->where('token', '!=', null)->pluck('token')->toArray();

            

                if (count($tokens)) {
                    $title = $notification->title;
                    $content = $notification->content;
                    $data = [
                        'donation_id' => $dontationRequest->id,
                    ];
                    $send = notifyByFirebase($title, $content, $tokens, $data);
                
                }
                return responsejson(1,'تم الاضافة للطلب بنجاح',$send);
            } 
            
            else {
                responsejson(1, 'not user found');
            }
}

    
    public function show($id) {

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
        $notifications = $request->user()->notifications()->where('read_statue','=',0)->get();
        return $notifications->count();
    }



}

        
        //return responsejson(1,'تم الاضافة للطلب بنجاح',$requests);   

          /* if($request->user()->blood_types_id == $requests->blood_types_id){

          dd("yes the the same");  }*/

       
         // get notification settings

         
         
         
         
          /*$n= Notification::all();
          $c= Client::find($request->user()->id);
          
          for ($i=0; $i <1 ; $i++) { 
            dd($n[$i]->donation->blood_types_id);
          }

         foreach ($c->blood_types as $p){

              
                for ($i=0; $i <count($n); $i++) { 
                if($p->pivot->blood_type_id === $n[$i]->donation->blood_types_id XOR $request->user()->blood_types_id === $n[$i]->donation->blood_types_id){

                    
                    $n[$i]->clients()->attach($request->user()->id);
                    
                }
        }}
        if($request->user()->blood_types_id == $n->donation->blood_types_id OR $request->user()->blood_types_id == $n->donation->blood_types_id){
              dd('success');
          return responsejson(1, 'we found');}  $n->clients()->attach($request->user()->id);*/

         
      
