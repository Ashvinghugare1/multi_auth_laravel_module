<?php
namespace App\Http\Controllers\API;

use Auth;
use App\Models\User;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(),
        [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password'
        ]);
        if($validator->falls()){
            $response = [
                'success' => false,
                'message' =>$validator->error()
            ];
            return response()->json($response, 400); 
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        $success['name']  = $user->name;
        $response = [
            'success' => true,
            'data' => $success,
            'message' => 'User registration successfully'
        ];
        return response()->json($response, 200);
    }


    public function login(Request $request){
        if(Auth::attempt(['email' => $request->email, 'password'=> $request->password])){
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->plainTextToken;
            $success['name']  = $user->name;
            $response = [
            'success' => true,
            'data' => $success,
            'message' => 'User login successfully'
        ];
        return response()->json($response, 200);
        }else{
            $response = [
                'success' => false,
                'message' => 'unauthorized'
            ];
            return response()->json($response);
        }
    }
}
