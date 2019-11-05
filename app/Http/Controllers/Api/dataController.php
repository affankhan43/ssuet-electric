<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Stat;
use App\Wallet;
use App\Transaction;
use App\Rate;
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
	private $rates;
	
	public function __construct(User $user, JWTAuth $jwtauth, Stat $stats, Wallet $wallets, Transaction $transactions, Rate $rates){
		$this->user = $user;
		$this->jwtauth = $jwtauth;
		$this->stats = $stats;
		$this->wallets = $wallets;
		$this->transactions = $transactions;
		$this->rates = $rates;
	}

	public function Stats(Request $request){
		$user = $this->jwtauth->parseToken()->authenticate();
		if($user){
			$stats = $this->stats->where('period','current')->get();
			if($stats != '[]'){
				$data = array();
				foreach ($stats as $stat_value) {
					if(isset($stat_value['updated_at'])){
						$data['LastUpdated'] = strtotime($stat_value['updated_at']);
					}
					if($stat_value['category'] == 'request_1'){
						$request1 = json_decode($stat_value['stats'],true);
						$data['Voltage'] = $request1['Voltage'];
						$data['MainPower'] = $request1['MainPower'];
						$data['GridTiePower'] = $request1['GridTiePower'];
						if($user->id == 1){ $data['LoadPower'] = $request1['LoadPower1'];}
						elseif($user->id == 2){ $data['LoadPower'] = $request1['LoadPower2'];}
					}
				}
				return response()->json(['success'=>true,'stats'=>$data]);
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
		$stats = $this->stats->where(['category'=>'request_1','period'=>'current'])->first();
		if($stats){
			$update = $stats->update(['stats'=>$request->stats]);
			//return response()->json(['success'=>true,'message'=>'Updated Successfully']);
		}else{
			$create = $this->stats->create([
				"stats"=>$request->stats,
				"category"=>"request_1",
				"period"=>"current"
			]);
			//return response()->json(['success'=>true,'message'=>'Stats Created']);
		}
	}

	public function updateStatsPower(Request $request){
		$request->validate(['stats'=>'required']);
		date_default_timezone_set("Asia/Karachi");
		$stats = $this->stats->where(['category'=>'request_2','period'=>'current'])->first();
		if($stats){
			$update = $stats->update(['stats'=>$request->stats]);
			//return response()->json(['success'=>true,'message'=>'Updated Successfully']);
		}else{
			$create = $this->stats->create([
				"stats"=>$request->stats,
				"category"=>"request_2",
				"period"=>"current"
			]);
			//return response()->json(['success'=>true,'message'=>'Stats Created']);
		}
	}

	public function updateStatsUnits(Request $request){
		$request->validate(['stats'=>'required']);
		date_default_timezone_set("Asia/Karachi");
		$stats = $this->stats->where(['category'=>'request_3','period'=>'current'])->first();
		if($stats){
			$update = $stats->update(['stats'=>$request->stats]);
			//return response()->json(['success'=>true,'message'=>'Updated Successfully']);
		}else{
			$create = $this->stats->create([
				"stats"=>$request->stats,
				"category"=>"request_3",
				"period"=>"current"
			]);
			//return response()->json(['success'=>true,'message'=>'Stats Created']);
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

	public function updateRate(Request $request){
		$user = $this->jwtauth->parseToken()->authenticate();
		if($user){
			$request->validate(['rate'=>'required|numeric']);
			$c_rates = $this->rates->where('user_id',$user->id)->first();
			if($c_rates){
				$update = $c_rates->update(['rate'=>$request->rate]);
				return response()->json(['success'=>true,'message'=>'Rate Updated Successfully']);
			}else{
				$create = $this->rates->create(['user_id'=>$user->id,'rate'=>$request->rate]);
				return response()->json(['success'=>true,'message'=>'Rate Created Successfully']);
			}
		}
		else{
			return response()->json(['success'=>false,'message'=>"invalid_user_make_logout"],401);
		}
	}

	public function getRates(Request $request){
		$user = $this->jwtauth->parseToken()->authenticate();
		if($user){
			$get_rate = $this->rates->where('user_id',$user->id)->first();
			if($get_rate){
				return response()->json(['success'=>true,'rate'=>$get_rate->rate]);
			}else{
				$create = $this->rates->create(['user_id'=>$user->id,'rate'=>10]);
				return response()->json(['success'=>true,'rate'=>$create->rate]);
			}
		}
		else{
			return response()->json(['success'=>false,'message'=>"invalid_user_make_logout"],401);
		}
	}
}
