<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Stat;
use App\Wallet;
use App\Transaction;
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
	private $wallets;
	private $transactions;
	
	public function __construct(User $user, JWTAuth $jwtauth, Stat $stats, Wallet $wallets, Transaction $transactions){
		$this->user = $user;
		$this->jwtauth = $jwtauth;
		$this->stats = $stats;
		$this->$wallets = $wallets;
		$this->$transactions = $transactions;
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
			return response()->json(['success'=>false,'message'=>"invalid_user_make_logout"],401);
		}
	}

	public function updateStats(Request $request){
		$request->validate(['stats'=>'required']);
		date_default_timezone_set("Asia/Karachi");
		$stats = $this->stats->first();
		if($stats){
			$update = $stats->update(['stats'=>$request->stats]);
			return response()->json(['success'=>true,'message'=>'Updated Successfully']);
		}else{
			$create = $this->stats->create([
				"stats"=>$request->stats
			]);
			return response()->json(['success'=>true,'message'=>'Stats Created']);
		}
	}

	public function fetchWallet(Request $request){
		$user = $this->jwtauth->parseToken()->authenticate();
		if($user){
			//print_r($user);
			$wallet_data = Wallet::where(['user_id'=>2])->with('transactions')->first();
			if($wallet_data){
				$walletShow  = ['address'=>$wallet_data->address,'balance'=>$wallet_data->balance];
				return response()->json(['success'=>true,'wallet'=>$walletShow,'transactions'=>$wallet_data->transactions]);
			}
			else{
				return response()->json(['success'=>false,'message'=>'Wallet Not Found!!']);
			}
		}else{
			return response()->json(['success'=>false,'message'=>"invalid_user_make_logout"],401);
		}
	}
}
