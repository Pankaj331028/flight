<?php

namespace App\Http\Controllers;

use App\Library\Helper;
use App\Library\Notify;
use App\Model\ReferrerUser;
use App\Model\User;
use App\Model\Wallet;
use Auth;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use URL;

class UserController extends Controller {
	public $user;
	public $columns;
	public $currency;
	public $phonecode;

	public function __construct() {
		$this->user = new User;
		$this->currency = Notify::getBusRuleRef('currency');
		$this->phonecode = Notify::getBusRuleRef('phonecode');
		$this->columns = [
			"select", "profile_picture", "user_code", "first_name", "last_name", "email", "mobile_number", "google_signin", "facebook_signin", "referral_code", "wallet_amount", "is_verified", "device_type", "created_at", "status", "activate", "action",
		];
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		return view('users.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function userAjax(Request $request) {
		if (isset($request->search['value'])) {
			$request->search = $request->search['value'];
		}
		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column'];
			$request->order_dir = $request->order[0]['dir'];
		}
		$records = $this->user->fetchUsers($request, $this->columns);
		$total = $records->get();
		if (isset($request->start)) {
			$users = $records->offset($request->start)->limit($request->length)->get();
		} else {
			$users = $records->offset($request->start)->limit(count($total))->get();
		}
		// echo $total;
		$result = [];
		foreach ($users as $user) {
			$data = [];
			$data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_id[]" value="' . $user->id . '"><i class="input-helper"></i></label></div>';
			$data['profile_picture'] = ($user->profile_picture != null) ? (stripos($user->profile_picture, "http") !== false) ? $user->profile_picture : URL::asset('/uploads/profiles/' . $user->profile_picture) : '';
			$data['user_code'] = $user->user_code;
			$data['name'] = $user->first_name . " " . $user->last_name;
			$data['email'] = $user->email;
			$data['mobile_number'] = ($user->mobile_number != null) ? '+' . $this->phonecode . '-' . $user->mobile_number : '-';
			$data['referral_code'] = $user->referral_code;
			$data['wallet_amount'] = $this->currency . $user->wallet_amount;
			$data['device_type'] = $user->device_type;
			$data['created_at'] = date('d M Y', strtotime($user->created_at));
			$data['google_signin'] = ucfirst(config('constants.CONFIRM.' . $user->google_signin));
			$data['facebook_signin'] = ucfirst(config('constants.CONFIRM.' . $user->facebook_signin));
			$data['is_verified'] = ucfirst(config('constants.CONFIRM.' . $user->is_verified));
			$data['status'] = ucfirst(config('constants.STATUS.' . $user->status));
			$data['travel_access'] = ucfirst($user->travel_access);
			$data['flight_access'] = ucfirst($user->flight_access);
			$data['grocery_front_access'] = ucfirst($user->grocery_front_access);
			$data['travel_with_api_access'] = ucfirst($user->travel_with_api_access);
			$data['sample_api_access'] = ucfirst($user->sample_api_access);
			$data['database_access'] = ucfirst($user->database_access);
			$data['admin_panel_access'] = ucfirst($user->admin_panel_access);
			$data['mobile_app_access'] = ucfirst($user->mobile_app_access);
			if (Helper::checkAccess(route('changeStatusUser'))) {
				$data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($user->status == 'AC' ? ' checked' : '') . ' data-id="' . $user->id . '" data-code="' . $user->name . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusUser"></div></div>';
			} else {
				$data['activate'] = ucfirst(config('constants.STATUS.' . $user->status));
			}
			if (Helper::checkAccess(route('changeStatusUser'))) {
				$data['travel_access1'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($user->travel_access == 'yes' ? ' checked' : '') . ' data-id="' . $user->id . '" data-code="' . $user->name . '" data-on-color="success" data-off-color="info" data-on-text="Yes" data-off-text="No" data-size="mini" name="cverify" class="travelAccess"></div></div>';
			} else {
				$data['travel_access1'] = ucfirst($user->travel_access);
			}
			if (Helper::checkAccess(route('changeStatusUser'))) {
				$data['flight_access1'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($user->flight_access == 'yes' ? ' checked' : '') . ' data-id="' . $user->id . '" data-code="' . $user->name . '" data-on-color="success" data-off-color="info" data-on-text="Yes" data-off-text="No" data-size="mini" name="cverify" class="flightAccess"></div></div>';
			} else {
				$data['flight_access1'] = ucfirst($user->flight_access);
			}

			if (Helper::checkAccess(route('changeStatusUser'))) {
				$data['grocery_front_access1'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($user->grocery_front_access == 'yes' ? ' checked' : '') . ' data-id="' . $user->id . '" data-code="' . $user->name . '" data-on-color="success" data-off-color="info" data-on-text="Yes" data-off-text="No" data-size="mini" name="cverify" class="groceryFrontAccess"></div></div>';
			} else {
				$data['grocery_front_access1'] = ucfirst($user->grocery_front_access);
			}

			if (Helper::checkAccess(route('changeStatusUser'))) {
				$data['travel_with_api_access1'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($user->travel_with_api_access == 'yes' ? ' checked' : '') . ' data-id="' . $user->id . '" data-code="' . $user->name . '" data-on-color="success" data-off-color="info" data-on-text="Yes" data-off-text="No" data-size="mini" name="cverify" class="groceryFrontWithApiAccess"></div></div>';
			} else {
				$data['travel_with_api_access1'] = ucfirst($user->travel_with_api_access);
			}

			if (Helper::checkAccess(route('changeStatusUser'))) {
				$data['sample_api_access1'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($user->sample_api_access == 'yes' ? ' checked' : '') . ' data-id="' . $user->id . '" data-code="' . $user->name . '" data-on-color="success" data-off-color="info" data-on-text="Yes" data-off-text="No" data-size="mini" name="cverify" class="sampleApiAccess"></div></div>';
			} else {
				$data['sample_api_access1'] = ucfirst($user->sample_api_access);
			}

			if (Helper::checkAccess(route('changeStatusUser'))) {
				$data['database_access1'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($user->database_access == 'yes' ? ' checked' : '') . ' data-id="' . $user->id . '" data-code="' . $user->name . '" data-on-color="success" data-off-color="info" data-on-text="Yes" data-off-text="No" data-size="mini" name="cverify" class="databaseAccess"></div></div>';
			} else {
				$data['database_access1'] = ucfirst($user->database_access);
			}

			if (Helper::checkAccess(route('changeStatusUser'))) {
				$data['admin_panel_access1'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($user->admin_panel_access == 'yes' ? ' checked' : '') . ' data-id="' . $user->id . '" data-code="' . $user->name . '" data-on-color="success" data-off-color="info" data-on-text="Yes" data-off-text="No" data-size="mini" name="cverify" class="adminPanelAccess"></div></div>';
			} else {
				$data['admin_panel_access1'] = ucfirst($user->admin_panel_access);
			}

			if (Helper::checkAccess(route('changeStatusUser'))) {
				$data['mobile_app_access1'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($user->mobile_app_access == 'yes' ? ' checked' : '') . ' data-id="' . $user->id . '" data-code="' . $user->name . '" data-on-color="success" data-off-color="info" data-on-text="Yes" data-off-text="No" data-size="mini" name="cverify" class="mobileAppAccess"></div></div>';
			} else {
				$data['mobile_app_access1'] = ucfirst($user->mobile_app_access);
			}

			if (Helper::checkAccess(route('changeStatusUser'))) {
				$data['admin_verified'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($user->admin_verify == 1 ? ' checked' : '') . ' data-id="' . $user->id . '" data-code="' . $user->name . '" data-on-color="success" data-off-color="info" data-on-text="Yes" data-off-text="No" data-size="mini" name="cverify" class="verifyUser"></div></div>';
			} else {
				$data['admin_verified'] = ucfirst(config('constants.STATUS.' . $user->status));
			}

			$action = '';

			if (Helper::checkAccess(route('editUser'))) {
				$action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('editUser', ['id' => $user->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
			}

			if (Auth::guard('admin')->user()->user_role[0]->role == 'user' || Helper::checkAccess(route('viewUser'))) {
				$action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('viewUser', ['id' => $user->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';
			}

			if (Helper::checkAccess(route('deleteUser'))) {
				$action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('deleteUser', ['id' => $user->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>';
				// $action .= '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" class="toolTip deleteUser" data-code="{{$user->name}}" data-id="{{$user->id}}" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a></td>';
			}

			$data['action'] = $action;

			$result[] = $data;
		}
		$data = json_encode([
			'data' => $result,
			'recordsTotal' => count($total),
			'recordsFiltered' => count($total),
		]);
		echo $data;

	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $id = null) {
		if (isset($id) && $id != null) {
			$user = User::with(['orders' => function ($q) {
				$q->orderBy('created_at', 'desc');
			}, 'wallet' => function ($q) {
				$q->orderBy('created_at', 'desc');
			}, 'user_couponcode' => function ($q) {
				$q->orderBy('created_at', 'desc');
			}, 'user_addresses' => function ($q) {
				$q->orderBy('created_at', 'desc');
			}, 'rating' => function ($q) {
				$q->orderBy('created_at', 'desc');
			}, 'user_referrer' => function ($q) {
				$q->orderBy('created_at', 'desc');
			}, 'user_invites' => function ($q) {
				$q->orderBy('created_at', 'desc');
			}])->where('id', $id)->first();
			if (isset($user->id)) {
				$currency = $this->currency;
				$phonecode = $this->phonecode;
				return view('users.view', compact('user', 'currency', 'phonecode'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('users');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('users');
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $request, $id = null) {
		if (isset($id) && $id != null) {
			$user = User::where('id', $id)->first();
			if (isset($user->id)) {
				$type = 'edit';
				$url = route('updateUser', ['id' => $user->id]);
				return view('users.create', compact('user', 'type', 'url'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('users');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('users');
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id = null) {

		if (isset($id) && $id != null) {
			$user = User::where('id', $id)->first();
			if (isset($user->id)) {

				$validate = Validator($request->all(), [
					'mobile_number' => 'required|digits_between:8,15',
					'first_name' => 'required',
					'last_name' => 'required',
				]);

				$attr = [
					'mobile_number' => 'Contact Number',
					'first_name' => 'First Name',
					'last_name' => 'Last Name',
				];

				$validate->setAttributeNames($attr);

				if ($validate->fails()) {
					return redirect()->route('editUser', ['id' => $user->id])->withInput($request->all())->withErrors($validate);
				} else {
					try {

						$user->first_name = $request->post('first_name');
						$user->last_name = $request->post('last_name');
						$user->mobile_number = $request->post('mobile_number');
						$user->status = $request->post('status');
						$user->admin_verify = $request->post('verify');

						if ($user->save()) {
							// $user->assignRole($request->role);
							$request->session()->flash('success', 'User updated successfully');
							return redirect()->route('users');
						} else {
							$request->session()->flash('error', 'Something went wrong. Please try again later.');
							return redirect()->route('users');
						}
					} catch (Exception $e) {
						$request->session()->flash('error', 'Something went wrong. Please try again later.');
						return redirect()->route('users');
					}

				}
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('users');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('users');
		}

	}

	// activate/deactivate user
	public function updateStatus(Request $request) {

		if (isset($request->statusid) && $request->statusid != null) {
			$user = User::find($request->statusid);

			if (isset($user->id)) {
				$user->status = $request->status;
				$user->admin_verify = $request->verify;
				if ($user->save()) {
					$request->session()->flash('success', 'User updated successfully.');
					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Unable to update user. Please try again later.');
					return redirect()->back();
				}
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->back();
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->back();
		}

	}

	// activate/deactivate user
	public function updateStatusAjax(Request $request) {

		if (isset($request->statusid) && $request->statusid != null) {
			$user = User::find($request->statusid);

			if (isset($user->id)) {
				$user->status = $request->status;
				if ($user->save()) {
					echo json_encode(['status' => 1, 'message' => 'User updated successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to update user. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid User']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid User']);
		}

	}

	// activate/deactivate user
	public function updateVerifyStatusAjax(Request $request) {
		if (isset($request->statusid) && $request->statusid != null) {
			$user = User::find($request->statusid);

			if (isset($user->id)) {
				$user->admin_verify = $request->status;
				$user->api_key = Helper::generateApiKey();
				// $user->admin_verify = 1;
				if ($user->save()) {
					Notify::sendMail("emails.complete_verification_admin", $user->toArray(), "omrbranch - Account Approved");
					echo json_encode(['status' => 1, 'message' => 'Verification status Updated successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to user. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid User']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid User']);
		}

	}

	// update travel access user
	public function updateTravelAccessAjax(Request $request) {
		if (isset($request->statusid) && $request->statusid != null) {
			$user = User::find($request->statusid);

			if (isset($user->id)) {
				$user->travel_access = $request->status;
				if ($user->save()) {
					echo json_encode(['status' => 1, 'message' => 'Travel access status Updated successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to update. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid User']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid User']);
		}

	}

	// update flight access user
	public function updateFlightAccessAjax(Request $request) {
		if (isset($request->statusid) && $request->statusid != null) {
			$user = User::find($request->statusid);

			if (isset($user->id)) {
				$user->flight_access = $request->status;
				if ($user->save()) {
					echo json_encode(['status' => 1, 'message' => 'Flight access status Updated successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to update. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid User']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid User']);
		}

	}

	// update grocery access user
	public function updateGroceryAccessAjax(Request $request) {
		if (isset($request->statusid) && $request->statusid != null) {
			$user = User::find($request->statusid);

			if (isset($user->id)) {
				$user->grocery_front_access = $request->status;
				if ($user->save()) {
					echo json_encode(['status' => 1, 'message' => 'Grocery front access status Updated successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to update. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid User']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid User']);
		}

	}

	// update grocery with api access user
	public function updateGroceryWithApiAccessAjax(Request $request) {
		if (isset($request->statusid) && $request->statusid != null) {
			$user = User::find($request->statusid);

			if (isset($user->id)) {
				$user->travel_with_api_access = $request->status;
				if ($user->save()) {
					echo json_encode(['status' => 1, 'message' => 'Travel api access status Updated successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to update. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid User']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid User']);
		}

	}

	// update admin panel access user
	public function updateAdminPanelAccessAjax(Request $request) {
		if (isset($request->statusid) && $request->statusid != null) {
			$user = User::find($request->statusid);

			if (isset($user->id)) {
				$user->admin_panel_access = $request->status;
				if ($user->save()) {
					echo json_encode(['status' => 1, 'message' => 'Admin panel access status Updated successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to update. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid User']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid User']);
		}

	}

	// update mobile app access user
	public function updateMobileAppAccessAjax(Request $request) {
		if (isset($request->statusid) && $request->statusid != null) {
			$user = User::find($request->statusid);

			if (isset($user->id)) {
				$user->mobile_app_access = $request->status;
				if ($user->save()) {
					echo json_encode(['status' => 1, 'message' => 'Mobile App access status Updated successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to update. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid User']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid User']);
		}

	}

	// update database access user
	public function updateDatabaseAccessAjax(Request $request) {
		if (isset($request->statusid) && $request->statusid != null) {
			$user = User::find($request->statusid);

			if (isset($user->id)) {
				$user->database_access = $request->status;
				if ($user->save()) {
					echo json_encode(['status' => 1, 'message' => 'Database access status Updated successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to update. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid User']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid User']);
		}

	}

	// update sample api access user
	public function updateSampleApiAccessAjax(Request $request) {
		if (isset($request->statusid) && $request->statusid != null) {
			$user = User::find($request->statusid);

			if (isset($user->id)) {
				$user->sample_api_access = $request->status;
				if ($user->save()) {
					echo json_encode(['status' => 1, 'message' => 'Sample api access status Updated successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to update. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid User']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid User']);
		}

	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \Illuminate\Http\Request
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request, $id) {
		if (isset($request->deleteid) && $request->deleteid != null) {
			$user = User::find($request->deleteid);

			if (isset($user->id)) {
				$user->status = 'DL';
				if ($user->save()) {
					$request->session()->flash('success', 'User deleted successfully.');
					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Unable to delete user. Please try again later.');
					return redirect()->back();
				}
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->back();
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->back();
		}
	}

	public function delete(Request $request, $id) {
		$user = User::find($id);
		if (isset($user)) {
			if (isset($user->id)) {
				foreach ($user->tokens as $token) {
					$token->delete();
				}
				$user->status = 'DL';
				if ($user->save()) {
					$request->session()->flash('success', 'User deleted successfully.');
					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Unable to delete user. Please try again later.');
					return redirect()->back();
				}
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->back();
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->back();
		}
	}

	/**
	 * Remove multiple resource from storage.
	 *
	 * @param  \Illuminate\Http\Request
	 * @return \Illuminate\Http\Response
	 */
	public function bulkdelete(Request $request) {

		/*if (isset($request->ids) && $request->ids != null) {
				$ids = count($request->ids);
				$count = 0;
				foreach ($request->ids as $id) {
					$user = User::find($id);

					if (isset($user->id)) {
						$user->status = 'DL';
						if ($user->save()) {
							$count++;
						}
					}
				}
				if ($count == $ids) {
					echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Users deleted successfully.']);
				} else {
					echo json_encode(["status" => 0, 'message' => 'Not all users were deleted. Please try again later.']);
				}
			} else {
				echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
		*/

		if (User::whereHas('user_role', function ($query) use ($request) {
			$query->where('g_roles.role', 'user');
		})->where('status', '!=', 'DL')->update(['status' => 'DL'])) {
			$request->session()->flash('success', 'Users deleted successfully.');
			return redirect()->back();
		} else {
			$request->session()->flash('error', 'Something went wrong. Please try again later');
			return redirect()->back();
		}
	}
	/**
	 * activate/deactivate multiple resource from storage.
	 *
	 * @param  \Illuminate\Http\Request
	 * @return \Illuminate\Http\Response
	 */
	public function bulkchangeStatus(Request $request) {

		if (isset($request->ids) && $request->ids != null) {
			$ids = count($request->ids);
			$count = 0;
			foreach ($request->ids as $id) {
				$user = User::find($id);

				if (isset($user->id)) {
					if ($user->status == 'AC') {
						$user->status = 'IN';
					} elseif ($user->status == 'IN') {
						$user->status = 'AC';
					}

					if ($user->save()) {
						$count++;
					}
				}
			}
			if ($count == $ids) {
				echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Users updated successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Not all users were updated. Please try again later.']);
			}
		} else {
			echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
		}
	}

	//verify email address after registration once user clicks on the link within email
	public function verifyEmail(Request $request) {
		$email = base64_decode($request->get('id'));
		if ($user = User::where('email', $email)->whereStatus('AC')->first()) {
			if ($user->is_verified == 0) {
				$user->is_verified = 1;
				if ($user->save()) {
					$request->session()->flash('success', "Congratulations! your account has been verified. <br>Please wait for admin to verify.");
					Notify::sendMail("emails.complete_verification", $user->toArray(), "omrbranch - Successful Verification");

					// if user has signed up using referral code, then send notification to user and referrer
					$user_referrer = ReferrerUser::where('user_id', $user->id)->where('status', 'AC')->first();
					$currency = Notify::getBusRuleRef('currency');
					if ($user_referrer) {
						$prntUser = User::find($user_referrer->referrer_id);

						// add amount to wallet of refrerred user
						$wallet = new Wallet();
						$wallet->user_id = $user->id;
						$wallet->referrer_id = $user_referrer->id;
						$wallet->amount = $user_referrer->amount;
						$wallet->type = 'credit';
						$wallet->description = 'User ' . $user->first_name . ' ' . $user->last_name . ' registered using referral code of ' . $prntUser->first_name . ' ' . $prntUser->last_name . ' and received amount of ' . $user_referrer->amount;
						$wallet->reason = "refer";
						$wallet->save();

						// add amount to wallet of referrer
						$wallet = new Wallet();
						$wallet->user_id = $prntUser->id;
						$wallet->referrer_id = $user_referrer->id;
						$wallet->amount = $user_referrer->amount;
						$wallet->type = 'credit';
						$wallet->description = 'Referral code of user ' . $prntUser->first_name . ' ' . $prntUser->last_name . ' was used by ' . $user->first_name . ' ' . $user->last_name . '  during registration and received amount of ' . $user_referrer->amount;
						$wallet->reason = "refer";
						$wallet->save();

						$user->wallet_amount = $user_referrer->amount;
						$user->save();

						$prntUser->wallet_amount = $prntUser->wallet_amount + $user_referrer->amount;
						$prntUser->save();

						$title = "Successful Signup";
						$description = "Congratulations! You have completed the signup process using referral code " . $user_referrer->referral_code . " and you have earned a credit amount of " . $currency . $user_referrer->amount;
						$type = 'referral';

						Notify::sendNotification($user->id, 1, $title, $description, $type);

						$title = "Successful Referral Signup";
						$description = "Congratulations! " . $user->first_name . " " . $user->last_name . " have completed the signup process using your referral code " . $user_referrer->referral_code . " and you have earned a credit amount of " . $currency . $user_referrer->amount;
						$type = 'referral';

						Notify::sendNotification($user_referrer->referrer_id, 1, $title, $description, $type);

					}

					return redirect()->route('appIndex');
				}
			} else {
				$request->session()->flash('error', "Your account is already verified.");
				return redirect()->route('appIndex');
			}

		} else {
			$request->session()->flash('error', "Email ID doesn't exists.");
			return redirect()->route('appIndex');
		}
	}

}
