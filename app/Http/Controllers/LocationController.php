<?php

namespace App\Http\Controllers;
use App\Library\Helper;
use App\Model\City;
use App\Model\Country;
use App\Model\Location;
use App\Model\State;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LocationController extends Controller {
	public $area;
	public $columns;
	public $city;
	public $state;

	public function __construct() {
		$this->area = new Location;
		$this->city = Session::get('globalCity');
		$this->state = Session::get('globalState');
		$this->columns = [
			"select", "area_code", "area", "city", "status", "activate", "action", "sno",
		];
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		return view('areas.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function areaAjax(Request $request) {
		$request->search = $request->search['value'];
		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column'];
			$request->order_dir = $request->order[0]['dir'];
		}
		$records = $this->area->fetchLocations($this->city, $this->state, $request, $this->columns);
		$total = $records->get();
		if (isset($request->start)) {
			$areas = $records->offset($request->start)->limit($request->length)->get();
		} else {
			$areas = $records->offset($request->start)->limit(count($total))->get();
		}
		// echo $total;
		$result = [];
		$i = $request->start + 1;
		foreach ($areas as $area) {
			$data = [];
			$data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_id[]" value="' . $area->id . '"><i class="input-helper"></i></label></div>';
			$data['area_code'] = $area->area_code;
			$data['area'] = $area->area;
			$data['city'] = $area->city->name;
			$data['status'] = ucfirst(config('constants.STATUS.' . $area->status));

			if (Helper::checkAccess(route('changeStatusArea'))) {
				$data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($area->status == 'AC' ? ' checked' : '') . ' data-id="' . $area->id . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusArea"></div></div>';
			} else {
				$data['activate'] = ucfirst(config('constants.STATUS.' . $area->status));
			}
			$action = '';

			if (Helper::checkAccess(route('editArea'))) {
				$action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('editArea', ['id' => $area->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
			}
			// if (Helper::checkAccess(route('viewArea'))) {
			// 	$action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('viewArea', ['id' => $area->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';
			// }
			$data['action'] = $action;
			$data['sno'] = $i++;

			$result[] = $data;
		}
		$data = json_encode([
			'data' => $result,
			'recordsTotal' => count($total),
			'recordsFiltered' => count($total),
		]);
		// &nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" class="toolTip deleteUser" data-code="{{$user->name}}" data-id="{{$user->id}}" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a></td>
		echo $data;

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		$type = 'add';
		$url = route('addArea');
		$area = new Location;
		$states = $cities = [];

		if (Session::get('globalCountry') != 'all') {
			$states = State::whereHas('country', function ($q) {
				$q->where('g_countries.name', Session::get('globalCountry'));
			})->pluck('name', 'id');
		}

		if (Session::get('globalState') != 'all') {
			$cities = City::whereHas('state', function ($q) {
				$q->where('g_states.name', Session::get('globalState'));
			})->pluck('name', 'id');
		}

		return view('areas.create', compact('type', 'url', 'area', 'states', 'cities'));
	}

	/**
	 * check for unique area within a city
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function checkArea(Request $request, $id = null) {
		if (isset($request->area)) {
			$check = Location::where('area', $request->area);
			if (isset($id) && $id != null) {
				$check = $check->where('id', '!=', $id);
			}
			if (isset($request->city) && $request->city != null) {
				$check = $check->whereHas('city', function ($q) use ($request) {
					$q->where('g_cities.name', $request->city);
				});
			}
			$check = $check->where('status', '!=', 'DL')->count();
			if ($check > 0) {
				return "false";
			} else {
				return "true";
			}

		} else {
			return "true";
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$validate = Validator($request->all(), [
			'area' => 'required',
			'city' => 'required',

		]);

		$attr = [
			'area' => 'Area',
			'city' => 'City',
		];

		$validate->setAttributeNames($attr);

		if ($validate->fails()) {
			return redirect()->route('createArea')->withInput($request->all())->withErrors($validate);
		} else {
			try {
				$area = new Location;
				$area->area_code = Helper::generateNumber('c_areas', 'area_code');
				$area->area = $request->post('area');
				$area->city_id = City::where('name', $request->post('city'))->whereHas('state', function ($q) use ($request) {
					$q->where('g_states.name', $request->state);
				})->first()->id;
				$area->status = trim($request->post('status'));
				$area->created_at = date('Y-m-d H:i:s');

				if ($area->save()) {
					$request->session()->flash('success', 'Area added successfully');
					return redirect()->route('areas');
				} else {
					$request->session()->flash('error', 'Something went wrong. Please try again later.');
					return redirect()->route('areas');
				}
			} catch (Exception $e) {
				$request->session()->flash('error', 'Something went wrong. Please try again later.');
				return redirect()->route('areas');
			}

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
			$area = Location::where('id', $id)->first();
			if (isset($area->id)) {
				$type = 'edit';

				$states = Country::getStates(Session::get('globalCountry'));

				$cities = State::getCities($area->city->state->name);

				$url = route('updateArea', ['id' => $area->id]);
				return view('areas.create', compact('area', 'type', 'url', 'states', 'cities'));

			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('areas');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('areas');
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
			$area = Location::where('id', $id)->first();
			if (isset($area->id)) {
				$validate = Validator($request->all(), [
					'area' => 'required',

				]);

				$attr = [
					'area' => 'Area',
				];

				$validate->setAttributeNames($attr);

				if ($validate->fails()) {
					return redirect()->route('editArea', ['id' => $area->id])->withInput($request->all())->withErrors($validate);
				} else {
					try {
						$area->area = $request->post('area');
						$area->status = trim($request->post('status'));

						if ($area->save()) {
							$request->session()->flash('success', 'Area updated successfully');
							return redirect()->route('areas');
						} else {
							$request->session()->flash('error', 'Something went wrong. Please try again later.');
							return redirect()->route('areas');
						}
					} catch (Exception $e) {
						$request->session()->flash('error', 'Something went wrong. Please try again later.');
						return redirect()->route('areas');
					}

				}
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('areas');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('areas');
		}

	}

	// activate/deactivate offer
	public function updateStatus(Request $request) {

		if (isset($request->statusid) && $request->statusid != null) {
			$area = Location::find($request->statusid);

			if (isset($area->id)) {
				$area->status = $request->status;
				if ($area->save()) {
					$request->session()->flash('success', 'Area updated successfully.');
					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Unable to update area. Please try again later.');
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

	// activate/deactivate offer
	public function updateStatusAjax(Request $request) {

		if (isset($request->statusid) && $request->statusid != null) {
			$area = Location::find($request->statusid);

			if (isset($area->id)) {
				$area->status = $request->status;
				if ($area->save()) {
					echo json_encode(['status' => 1, 'message' => 'Area updated successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to update area. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid Area']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid Area']);
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
				$area = Location::find($id);

				if (isset($area->id)) {
					if ($area->status == 'AC') {
						$area->status = 'IN';
					} elseif ($area->status == 'IN') {
						$area->status = 'AC';
					}

					if ($area->save()) {
						$count++;
					}
				}
			}
			if ($count == $ids) {
				echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Areas updated successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Not all areas were updated. Please try again later.']);
			}
		} else {
			echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
		}
	}
}
