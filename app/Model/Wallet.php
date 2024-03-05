<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model {
	protected $table = 'c_wallets';

	public function order() {
		return $this->belongsTo("App\Model\Order", "order_id");
	}

	public function order_return() {
		return $this->belongsTo("App\Model\OrderReturn", "order_return_id");
	}

	public function referrer() { 
		return $this->belongsTo("App\Model\ReferrerUser", "user_id", "user_id");
	}
}
