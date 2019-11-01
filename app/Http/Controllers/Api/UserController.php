<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    //
    private $user;

    function __construct(User $user){
    	$this->user = $user;
    }

    public function login(Request $request){
    	if($request->isMethod('post')){

    	}else{
    		return response()->json(['success'=>false,'message'=>'Invalid Request']);
    	}
    }
}
