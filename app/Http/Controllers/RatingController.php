<?php

namespace App\Http\Controllers;

use App\Model\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller {
	public $rating;
	public $columns;

	public function __construct() {
		$this->rating = new Rating;
		$this->columns = ['sno', 'user', 'rating'];
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		return view('ratings.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function ratingsAjax(Request $request) {
		if(isset($request->search['value'])){
            $request->search = $request->search['value'];
        }
		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column'];
			$request->order_dir = $request->order[0]['dir'];
		}
		$records = $this->rating->fetchRatings($request, $this->columns);
		$total = $records->get();
		if (isset($request->start)) {
			$ratings = $records->offset($request->start)->limit($request->length)->get();
		} else {
			$ratings = $records->offset($request->start)->limit(count($total))->get();
		}
		// echo $total;
		$result = [];
		$i = $request->start + 1;
		foreach ($ratings as $rating) {
			$data = [];
			$data['sno'] = $i++;
			$data['user'] = ucfirst($rating->user['first_name'] . " " . $rating->user['last_name']);
			$data['rating'] = $rating->rating;
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
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $id = null) {
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id) {
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) {
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		//
	}
}
