<?php

namespace App\Http\Controllers\Api\Client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Token;
use App\Client;
use Validator;
use Response;
use Auth;
use Mail;


class AuthController extends Controller
{
    public function addItemToCart(Request $request)
    {
        $validation = validator()->make($request->all(), [
            'item_id'  => 'required|exists:items,id',
            'quantity' => 'required',
        ]);
        if ($validation->fails()) {
            $data = $validation->errors();
            return responseJson(0, $validation->errors()->first(), $data);
        }
        
        $item = Item::find($request->item_id);

        $readyItem = [
            $item->id => [
                'quantity' => $request->quantity,
                'price'    => $item->price,
                'note'     => $request->note
            ]
        ];

        $request->user()->cart()->attach($readyItem);

        return responseJson(1, 'تم الاضافة');
    }
    public function register(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|confirmed'
        ]);
        if ($validation->fails()) {
            return Response::json([
                'data' => [
                    'status' => 0,
                    'msg' => 'برجاء ملئ جميع الحقول',
                    'errors' => $validation->errors()
                ]
            ], 200);
        }
        $userToken = str_random(60);
        $request->merge(array('api_token' => $userToken));
        $request->merge(array('password' => bcrypt($request->password)));
        $user = Client::create($request->all());
        if ($user) {
            $data = [
                'data' => [
                    'status' => 1,
                    'api_token' => $userToken,
                    'msg' => 'تم التسجيل بنجاح',
                    'data' => $user
                ]
            ];
            return Response::json($data, 200);
        } else {
            return Response::json([
                'data' => [
                    'status' => 0,
                    'message' => 'حدث خطأ ، حاول مرة أخرى',
                ]
            ], 200);
        }
    }
    /**
     * @param Request $request
     * @return mixed
     */
    public function profile(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'password' => 'confirmed',
        ]);
        if ($validation->fails()) {
            return Response::json([
                'data' => [
                    'status' => 0,
                    'msg' => 'برجاء ملئ جميع الحقول',
                    'errors' => $validation->errors()
                ]
            ], 200);
        }
        if ($request->has('name')) {
            Auth::guard('api')->user()->update($request->only('name'));
        }
        if ($request->has('email')) {
            Auth::guard('api')->user()->update($request->only('email'));
        }
        if ($request->has('password')) {
            $request->merge(array('password' => bcrypt($request->password)));
            Auth::guard('api')->user()->update($request->only('password'));
        }
        if ($request->has('phone')) {
            $phoneString = $request->phone;
            $phoneString = str_replace('+','00',$phoneString);
            $phoneToArray = str_split($phoneString);
            if ($phoneToArray[0].$phoneToArray[1] != '00')
            {
                $phoneString = '00'.$phoneString;
            }
            $request->merge(['phone' => $phoneString]);
            Auth::guard('api')->user()->update($request->only('phone'));
        }
        if ($request->has('city_id')) {
            Auth::guard('api')->user()->update($request->only('city_id'));
        }
        if ($request->has('date_of_birth')) {
            Auth::guard('api')->user()->update($request->only('date_of_birth'));
        }
        if ($request->has('gender')) {
            Auth::guard('api')->user()->update($request->only('gender'));
        }
        $data = [
            'data' => [
                'status' => 1,
                'msg' => 'تم تحديث البيانات',
                'data' => $request->user()
            ]
        ];
        return Response::json($data, 200);
    }
    
    /**
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);
        if ($validation->fails()) {
            return Response::json([
                'data' => [
                    'status' => 0,
                    'msg' => 'برجاء ملئ جميع الحقول',
                    'errors' => $validation->errors()
                ]
            ], 200);
        }
        $validUser = Auth::validate($request->all());
        if ($validUser) {
            $client = Client::where('email', $request->input('email'))->first();
            $data = [
                'data' => [
                    'status' => 1,
                    'user' => $client,
                    'msg' => 'Login Succsed'
                ]
            ];
            return Response::json($data, 200);
        } else {
            return Response::json([
                'data' => [
                    'status' => 0,
                    'msg' => 'Check Your info and try agian',
                ]
            ], 200);
        }
    }
    /**
     * @param Request $request
     * @return mixed
     */
    public function reset(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required'
        ]);
        if ($validation->fails()) {
            return Response::json([
                'data' => [
                    'status' => 0,
                    'msg' => 'البريد الالكتروني مطلوب',
                    'errors' => $validation->errors()
                ]
            ], 200);
        }
        $user = Client::where('email',$request->email)->first();
        if ($user){
            $code = rand(1111,9999);
            $update = $user->update(['code' => $code]);
            return Response::json($user, 200);
            // sending email
            if ($update)
            {
                // send email
                Mail::send('emails.reset', ['code' => $code], function ($mail) use($user) {
                    $mail->from('no-reply@aklatcontrol.com', 'تطبيق أكلات');
                    $mail->to($user->email, $user->name)->subject('إعادة تعيين كلمة المرور');
                });
                $data = [
                    'data' => [
                        'status' => 1,
                        'msg' => 'برجاء فحص بريدك الالكتروني'
                    ]
                ];
                return Response::json($data, 200);
            }else{
                return Response::json([
                    'data' => [
                        'status' => 0,
                        'message' => 'حدث خطأ ، حاول مرة أخرى',
                    ]
                ], 200);
            }
        }else{
            return Response::json([
                'data' => [
                    'status' => 0,
                    'message' => 'لا يوجد أي حساب مرتبط بهذا البريد الالكتروني',
                ]
            ], 200);
        }
    }
    /**
     * @param Request $request
     * @return mixed
     */
    public function password(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'code' => 'required',
            'password' => 'confirmed'
        ]);
        if ($validation->fails()) {
            return Response::json([
                'data' => [
                    'status' => 0,
                    'msg' => 'برجاء ملئ جميع الحقول',
                    'errors' => $validation->errors()
                ]
            ], 200);
        }
        $user = Client::where('code',$request->code)->where('code','!=',0)->first();
        if ($user)
        {
            $update = $user->update(['password' => $request->password]);
            if ($update)
            {
                $data = [
                    'data' => [
                        'status' => 1,
                        'msg' => 'تم تغيير كلمة المرور بنجاح'
                    ]
                ];
                return Response::json($data, 200);
            }else{
                return Response::json([
                    'data' => [
                        'status' => 0,
                        'message' => 'حدث خطأ ، حاول مرة أخرى',
                    ]
                ], 200);
            }
        }else{
            return Response::json([
                'data' => [
                    'status' => 0,
                    'message' => 'هذا الكود غير صالح',
                ]
            ], 200);
        }
    }
    public function registerToken(Request $request)
    {
        $validation = validator()->make($request->all(), [
            'type' => 'required|in:android,ios',
            'token' => 'required',
        ]);
        if ($validation->fails()) {
            return response()->json([
                'data' => [
                    'status' => 0,
                    'msg' => 'برجاء ملئ جميع الحقول',
                    'errors' => $validation->errors()
                ]
            ], 200);
        }
        Token::where('token',$request->token)->delete();
       
        auth()->user()->tokens()->create($request->all());
        
        $data = [
            'status' => 1,
            'msg' => 'تم التسجيل بنجاح',
        ];
        return response()->json($data);
    }
    public function removeToken(Request $request)
    {
        $validation = validator()->make($request->all(), [
            'token' => 'required',
        ]);
        if ($validation->fails()) {
            return response()->json([
                'data' => [
                    'status' => 0,
                    'msg' => 'برجاء ملئ جميع الحقول',
                    'errors' => $validation->errors()
                ]
            ], 200);
        }
        Token::where('token',$request->token)->delete();
        $data = [
            'status' => 1,
            'msg' => 'تم  الحذف بنجاح بنجاح',
        ];
        return response()->json($data);
    }
}