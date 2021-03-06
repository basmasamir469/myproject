<?php

namespace App\Http\Controllers\user;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\traits\GeneralTraits;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use GeneralTraits;
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $user = User::create(array_merge(
            $validator->validated()
        ));

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }




    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' =>  60,
            'user' => auth()->user()
        ]);
    }
    public function index(Request $request){
        $token = $request -> header('auth-token');
        if($token) {
            $user_id = $request->user()->id;
            $data = User::find($user_id);
            return $this->returnData('200', $data, 'user profile');
        }
        else{
            $this -> returnError('','some thing went wrongs');
        }

    }

    public function login(Request $request)
    {

        try {
            $rules = [
                "email" => "required",
                "password" => "required",

            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            //login

            $credentials = $request->only(['email', 'password']);

            $token = Auth::guard('user-api')->attempt($credentials);

            if (!$token)
                return $this->returnError('E001', '???????????? ???????????? ?????? ??????????');

            $user = Auth::guard('user-api')->user();
            $user->api_token = $token;
            //return token
            return $this->returnData('user', $user);

        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }


    }
    public function edit(Request $request){
        $token = $request -> header('auth-token');
        if($token) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|between:2,100',
                'email' => 'required|string|email|max:100|unique:users,'. $request->user()->id,
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }
            $user_id = $request->user()->id;
            $user = User::find($user_id);

            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = $request->password;


            $user->update();


            return response()->json([
                'message' => 'User successfully updated',
                'user' => $user
            ], 201);
        } else{
            $this -> returnError('','some thing went wrongs');
        }


    }

    public function logout(Request $request)
    {
        $token = $request -> header('auth-token');
        if($token){
            try {
                JWTAuth::setToken($token)->invalidate(); //logout
            }catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
                return  $this -> returnError('','some thing went wrongs');
            }
            return $this->returnSuccessMessage('Logged out successfully');
        }else{
            $this -> returnError('','some thing went wrongs');
        }

    }

}
