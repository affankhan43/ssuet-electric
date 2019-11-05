<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    //
    protected $table = 'wallets';
    protected $fillable = ['user_id','balance','address'];

    public function transactions()
    {
        return $this->hasMany('App\Transaction','wallet_id');
    }
}
