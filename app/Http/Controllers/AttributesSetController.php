<?php

namespace App\Http\Controllers;
use App\Library\Helper;
use App\Model\AttributesSet;
use App\Model\Attribute;
use App\Library\Notify;
use App\Model\Brand;
use App\Model\AttributesSetValue;
use Config;
use DB;
use URL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AttributesSetController extends Controller {
	public $attributesset;
	public $columns;

	public function __construct() {
		$this->attributesset = new AttributesSet;
		$this->columns = [
			"select", "name", "status", "activate", "action",
		];
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		return view('attributes-sets.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function AttributesSetAjax(Request $request) {
		if(isset($request->search['value']))
        {
            $request->search = $request->search['value'];
        }
		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column'];
			$request->order_dir = $request->order[0]['dir'];
		}
		$records = $this->attributesset->fetchAttributesSet($request, $this->columns);
		$total = $records->get();
		if (isset($request->start)) {
			$attributessets = $records->offset($request->start)->limit($request->length)->get();
		} else {
			$attributessets = $records->offset($request->start)->limit(count($total))->get();
		}
		// echo $total;
		$result = [];
		foreach ($attributessets as $attributesset) {
			$data = [];
			$data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="attributesset_id[]" value="' . $attributesset->id . '"><i class="input-helper"></i></label></div>';
			$data['name'] = ucfirst($attributesset->name);

			$data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($attributesset->status == 'AC' ? ' checked' : '') . ' data-id="' . $attributesset->id . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusattributesset"></div></div>';
			
			$action = '';
			if (Helper::checkAccess(route('editStaff'))) {
				$action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('editAttributesSet', ['id' => $attributesset->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
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
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		$type = 'add';
		$url = route('addAttributesSet');
		$AttributesSetVariation = array();
		$attributesset = new AttributesSet;
		$roles = Attribute::select('id', 'name')->where('status','AC')->get();

		return view('attributes-sets.create', compact('type', 'url', 'attributesset', 'roles', 'AttributesSetVariation'));
	}

	/**
	 * check for unique email during adding AttributesSet
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function checkAttributesSet(Request $request, $id = null) {
		if (isset($request->title)) {
			$check = AttributesSet::where('title', $request->title);
			if (isset($id) && $id != null) {
				$check = $check->where('id', '!=', $id);
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
			'name' => 'required',

		]);

		$attr = [
			'name' => 'Name',
		];

		$validate->setAttributeNames($attr);

		if ($validate->fails()) {
			return redirect()->route('createAttributesSet')->withInput($request->all())->withErrors($validate);
		} else {
			try {
				$data = $request->all();
				$attributesset = new AttributesSet;
				$attributesset->name = $request->post('name');

				if ($attributesset->save()) {
					foreach ($request->post('attributes') as $key => $value) {
						//echo "<pre>"; print_r($value); die;
						$attributessetv = new AttributesSetValue;
						$attributessetv->attribute_set_id = $attributesset->id;
						$attributessetv->attribute_id = $value;
						//$attributessetv->status = 'AC';
						$attributessetv->created_at = date('Y-m-d H:i:s');
						//$attributessetv->created_at = date('Y-m-d H:i:s');
						$attributessetv->save();
					}
					$request->session()->flash('success', 'Attributes Set added successfully');
					return redirect()->route('attributes-sets');
				} else {
					$request->session()->flash('error', 'Something went wrong. Please try again later.');
					return redirect()->route('attributes-sets');
				}
			} catch (Exception $e) {
				$request->session()->flash('error', 'Something went wrong. Please try again later.');
				return redirect()->route('attributes-sets');
			}

		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $id = null) {
		if (isset($id) && $id != null) {
			$attributesset = AttributesSet::where('id', $id)->first();
			if (isset($attributesset->id)) {
				return view('attributes-sets.view', compact('attributesset'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('attributes-sets');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('attributes-sets');
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
			$attributesset = AttributesSet::where('id', $id)->first();
			if (isset($attributesset->id)) {
				$roles = Attribute::select('id', 'name')->where('status','AC')->get();
				$AttributesSetVariation = AttributesSetValue::where('attribute_set_id', $attributesset->id)->pluck('attribute_id')->toArray();
				$type = 'edit';
				$url = route('updateAttributesSet', ['id' => $attributesset->id]);
				return view('attributes-sets.create', compact('attributesset', 'type', 'url', 'roles', 'AttributesSetVariation'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('attributes-sets');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('attributes-sets');
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
			$attributesset = AttributesSet::where('id', $id)->first();
			if (isset($attributesset->id)) {

				$validate = Validator($request->all(), [
					'name' => 'required',

				]);

				$attr = [
					'name' => 'Name',
				];

				$validate->setAttributeNames($attr);

				if ($validate->fails()) {
					return redirect()->route('editAttributesSet', ['id' => $attributesset->id])->withInput($request->all())->withErrors($validate);
				} else {
					try {
						
						$attributesset->name = $request->post('name');
						$attributesset->type = $request->post('type');
						if ($attributesset->save()) {
							DB::table('attribute_attribute_set')->where('attribute_set_id', $id)->delete();
							foreach ($request->post('attributes') as $key => $value) {
								$attributessetv = new AttributesSetValue;
								$attributessetv->attribute_set_id = $attributesset->id;
								$attributessetv->attribute_id = $value;
								//$attributessetv->status = 'AC';
								$attributessetv->created_at = date('Y-m-d H:i:s');
								//$attributessetv->created_at = date('Y-m-d H:i:s');
								$attributessetv->save();
							}
							$request->session()->flash('success', 'AttributesSet updated successfully');
							return redirect()->route('attributes-sets');
						} else {
							$request->session()->flash('error', 'Something went wrong. Please try again later.');
							return redirect()->route('attributes-sets');
						}
					} catch (Exception $e) {
						$request->session()->flash('error', 'Something went wrong. Please try again later.');
						return redirect()->route('attributes-sets');
					}

				}
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('attributes-sets');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('attributes-sets');
		}

	}

	// activate/deactivate Attribute
	public function updateStatus(Request $request) {

		if (isset($request->statusid) && $request->statusid != null) {
			$attributesset = AttributesSet::find($request->statusid);

			if (isset($attributesset->id)) {
				$attributesset->status = $request->status;
				if ($attributesset->save()) {
					$request->session()->flash('success', 'AttributesSet updated successfully.');
					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Unable to update AttributesSet. Please try again later.');
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
	 * Remove the specified resource from storage.
	 *
	 * @param  \Illuminate\Http\Request
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request) {
		if (isset($request->deleteid) && $request->deleteid != null) {
			$attributesset = AttributesSet::find($request->deleteid);

			if (isset($attributesset->id)) {
				$attributesset->status = 'DL';
				if ($attributesset->save()) {
					$request->session()->flash('success', 'AttributesSet deleted successfully.');
					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Unable to delete AttributesSet. Please try again later.');
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

		if (isset($request->ids) && $request->ids != null) {
			$ids = count($request->ids);
			$count = 0;
			foreach ($request->ids as $id) {
				$attributesset = AttributesSet::find($id);

				if (isset($attributesset->id)) {
					$attributesset->status = 'DL';
					if ($attributesset->save()) {
						$count++;
					}
				}
			}
			if ($count == $ids) {
				echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'AttributesSet deleted successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Not all AttributesSet were deleted. Please try again later.']);
			}
		} else {
			echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
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
				$attributesset = AttributesSet::find($id);

				if (isset($attributesset->id)) {
					if ($attributesset->status == 'AC') {
						$attributesset->status = 'IN';
					} elseif ($attributesset->status == 'IN') {
						$attributesset->status = 'AC';
					}

					if ($attributesset->save()) {
						$count++;
					}
				}
			}
			if ($count == $ids) {
				echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'AttributesSet updated successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Not all AttributesSet were updated. Please try again later.']);
			}
		} else {
			echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
		}
	}

}
