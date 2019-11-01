<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Controllers\Controller;
use Validator, DB, Hash;
use Illuminate\Support\Facades\Password;

class UserController extends Controller
{
	//
	private $user;
	private $jwtauth;
	
	public function __construct(User $user, JWTAuth $jwtauth){
		$this->user = $user;
		$this->jwtauth = $jwtauth;
	}

	public function register(Request $request){
		$credentials = $request->only('name', 'email', 'password');
		$rules = [
			'name' => 'required|max:255',
			'email' => 'required|email|max:255|unique:users',
			'password'=>'required|min:8'
		];
		$validator = Validator::make($credentials, $rules);
		if($validator->fails()) {
			return response()->json(['success'=> false, 'error'=> $validator->messages()]);
		}
		$user = $this->user->create(['name'=>$request->name,'email'=>$request->email,'password'=>Hash::make($request->password)]);
		return response()->json(['success'=> true, 'message'=> 'Thanks for signing up!']);
	}

	public function login(LoginRequest $request){
		// get user credentials: email, password
		$credentials = $request->only('email', 'password');
		$token = null;
		try{
			$token = $this->jwtauth->attempt($credentials);
			if(!$token){
				return response()->json(['invalid_email_or_password'], 422);
			}
		}
		catch (JWTAuthException $e) {
			return response()->json(['failed_to_create_token'], 500);
		}
		$user = $this->jwtauth->setToken($token)->toUser();
		return response()->json(['id'=>$user->id,'email'=>$user->email,'name'=>$user->name,'token'=>$token]);
	}

	public function logout(Request $request) {
		$this->validate($request, ['token' => 'required']);
		try {
			$this->jwtauth->invalidate($request->input('token'));
			return response()->json(['success' => true, 'message'=> "You have successfully logged out."]);
		}
		catch (JWTException $e) {
			// something went wrong whilst attempting to encode the token
			return response()->json(['success' => false, 'error' => 'Failed to logout, please try again.'], 500);
		}
	}

}
