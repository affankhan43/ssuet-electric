<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Stat;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator, DB, Hash;
use Illuminate\Support\Facades\Password;

class dataController extends Controller
{
    //
    private $user;
	private $jwtauth;
	private $stats;
	
	public function __construct(User $user, JWTAuth $jwtauth, Stat $stats){
		$this->user = $user;
		$this->jwtauth = $jwtauth;
		$this->stats = $stats;
	}

	public function Stats(Request $request){
		$user = $this->jwtauth->parseToken()->authenticate();
		if($user){
			$stats = $this->stats->first();
			if($stats){
				return response()->json(['success'=>true,'stats'=>$stats]);
			}
			else{
				return response()->json(['success'=>false,'message'=>'Stats Not Available! Connection Not Build']);
			}
		}
		else{
			return response()->json(['success'=>false,'message'=>"invalid_user_make_logout"]);
		}
	}

	public function updateStats(Request $request){
		$user = $this->jwtauth->parseToken()->authenticate();
		if($user){
			date_default_timezone_set("Asia/Karachi");
			$create = $this->stats->create([
				"stats"=>'[{"Voltage":25,"Current":56}]'
			]);
			return response()->json(['success'=>true,'data'=>$create]);
		}

	}
}
