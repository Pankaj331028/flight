<?php

namespace App\Http\Controllers;

use App\Library\ResponseMessages;
use App\Model\Flight;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use URL;
use Validator;

class SampleApiController extends MyController {
	// function to get all state

	private $support;

	public function __construct() {
		$this->pagelength = 6;
		$this->support = [
			'url' => URL::to('/'),
			'text' => 'For Joining Automation Course, Please Contact-Velmurugan 9944152058',
		];
	}

	public function getFlights(Request $request) {

		try {
			$request->page = $request->page ?? 1;
			$data = Flight::select('id', 'flightName', 'Country', 'Destinations', 'URL')->offset(($request->page - 1) * $this->pagelength)->limit($this->pagelength)->get();

			if (count($data) > 0) {
				$this->response = [
					'page' => $request->page,
					'per_page' => $this->pagelength,
					'total' => Flight::count(),
					'total_pages' => ceil(Flight::count() / $this->pagelength),
					'data' => $data,
					'support' => $this->support,
				];
				return response()->json($this->response, 200);
			} else {
				$this->response = array(
					'status' => 404,
					'message' => ResponseMessages::getStatusCodeMessages(404),
				);
				return response()->json($this->response, 404);
			}

		} catch (\Exception $e) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);

			return response()->json($this->response, 501);
		}

	}

	public function getSingleFlight(Request $request, $id) {

		try {
			$flight = Flight::find($id);

			if (isset($flight->id)) {

				$this->response = [
					'data' => $flight,
					'support' => $this->support,
				];
				return response()->json($this->response, 200);
			} else {
				$this->response = array(
					'status' => 404,
					'message' => ResponseMessages::getStatusCodeMessages(404),
				);
				return response()->json($this->response, 404);
			}

		} catch (\Exception $e) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
			return response()->json($this->response, 501);
		}

	}

	public function createFlight(Request $request) {

		try {

			$validate = Validator::make($request->all(), [
				'flightName' => 'required',
				'Country' => 'required',
				'Destinations' => 'required',
				'URL' => 'required|url',
			]);

			if ($validate->fails()) {

				$errors = $validate->errors();
				$this->response = array(
					"status" => 300,
					"message" => $errors->first(),
					"data" => null,
					"errors" => $errors,
				);
				return response()->json($this->response, 300);
			}

			if (Flight::create($request->all())) {
				$this->response = [
					'message' => ResponseMessages::getStatusCodeMessages(330),
					'data' => Flight::select('id', 'flightName', 'Country', 'Destinations', 'URL', 'Created_date')->orderBy('id', 'desc')->first(),
				];

				return response()->json($this->response, 201);
			} else {
				$this->response = array(
					"status" => 500,
					"message" => ResponseMessages::getStatusCodeMessages(500),
				);
			}

		} catch (\Exception $e) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
			return response()->json($this->response, 501);
		}

	}

	public function updateFlight(Request $request, $id) {

		try {
			$flight = Flight::find($id);

			if (isset($flight->id)) {
				if (Flight::where('id', $id)->update($request->all())) {
					$this->response = [
						'message' => ResponseMessages::getStatusCodeMessages(331),
						'data' => Flight::select('id', 'flightName', 'Country', 'Destinations', 'URL', 'Updated_date')->orderBy('id', 'desc')->first(),
					];

					return response()->json($this->response, 200);
				} else {
					$this->response = array(
						"status" => 500,
						"message" => ResponseMessages::getStatusCodeMessages(500),
					);
					return response()->json($this->response, 500);
				}
			} else {

				$this->response = array(
					"status" => 400,
					"message" => ResponseMessages::getStatusCodeMessages(332),
				);
				return response()->json($this->response, 400);
			}

		} catch (\Exception $e) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
			return response()->json($this->response, 501);
		}

	}

	public function deleteFlight(Request $request, $id) {

		try {

			$flight = Flight::find($id);

			if (isset($flight->id)) {
				if (Flight::where('id', $id)->delete()) {
					return response()->json([], 204);
				} else {
					$this->response = array(
						"status" => 500,
						"message" => ResponseMessages::getStatusCodeMessages(500),
					);
					return response()->json($this->response, 500);
				}
			} else {

				$this->response = array(
					"status" => 400,
					"message" => ResponseMessages::getStatusCodeMessages(332),
				);
				return response()->json($this->response, 400);
			}

		} catch (\Exception $e) {
			dd($e);
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
			return response()->json($this->response, 501);
		}

	}

}
