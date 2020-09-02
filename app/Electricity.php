<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Electricity extends Model {

    protected $table = 'electricity_transactions';

    protected $fillable = [
        'customer_name',
        'meter_number',
        'customer_address',
        'amount',
        'status',
        'phone',
        'request_id',
        'token',
        'bonus_token',
        'transaction_id',
        'user_id'
    ];
    
    public function user(){
        return $this->belongsTo(User::class);
    }
}