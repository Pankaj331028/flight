<?php

namespace App\Http\Controllers;
use App\Library\Helper;
use App\Model\CMS;
use App\Model\FAQQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CMSController extends Controller {
	public $cms;
	public $columns;

	public function __construct() {
		$this->cms = new CMS;
		$this->columns = [
			"sno", "name", "action",
		];
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		return view('cms.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function cmsAjax(Request $request) {
		if(isset($request->search['value']))
        {
            $request->search = $request->search['value'];
        }
		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column'];
			$request->order_dir = $request->order[0]['dir'];
		}
		$records = $this->cms->fetchCMS($request, $this->columns);
		$total = $records->get();
		if (isset($request->start)) {
			$cms = $records->offset($request->start)->limit($request->length)->get();
		} else {
			$cms = $records->offset($request->start)->limit(count($total))->get();
		}
		// echo $total;
		$result = [];
		$i = 1;
		foreach ($cms as $list) {
			$data = [];
			$data['sno'] = $i++;
			$data['name'] = $list->name;
			$action = '';

			if (Helper::checkAccess(route('editCMS'))) {
				$action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('editCMS', ['id' => $list->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
			}
			if (Helper::checkAccess(route('viewCMS'))) {
				$action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('viewCMS', ['id' => $list->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';
			}
			$data['action'] = $action;

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
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $id = null) {
		if (isset($id) && $id != null) {
			$cms = CMS::where('id', $id)->first();
			if (isset($cms->id)) {
				$faqs = [];
				if ($cms->slug == 'faq' || $cms->slug == 'delivery-faq') {

					$faqs = FAQQuestion::fetchquestions($cms->slug);
				}
				return view('cms.view', compact('cms', 'faqs'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('cms');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('cms');
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
			$cms = CMS::where('id', $id)->first();
			if (isset($cms->id)) {
				$faqs = [];
				if ($cms->slug == 'faq' || $cms->slug == 'delivery-faq') {
					$faqs = FAQQuestion::fetchquestions($cms->slug);
				}
				return view('cms.create', compact('cms', 'faqs'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('cms');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('cms');
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
			$cms = CMS::where('id', $id)->first();
			if (isset($cms->id)) {
				try {
					if ($cms->slug == 'faq' || $cms->slug == 'delivery-faq') {
						$count = 0;
						foreach ($request->all() as $key => $value) {
							if (stripos($key, 'faqid') !== false) {
								$count++;
							}
						}
						FAQQuestion::saveQuestions($count, $request->all());
					}else{
						$cms->content = $request->cms_content;

						if ($cms->original_content == '') {
							$cms->original_content = $request->cms_content;
						}

						$cms->save();
					}

					$request->session()->flash('success', 'Page updated successfully');
					return redirect()->route('cms');

				} catch (Exception $e) {
					$request->session()->flash('error', 'Something went wrong. Please try again later.');
					return redirect()->route('cms');
				}

			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('cms');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('cms');
		}

	}
	/**
	 * Remove the specified FQA from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request) {
		if (isset($request->id) && $request->id != null) {
			$faq = FAQQuestion::find($request->id);

			if (isset($faq->id)) {
				$faq->status = 'DL';
				if ($faq->save()) {
					echo json_encode(["status" => 1, 'message' => 'FAQ deleted successfully.']);
				} else {
					echo json_encode(["status" => 0, 'message' => 'Some error occurred while deleting the FAQ']);
				}
			} else {
				echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
			}

		} else {
			echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
		}
	}

	public function privacypolicy() {
		$cms = CMS::where('slug', 'privacy-policy')->first();
		return view('cms.page', compact('cms'));
	}
}
