<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model {
	protected $table = 't_notifications';

	protected $fillable = ['is_read'];
	
	public function sender() {
		return $this->belongsTo(User::class, "sender_id");
	}

	public function receiver() {
		return $this->belongsTo(User::class, "receiver_id");
	}

	public function fetchNotifications($request, $columns) {
		Notification::where('is_read', '0')->update(['is_read' => '1']);
		$query = Notification::where('status', '!=', 'DL');

		if (isset($request->from_date)) {
			$query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
		}
		if (isset($request->end_date)) {
			$query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
		}

		if (isset($request->search['value'])) {
			$query->where(function ($q) use ($request) {
				$q->where('title', 'like', '%' . $request->search . '%');
				$q->orWhere('description', 'like', '%' . $request->search . '%');
				$q->orWhere('notification_type', 'like', '%' . $request->search . '%');
				$q->orWhereHas('sender', function ($qu) use ($request) {
					$qu->whereRaw('CONCAT(first_name," ",last_name) like ?', ["%" . $request->search . "%"]);
				});
				$q->orWhereHas('receiver', function ($qu) use ($request) {
					$qu->whereRaw('CONCAT(first_name," ",last_name) like ?', ["%" . $request->search . "%"]);
				});
			});
		}
		if (isset($request->status)) {
			$query->where('status', $request->status);
		}
		if (isset($request->notification_type)) {
			$query->where('notification_type', $request->notification_type);
		}
		if (isset($request->order_column)) {
			$plans = $query->orderBy($columns[$request->order_column], $request->order_dir);
		} else {
			$plans = $query->orderBy('created_at', 'desc');
		}
		return $plans;
	}
}
