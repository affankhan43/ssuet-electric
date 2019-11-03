<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator, DB, Hash;
use Illuminate\Support\Facades\Password;

class dataController extends Controller
{
    //
    private $user;
	private $jwtauth;
	
	public function __construct(User $user, JWTAuth $jwtauth){
		$this->user = $user;
		$this->jwtauth = $jwtauth;
	}
}
