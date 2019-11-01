<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
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
		$credentials = $request->only('name', 'email', 'password','grid_name');
		$rules = [
			'name' => 'required|max:255',
			'email' => 'required|email|max:255|unique:users',
			'password'=>'required|min:8',
			'grid_name'=>'required'
		];
		$validator = Validator::make($credentials, $rules);
		if($validator->fails()) {
			return response()->json(['success'=> false, 'error'=> $validator->messages()]);
		}
		$user = $this->user->create(['name'=>$request->name,'email'=>$request->email,'password'=>Hash::make($request->password),'grid_name'=>$request->grid_name]);
		return response()->json(['success'=> true, 'message'=> 'Thanks for signing up!']);
	}

	public function login(Request $request){
		// get user credentials: email, password
		$credentials = $request->only('email', 'password');
		$rules = [
			'email' => 'required|email|max:255',
			'password'=>'required',
		];
		$validator = Validator::make($credentials, $rules);
		if($validator->fails()) {
			return response()->json(['success'=> false, 'error'=> $validator->messages()]);
		}
		$token = null;
		try{
			$token = $this->jwtauth->attempt($credentials);
			if(!$token){
				return response()->json(['success'=> false, 'message'=>'Invalid Email Password']);
			}
		}
		catch (JWTAuthException $e) {
			return response()->json(['success'=>false,'message'=>'Failed To Signin']);
		}
		$user = $this->jwtauth->setToken($token)->toUser();
		return response()->json(['success'=>true,'id'=>$user->id,'email'=>$user->email,'name'=>$user->name,'token'=>$token]);
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
