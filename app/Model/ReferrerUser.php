<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ReferrerUser extends Model {
	protected $table = "user_referrers";

	public function user() {
		return $this->belongsTo(User::class, 'user_id', 'id');
	}

	public function referrer() {
		return $this->belongsTo(User::class, 'referrer_id', 'id');
	}
}
