<?php

namespace App\Http\Controllers;
use App\Model\City;
use App\Model\Country;
use App\Model\State;
use Illuminate\Http\Request;
use Session;

class StateCityController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		//
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
	public function show($id) {
		//
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

	/**
	 * fetch cities from state selected
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function fetch(Request $request) {
		$cities = City::whereHas('state', function ($query) use ($request) {
			$query->where('g_states.name', $request->state);
		})->where("status", "AC")->pluck('name');

		$configData = [
			'states' => State::whereHas('country', function ($query) use ($request) {
				$query->where('name', 'India')->where("status", "AC");
			})->pluck('name'),
			'cities' => $cities,
		];
		config(['statecity' => $configData]);
		Session::put('globalCity', 'all');

		echo json_encode($cities);
	}
	/**
	 * fetch states from country selected
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function fetchStates(Request $request) {
		$states = State::whereHas('country', function ($query) use ($request) {
			$query->where('name', $request->country)->where("status", "AC");
		})->pluck('name');

		$configData = [
			'states' => $states,
			'cities' => [],
		];
		config(['statecity' => $configData]);
		echo json_encode($states);
	}

	/**
	 * set city session
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function setSession(Request $request) {
		Session::put('globalCity', $request->city);
		Session::put('globalState', $request->state);
		echo Session::get('globalCity');
	}
	/**
	 * reset city session
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function resetSession(Request $request) {
		Session::put('globalCity', 'all');
		Session::put('globalState', 'all');

		echo Session::get('globalCity');
	}
}
