<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\ApiController as ApiController;
use App\Http\Controllers\HotelApiController as HotelApiController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MyController as MyController;
use App\Model\BusRuleRef;
use App\Model\Hotel;
use App\Model\User;
// use Illuminate\Support\Facades\Http;
use Auth;
use DB;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Session;

// App\Services\PayUService\Exception

class FlightController extends Controller {

	public $tripHost = 'http://170.39.214.114:8086/omrbranch/';
	public $airlineHost = 'http://170.39.214.114:8085/omrbranch/';
	public $bookingHost = 'http://170.39.214.114:8087/omrbranch/';
	public $currency;
	public $globelFlightNameArray ;

	public function __construct(Request $request) {
		$this->api = (new ApiController($request));
		$this->hotelapi = (new HotelApiController($request));
		$this->function = (new MyController($request));
		$this->currency = BusRuleRef::where('rule_name', 'currency')->first()->rule_value;
	}

	public function flight(Request $request) {

		if (Auth::guard('front')->user()) {
			$user_id = Auth::guard('front')->user()->id;

			$client = new Client();

			$response = $client->get('http://170.39.214.114:8085/omrbranch/getAirports', [
				'headers' => [
					'Authorization' => 'Bearer ' . Session::get('logtoken'),
				],
			]);

			$apiData = json_decode($response->getBody(), true);
			// dd($apiData);
			$airportCode = [];

			$airPortId = [];
			foreach ($apiData as $api_data) {
				// dd($api_data);
				// $airportCode[$api_data['airportId']] = $api_data['airportName'].' ('. $api_data['airportCode'].')';
				$keySet = $api_data['airportName'] . ' (' . $api_data['airportCode'] . ') - ' . $api_data['city_country'];
				$airportCode[] = $api_data['airportName'] . ' (' . $api_data['airportCode'] . ') - ' . $api_data['city_country'];
				$airPortId[$keySet] = $api_data['airportId'];
			}
			// dd($airPortId);
			return view('front.flight.index', compact('apiData', 'airportCode', 'airPortId'));
		} else {
			$user_id = '';
			$user = '';
			return view('front.login');
		}
	}

	public function viewFlight(Request $request, $id) {
		// dd($request->all());
		// dd($id);
		$trip_type = $request->trip_type;
		$countryList = $this->api->countryList($request);
		// dd($countryList);
		$client = new Client();

		$response = $client->get($this->airlineHost . 'getAirports', [
			'headers' => [
				'Authorization' => 'Bearer ' . Session::get('logtoken'),
			],
		]);

		$apiData = json_decode($response->getBody(), true);

		$response = $client->get($this->airlineHost . 'getAirlinesByNames', [
			'headers' => [
				'Authorization' => 'Bearer ' . Session::get('logtoken'),
			],
		]);

		$getAirlinesByNames = json_decode($response->getBody(), true);
		$getAirlinesName = [];

		foreach ($getAirlinesByNames as $getAirlines) {
			$getAirlinesName[] = explode(":", $getAirlines)[1];
		}

		$flightNameArray = [];
		foreach ($apiData as $api_data) {
			$flightNameArray[$api_data['airportId']] = $api_data['airportCode'];
		}
		$this->globelFlightNameArray = $flightNameArray;
		$getAirlines = $client->get($this->airlineHost . 'getAirlines', [
			'headers' => [
				'Authorization' => 'Bearer ' . Session::get('logtoken'),
			],
		]);

		$getAirlines = json_decode($getAirlines->getBody(), true);

		$getAirlineDetails = [];
		foreach ($getAirlines as $key => $get_airline) {
			$getAirlineDetails[$get_airline['airlineId']] = [
				'airlineName' => $get_airline['airlineName'],
				'airlineLogo' => $get_airline['airlineLogo'],
			];
		}

		if ($trip_type == 'one_way_trip') {
			$fligtId = explode(" ", $id);
		} else {
			$fligtId = explode(",", $id);
		}

		$airlineDeatails = [];
		// $createTripDetails = [];
		foreach ($fligtId as $ids) {
			// dd($ids);
			$response = $client->get($this->airlineHost . 'searchFlight/' . $ids, [
				'headers' => [
					'Authorization' => 'Bearer ' . Session::get('logtoken'),
				],
			]);

			$airlineData = json_decode($response->getBody(), true);
			$airlineDeatails[] = $airlineData;

			// $response = $client->post($this->tripHost . 'createTrip', [
			// 	'headers' => [
			// 		'Authorization' => 'Bearer ' . Session::get('logtoken'),
			// 		'Content-Type' => 'application/json',
			// 	],
			// 	'json' => [
			// 		'routeId' => [$ids],
			// 	],
			// ]);

			// $statusCode = $response->getStatusCode();
			// $createTrip = json_decode($response->getBody(), true);
			// $createTripDetails[] = $createTrip;

		}

		if ($trip_type == 'one_way_trip') {
			$response = $client->post($this->tripHost . 'createTrip', [
				'headers' => [
					'Authorization' => 'Bearer ' . Session::get('logtoken'),
					'Content-Type' => 'application/json',
				],
				'json' => [
					'routeId' => [$id],
				],
			]);

			$statusCode = $response->getStatusCode();
			$createTrip = json_decode($response->getBody(), true);

		} else {
			if ($trip_type == 'roundTrip') {
				// dd();
				$response = $client->post($this->tripHost . 'roundTrip', [
					'headers' => [
						'Authorization' => 'Bearer ' . Session::get('logtoken'),
						'Content-Type' => 'application/json',
					],
					'json' => [
						'routeId' => explode(",", $id),
					],
				]);

				$statusCode = $response->getStatusCode();
				$createTrip = json_decode($response->getBody(), true);

			} else {

				$response = $client->post($this->tripHost . 'multiTrip', [
					'headers' => [
						'Authorization' => 'Bearer ' . Session::get('logtoken'),
						'Content-Type' => 'application/json',
					],
					'json' => [
						'routeId' => explode(",", $id),
					],
				]);

				$statusCode = $response->getStatusCode();
				$createTrip = json_decode($response->getBody(), true);
			}
		}
		// dd($createTrip);
		// dd($createTrip['tripDetailsId']);
		// "userId":254,
		// "tripId":176
		//createTripDetails
		$response = $client->post($this->tripHost . 'createTripDetails', [
			'headers' => [
				'Authorization' => 'Bearer ' . Session::get('logtoken'),
				'Content-Type' => 'application/json',
			],
			'json' => [
				'userId' => $createTrip['userId'],
				'tripId' => $createTrip['tripDetailsId'],
			],
		]);

		$statusCode = $response->getStatusCode();
		$createTripDetails = json_decode($response->getBody(), true);
		// dd($createTrip,$createTripDetails);
		// dd($createTripDetails['tripDetailsId']);
		// $response = $client->get($this->tripHost . 'getTripDetails/' . $createTrip['tripDetailsId'], [
		// 	'headers' => [
		// 		'Authorization' => 'Bearer ' . Session::get('logtoken'),
		// 	],
		// ]);

		// $getTripDetailsData = json_decode($response->getBody(), true);

		// dd($getTripDetailsData);
		// get getTrip
		$response = $client->get($this->tripHost . 'getTrip/' . $createTrip['tripDetailsId'], [
			'headers' => [
				'Authorization' => 'Bearer ' . Session::get('logtoken'),
			],
		]);

		$getTrip = json_decode($response->getBody(), true);
		// dd($getTrip);
		// foreach($getTrip['airlineRoutesEntity'] as $v){
		// 	dd($v);
		// }

		// get insurance
		$response = $client->get($this->tripHost . 'getInsurances', [
			'headers' => [
				'Authorization' => 'Bearer ' . Session::get('logtoken'),
			],
		]);

		$getInsurances = json_decode($response->getBody(), true);

		// getPromotions and Cupone Details
		$response = $client->get($this->tripHost . 'getPromotions', [
			'headers' => [
				'Authorization' => 'Bearer ' . Session::get('logtoken'),
			],
		]);

		$getPromotions = json_decode($response->getBody(), true);

		// getMeals
		$response = $client->get($this->tripHost . 'getMeals', [
			'headers' => [
				'Authorization' => 'Bearer ' . Session::get('logtoken'),
			],
		]);

		$getMeals = json_decode($response->getBody(), true);
		// dd($getMeals);

		// getCars
		$response = $client->get($this->tripHost . 'getCars', [
			'headers' => [
				'Authorization' => 'Bearer ' . Session::get('logtoken'),
			],
		]);

		$getCars = json_decode($response->getBody(), true);

		// getItinerary
		$response = $client->get($this->tripHost . 'getItinerary', [
			'headers' => [
				'Authorization' => 'Bearer ' . Session::get('logtoken'),
			],
		]);

		$getItinerary = json_decode($response->getBody(), true);
		// dd($getItinerary);

		$states = Hotel::select(DB::Raw('distinct state as state'))->whereStatus('AC')->pluck('state')->toArray();

		// dd($getInsurances);

		return view('front.flight.checkout', compact('states', 'airlineDeatails', 'countryList', 'trip_type', 'flightNameArray', 'getAirlineDetails', 'getAirlinesName', 'getInsurances', 'getPromotions', 'createTrip', 'createTripDetails', 'getItinerary', 'getMeals', 'getCars', 'getTrip'));

	}
	

	public function bookings(Request $request) {

		$client = new Client();
		$response = $client->get($this->bookingHost . 'getbookings', [
			'headers' => [
				'Authorization' => 'Bearer ' . Session::get('logtoken'),
			],
		]);

		if ($response->getStatusCode() == 200) {
			$bookings = json_decode($response->getBody(), true);
			$user = User::find(Auth::guard('front')->user()->id);
			$currency = $this->currency;
			
			// dd($bookings->airlineRouteDetails);


			$response = $client->get($this->airlineHost . 'getAirports', [
				'headers' => [
					'Authorization' => 'Bearer ' . Session::get('logtoken'),
				],
			]);
	
			$apiData = json_decode($response->getBody(), true);
	
			$flightNameArray = [];
			foreach ($apiData as $api_data) {
				$flightNameArray[$api_data['airportId']] = $api_data['airportCode'];
			}

			$getAirlines = $client->get($this->airlineHost . 'getAirlines', [
				'headers' => [
					'Authorization' => 'Bearer ' . Session::get('logtoken'),
				],
			]);
	
			$getAirlines = json_decode($getAirlines->getBody(), true);
	
			$getAirlineDetails = [];
			foreach ($getAirlines as $key => $get_airline) {
				$getAirlineDetails[$get_airline['airlineId']] = [
					'airlineName' => $get_airline['airlineName'],
					'airlineLogo' => $get_airline['airlineLogo'],
				];
			}

			// dd($getAirlineDetails);
			return view('front.flight.bookings', compact('bookings', 'currency', 'user','flightNameArray','getAirlineDetails'));

		} else {

			$request->session()->flash('error', 'Something went wrong. Please try again later!');
			return redirect()->route('flight');
		}

	}

	public function searchflight(Request $request) {
		// dd($request->all());
		$trip_type = $request->trip_type;
		$client = new Client();

		$response = $client->get('http://170.39.214.114:8085/omrbranch/getAirports', [
			'headers' => [
				'Authorization' => 'Bearer ' . Session::get('logtoken'),
			],
		]);

		$apiData = json_decode($response->getBody(), true);

		$airportCode = [];
		$airPortId = [];
		$flightNameArray = [];
		foreach ($apiData as $api_data) {

			$airportCode[] = $api_data['airportName'] . ' (' . $api_data['airportCode'] . ') - ' . $api_data['city_country'];
			$keySet = $api_data['airportName'] . ' (' . $api_data['airportCode'] . ') - ' . $api_data['city_country'];
			$airPortId[$keySet] = $api_data['airportId'];
			$flightNameArray[$api_data['airportId']] = $api_data['airportCode'];
		}
		
		$request->flyingfrom = $airPortId[$request->flyingfrom];
		$request->flyingTo = $airPortId[$request->flyingTo];

		$getAirlines = $client->get($this->airlineHost . 'getAirlines', [
			'headers' => [
				'Authorization' => 'Bearer ' . Session::get('logtoken'),
			],
		]);

		$getAirlines = json_decode($getAirlines->getBody(), true);

		$getAirlineDetails = [];
		foreach ($getAirlines as $key => $get_airline) {
			$getAirlineDetails[$get_airline['airlineId']] = [
				'airlineName' => $get_airline['airlineName'],
				'airlineLogo' => $get_airline['airlineLogo'],
			];
		}

		if ($request->ajax()) {
			// dd($request->all());
			// dd(implode(",",$request->airlines_type));
			// $client = new Client();
			// dd($request->flyingfrom,$request->flyingTo,$request->departureDates,$request->passenger,implode(",",$request->airlines_type),$request->sortby,$request->stopFilter,$request->offers_type);
			if ($request->trip_type == 'one_way_trip') {
				$queryParams = [
					'from' => $request->flyingfrom,
					'to' => $request->flyingTo,
					'deptDate' => $request->departureDates,
					'traveller' => $request->passenger,
				];

				if ($request->sortby != 'undefined') {
					$queryParams['sortBy'] = $request->sortby;
				}

				if ($request->stopFilter != 'undefined') {
					$queryParams['stopFilter'] = $request->stopFilter;
				}

				if ($request->offers_type != 'undefined') {
					$queryParams['offerFilter'] = $request->offers_type;
				}
				if ($request->filled('airlines_type')) {
					$queryParams['airlineFilter'] = implode(",", $request->airlines_type);
				}
				// dd($queryParams);
				$response = $client->get($this->airlineHost . 'searchFlight', [
					'headers' => [
						'Authorization' => 'Bearer ' . Session::get('logtoken'),
					],
					// 'query' => [
					// 	'from' => $request->flyingfrom,
					// 	'to' => $request->flyingTo,
					// 	'deptDate' => $request->departureDates,
					// 	'traveller' => $request->passenger,
					// 	'airlineFilter' => implode(",",$request->airlines_type),
					// 	'sortBy' => $request->sortby,
					// 	'stopFilter' => $request->stopFilter,
					// 	'offerFilter' => $request->offers_type,
					// ],
					'query' => $queryParams,
				]);
				$data = json_decode($response->getbody(), true);
				// dd($data);
				echo view('front.flight.flight_listing', compact('data', 'trip_type', 'getAirlineDetails', 'flightNameArray'))->render();
				// if (isset($data['status'])) {
				// 	// dd($request->all());
				// 	echo view('front.travel.hotel_list', compact('data'))->render();
				// } else {
				// 	echo 'error';
				// }
			} else {

				if ($trip_type == 'roundTrip') {
					// $queryParams = [
					// 	'from' => $request->flyingfrom,
					// 	'to' => $request->flyingTo,
					// 	'deptDate' => $request->departureDates,
					// 	'traveller' => $request->passenger,
					// ];

					// if ($request->sortby != 'undefined') {
					// 	$queryParams['sortBy'] = $request->sortby;
					// }

					// if ($request->stopFilter != 'undefined') {
					// 	$queryParams['stopFilter'] = $request->stopFilter;
					// }

					// if ($request->offers_type != 'undefined') {
					// 	$queryParams['offerFilter'] = $request->offers_type;
					// }
					// if ($request->filled('airlines_type')) {
					// 	$queryParams['airlineFilter'] = implode(",",$request->airlines_type);
					// }

					// $response = $client->get($this->airlineHost.'searchFlight', [
					// 	'headers' => [
					// 		'Authorization' => 'Bearer ' . Session::get('logtoken'),
					// 	],
					// 	'query' => $queryParams,
					// ]);
					// $data = json_decode($response->getbody(),true);
					// $queryParamsReturn = [
					// 	'from' => $request->flyingTo,
					// 	'to' => $request->flyingfrom,
					// 	'deptDate' => $request->returnDates,
					// 	'traveller' => $request->passenger,
					// ];

					// if ($request->sortby != 'undefined') {
					// 	$queryParamsReturn['sortBy'] = $request->sortby;
					// }

					// if ($request->stopFilter != 'undefined') {
					// 	$queryParamsReturn['stopFilter'] = $request->stopFilter;
					// }

					// if ($request->offers_type != 'undefined') {
					// 	$queryParamsReturn['offerFilter'] = $request->offers_type;
					// }
					// if ($request->filled('airlines_type')) {
					// 	$queryParamsReturn['airlineFilter'] = implode(",",$request->airlines_type);
					// }

					// $responseReturn = $client->get($this->airlineHost.'searchFlight', [
					// 	'headers' => [
					// 		'Authorization' => 'Bearer ' . Session::get('logtoken'),
					// 	],
					// 	'query' => $queryParamsReturn,
					// ]);
					// $dataReturn = json_decode($responseReturn->getbody(),true);
					function buildQueryParams($request, $direction = 'outbound') {
						$queryParams = [
							'from' => $direction === 'outbound' ? $request->flyingfrom : $request->flyingTo,
							'to' => $direction === 'outbound' ? $request->flyingTo : $request->flyingfrom,
							'deptDate' => $direction === 'outbound' ? $request->departureDates : $request->returnDates,
							'traveller' => $request->passenger,
						];

						if ($request->sortby != 'undefined') {
							$queryParams['sortBy'] = $request->sortby;
						}

						if ($request->stopFilter != 'undefined') {
							$queryParams['stopFilter'] = $request->stopFilter;
						}

						if ($request->offers_type != 'undefined') {
							$queryParams['offerFilter'] = $request->offers_type;
						}

						if ($request->filled('airlines_type')) {
							$queryParams['airlineFilter'] = implode(",", $request->airlines_type);
						}

						return $queryParams;
					}
					$queryParams = buildQueryParams($request, 'outbound');
					$response = $client->get($this->airlineHost . 'searchFlight', [
						'headers' => [
							'Authorization' => 'Bearer ' . Session::get('logtoken'),
						],
						'query' => $queryParams,
					]);
					$data = json_decode($response->getbody(), true);

					$queryParamsReturn = buildQueryParams($request, 'return');
					$responseReturn = $client->get($this->airlineHost . 'searchFlight', [
						'headers' => [
							'Authorization' => 'Bearer ' . Session::get('logtoken'),
						],
						'query' => $queryParamsReturn,
					]);
					$dataReturn = json_decode($responseReturn->getbody(), true);
					// dd($data);
					echo view('front.flight.flight_listing', compact('data', 'trip_type', 'getAirlineDetails', 'flightNameArray', 'dataReturn'))->render();
				} else {
					// dd($request->all());
					$dropCityId = [];
					foreach ($request->dropCity as $dropcity) {

						$dropCityId[] = $airPortId[$dropcity];
					}

					if (count($request->dropCity)) {
						$i = 0;

						$multipleFlightData = [];
						$flightname = [];
						foreach ($request->dropCity as $key => $dropCityData) {

							$flightname[] = [
								'from_location' => $flightNameArray[($key == 0) ? $request->flyingfrom : $dropCityId[$i - 1]],
								'to_location' => $flightNameArray[$airPortId[$dropCityData]],

							];

							$queryParams = [
								'from' => ($key == 0) ? $request->flyingfrom : $dropCityId[$i - 1],
								'to' => $airPortId[$dropCityData],
								'deptDate' => ($key == 0) ? $request->departureDates : $request->dateDeparture[$i - 1],
								'traveller' => $request->passenger,
							];

							if ($request->sortby != 'undefined') {
								$queryParams['sortBy'] = $request->sortby;
							}

							if ($request->stopFilter != 'undefined') {
								$queryParams['stopFilter'] = $request->stopFilter;
							}

							if ($request->offers_type != 'undefined') {
								$queryParams['offerFilter'] = $request->offers_type;
							}
							if ($request->filled('airlines_type')) {
								$queryParams['airlineFilter'] = implode(",", $request->airlines_type);
							}

							$responseOne = $client->get($this->airlineHost . 'searchFlight', [
								'headers' => [
									'Authorization' => 'Bearer ' . Session::get('logtoken'),
								],
								// 'query' => [
								// 	'from' => ($key == 0) ? $request->flyingfrom :$dropCityId[$i-1],
								// 	'to' => $airPortId[$dropCityData],
								// 	'deptDate' => ($key == 0) ? $request->departureDates : $request->dateDeparture[$i-1],

								// 	'traveller' =>$request->passenger,
								// ],
								'query' => $queryParams,
							]);
							$dataFlightOne = json_decode($responseOne->getBody(), true);
							$multipleFlightData[] = $dataFlightOne;
							$i++;
						}

						$flightname[] = [
							'from_location' => $flightNameArray[$dropCityId[count($request->dropCity) - 1]],
							'to_location' => $flightNameArray[$request->flyingTo],
						];

						$queryParamsLast = [
							'from' => $dropCityId[count($request->dropCity) - 1],
							'to' => $request->flyingTo,
							'deptDate' => $request->dateDeparture[count($request->dateDeparture) - 1],
							'traveller' => $request->passenger,
						];

						if ($request->sortby != 'undefined') {
							$queryParamsLast['sortBy'] = $request->sortby;
						}

						if ($request->stopFilter != 'undefined') {
							$queryParamsLast['stopFilter'] = $request->stopFilter;
						}

						if ($request->offers_type != 'undefined') {
							$queryParamsLast['offerFilter'] = $request->offers_type;
						}
						if ($request->filled('airlines_type')) {
							$queryParamsLast['airlineFilter'] = implode(",", $request->airlines_type);
						}

						$responseLast = $client->get($this->airlineHost . 'searchFlight', [
							'headers' => [
								'Authorization' => 'Bearer ' . Session::get('logtoken'),
							],
							// 'query' => [
							// 	'from' => $dropCityId[count($request->dropCity)-1],
							// 	'to' => $request->flyingTo,
							// 	'deptDate' => $request->dateDeparture[count($request->dateDeparture)-1],
							// 	'traveller' =>$request->passenger,
							// ],
							'query' => $queryParamsLast,
						]);

						$dataFlightLast = json_decode($responseLast->getBody(), true);
						$multipleFlightData[] = $dataFlightLast;

						// dd($multipleFlightData);
						// $flightname = [];
						// foreach($multipleFlightData as $multipleFlights){
						// 	$flightname [] = [
						// 		'from_location' => $flightNameArray[$multipleFlights[0]['from_location']],
						// 		'to_location' => $flightNameArray[$multipleFlights[0]['to_location']],

						// 	];
						// }
						// dd($flightname);
					}
					// dd($multipleFlightData);
					$dataReturn = '';
					$data = '';
					echo view('front.flight.flight_listing', compact('data', 'trip_type', 'getAirlineDetails', 'flightNameArray', 'multipleFlightData', 'flightname'))->render();
				}
			}

		} else {
			if ($trip_type == 'one_way_trip') {
				$queryParams = [
					'from' => $request->flyingfrom,
					'to' => $request->flyingTo,
					'deptDate' => $request->departureDates,
					'traveller' => $request->passenger,
				];

				if ($request->sortby != 'undefined') {
					$queryParams['sortBy'] = $request->sortby;
				}

				if ($request->stopFilter != 'undefined') {
					$queryParams['stopFilter'] = $request->stopFilter;
				}

				if ($request->offers_type != 'undefined') {
					$queryParams['offerFilter'] = $request->offers_type;
				}
				if ($request->filled('airlines_type')) {
					$queryParams['airlineFilter'] = implode(",", $request->airlines_type);
				}
				$response = $client->get('http://170.39.214.114:8085/omrbranch/searchFlight', [
					'headers' => [
						'Authorization' => 'Bearer ' . Session::get('logtoken'),
					],
					// 'query' => [
					// 	'from' => $request->flyingfrom,
					// 	'to' => $request->flyingTo,
					// 	'deptDate' => $request->departureDates,
					// 	'traveller' =>$request->passenger,
					// ],
					'query' => $queryParams,
				]);

				$data = json_decode($response->getBody(), true);
				$dataReturn = '';
				$multipleFlightData = '';
				$flightname = '';
			} else {
				if ($trip_type == 'roundTrip') {
					// $response = $client->get($this->airlineHost.'searchFlight', [
					// 	'headers' => [
					// 		'Authorization' => 'Bearer ' . Session::get('logtoken'),
					// 	],
					// 	'query' => [
					// 		'from' => $request->flyingfrom,
					// 		'to' => $request->flyingTo,
					// 		'deptDate' => $request->departureDates,
					// 		'traveller' =>$request->passenger,
					// 	],
					// ]);

					// $data = json_decode($response->getBody(), true);

					// $responseReturn = $client->get($this->airlineHost.'searchFlight', [
					// 	'headers' => [
					// 		'Authorization' => 'Bearer ' . Session::get('logtoken'),
					// 	],
					// 	'query' => [
					// 		'from' => $request->flyingTo,
					// 		'to' => $request->flyingfrom,
					// 		'deptDate' => $request->returnDates,
					// 		'traveller' =>$request->passenger,
					// 	],
					// ]);

					// $dataReturn = json_decode($responseReturn->getBody(), true);
					function buildQueryParams($request, $direction = 'outbound') {
						$queryParams = [
							'from' => $direction === 'outbound' ? $request->flyingfrom : $request->flyingTo,
							'to' => $direction === 'outbound' ? $request->flyingTo : $request->flyingfrom,
							'deptDate' => $direction === 'outbound' ? $request->departureDates : $request->returnDates,
							'traveller' => $request->passenger,
						];

						if ($request->sortby != 'undefined') {
							$queryParams['sortBy'] = $request->sortby;
						}

						if ($request->stopFilter != 'undefined') {
							$queryParams['stopFilter'] = $request->stopFilter;
						}

						if ($request->offers_type != 'undefined') {
							$queryParams['offerFilter'] = $request->offers_type;
						}

						if ($request->filled('airlines_type')) {
							$queryParams['airlineFilter'] = implode(",", $request->airlines_type);
						}

						return $queryParams;
					}
					$queryParams = buildQueryParams($request, 'outbound');
					$response = $client->get($this->airlineHost . 'searchFlight', [
						'headers' => [
							'Authorization' => 'Bearer ' . Session::get('logtoken'),
						],
						'query' => $queryParams,
					]);
					$data = json_decode($response->getbody(), true);

					$queryParamsReturn = buildQueryParams($request, 'return');
					$responseReturn = $client->get($this->airlineHost . 'searchFlight', [
						'headers' => [
							'Authorization' => 'Bearer ' . Session::get('logtoken'),
						],
						'query' => $queryParamsReturn,
					]);
					$dataReturn = json_decode($responseReturn->getbody(), true);

					$multipleFlightData = '';
					$flightname = '';
				} else {

					// $dropCityId = [];
					// foreach($request->dropCity as $dropcity){

					// 	$dropCityId []  = $airPortId[$dropcity];
					// }

					// if(count($request->dropCity) ){
					// 	$i = 0;

					// 	$multipleFlightData = [];
					// 	foreach ($request->dropCity as $key => $dropCityData) {
					// 				$responseOne = $client->get($this->airlineHost.'searchFlight', [
					// 					'headers' => [
					// 						'Authorization' => 'Bearer ' . Session::get('logtoken'),
					// 					],
					// 					'query' => [
					// 						'from' => ($key == 0) ? $request->flyingfrom :$dropCityId[$i-1],
					// 						'to' => $airPortId[$dropCityData],
					// 						'deptDate' => ($key == 0) ? $request->departureDates : $request->dateDeparture[$i-1],

					// 						'traveller' =>$request->passenger,
					// 					],
					// 				]);
					// 				$dataFlightOne = json_decode($responseOne->getBody(), true);
					// 				$multipleFlightData []=$dataFlightOne;
					// 			$i++;
					// 	}

					// 	$responseLast = $client->get($this->airlineHost.'searchFlight', [
					// 		'headers' => [
					// 			'Authorization' => 'Bearer ' . Session::get('logtoken'),
					// 		],
					// 		'query' => [
					// 			'from' => $dropCityId[count($request->dropCity)-1],
					// 			'to' => $request->flyingTo,
					// 			'deptDate' => $request->dateDeparture[count($request->dateDeparture)-1],
					// 			'traveller' =>$request->passenger,
					// 		],
					// 	]);
					// 	$dataFlightLast = json_decode($responseLast->getBody(), true);
					// 	$multipleFlightData []=$dataFlightLast;

					// 	// dd($multipleFlightData);
					// 	$flightname = [];
					// 	foreach($multipleFlightData as $multipleFlights){
					// 		$flightname [] = [
					// 			'from_location' => $flightNameArray[$multipleFlights[0]['from_location']],
					// 			'to_location' => $flightNameArray[$multipleFlights[0]['to_location']],

					// 		];
					// 	}
					// 	// dd($flightname);
					// }
					// dd($multipleFlightData);
					// dd($flightNameArray);
					$dropCityId = [];
					foreach ($request->dropCity as $dropcity) {

						$dropCityId[] = $airPortId[$dropcity];
					}

					if (count($request->dropCity)) {
						$i = 0;

						$multipleFlightData = [];
						$flightname = [];
						foreach ($request->dropCity as $key => $dropCityData) {

							$flightname[] = [
								'from_location' => $flightNameArray[($key == 0) ? $request->flyingfrom : $dropCityId[$i - 1]],
								'to_location' => $flightNameArray[$airPortId[$dropCityData]],

							];

							$queryParams = [
								'from' => ($key == 0) ? $request->flyingfrom : $dropCityId[$i - 1],
								'to' => $airPortId[$dropCityData],
								'deptDate' => ($key == 0) ? $request->departureDates : $request->dateDeparture[$i - 1],
								'traveller' => $request->passenger,
							];

							if ($request->sortby != 'undefined') {
								$queryParams['sortBy'] = $request->sortby;
							}

							if ($request->stopFilter != 'undefined') {
								$queryParams['stopFilter'] = $request->stopFilter;
							}

							if ($request->offers_type != 'undefined') {
								$queryParams['offerFilter'] = $request->offers_type;
							}
							if ($request->filled('airlines_type')) {
								$queryParams['airlineFilter'] = implode(",", $request->airlines_type);
							}

							$responseOne = $client->get($this->airlineHost . 'searchFlight', [
								'headers' => [
									'Authorization' => 'Bearer ' . Session::get('logtoken'),
								],
								// 'query' => [
								// 	'from' => ($key == 0) ? $request->flyingfrom :$dropCityId[$i-1],
								// 	'to' => $airPortId[$dropCityData],
								// 	'deptDate' => ($key == 0) ? $request->departureDates : $request->dateDeparture[$i-1],

								// 	'traveller' =>$request->passenger,
								// ],
								'query' => $queryParams,
							]);
							$dataFlightOne = json_decode($responseOne->getBody(), true);
							$multipleFlightData[] = $dataFlightOne;
							$i++;
						}

						$flightname[] = [
							'from_location' => $flightNameArray[$dropCityId[count($request->dropCity) - 1]],
							'to_location' => $flightNameArray[$request->flyingTo],
						];

						$queryParamsLast = [
							'from' => $dropCityId[count($request->dropCity) - 1],
							'to' => $request->flyingTo,
							'deptDate' => $request->dateDeparture[count($request->dateDeparture) - 1],
							'traveller' => $request->passenger,
						];

						if ($request->sortby != 'undefined') {
							$queryParamsLast['sortBy'] = $request->sortby;
						}

						if ($request->stopFilter != 'undefined') {
							$queryParamsLast['stopFilter'] = $request->stopFilter;
						}

						if ($request->offers_type != 'undefined') {
							$queryParamsLast['offerFilter'] = $request->offers_type;
						}
						if ($request->filled('airlines_type')) {
							$queryParamsLast['airlineFilter'] = implode(",", $request->airlines_type);
						}

						$responseLast = $client->get($this->airlineHost . 'searchFlight', [
							'headers' => [
								'Authorization' => 'Bearer ' . Session::get('logtoken'),
							],
							// 'query' => [
							// 	'from' => $dropCityId[count($request->dropCity)-1],
							// 	'to' => $request->flyingTo,
							// 	'deptDate' => $request->dateDeparture[count($request->dateDeparture)-1],
							// 	'traveller' =>$request->passenger,
							// ],
							'query' => $queryParamsLast,
						]);

						$dataFlightLast = json_decode($responseLast->getBody(), true);
						$multipleFlightData[] = $dataFlightLast;

						// dd($multipleFlightData);
						// $flightname = [];
						// foreach($multipleFlightData as $multipleFlights){
						// 	$flightname [] = [
						// 		'from_location' => $flightNameArray[$multipleFlights[0]['from_location']],
						// 		'to_location' => $flightNameArray[$multipleFlights[0]['to_location']],

						// 	];
						// }
						// dd($flightname);
					}
					$dataReturn = '';
					$data = '';
				}

			}

			return view('front.flight.flight-searching', compact('data', 'airportCode', 'trip_type', 'dataReturn', 'multipleFlightData', 'flightname', 'flightNameArray', 'getAirlineDetails'));
		}

	}

	public function step1next(Request $request) {
		// dd($request->all());
		if ($request->nri) {
			$nriCountry = $request->nriCountry;
		} else {
			$nriCountry = null;
		}
		//create pssenger
		$client = new Client();
		$response = $client->post($this->tripHost . 'createPassenger', [
			'headers' => [
				'Authorization' => 'Bearer ' . Session::get('logtoken'),
				'Content-Type' => 'application/json',
			],
			'json' => [
				'salutation' => $request->mr_type,
				'firstName' => $request->firstName,
				'lastName' => $request->lastName,
				'gender' => $request->gender,
				'dob' => $request->dob,
				'mobileNo' => $request->mobileNumber,
				'emailId' => $request->passengerEmail,
				'passport' => $request->passport,
				'visaNo' => $request->visa,
				'validDate' => $request->validDate,
				'occupation' => $request->occupation,
				'passedOut' => $request->passed_out,
				'address' => $request->address,
				'city' => $request->city_name,
				'state' => $request->state_name,
				'address' => $request->address,
				'country' => $request->country_name,
				'nriCountry' => $nriCountry,
				'prefferedClass' => $request->preffere_class,
				'specialClass' => $request->special_class,
				'memberShipAirline' => $request->airline_name,
				'memberShipId' => $request->membership_id,
				'pinNo' => $request->pin_number,
				'graduation' => $request->pin_number,

			],
		]);
		$statusCode = $response->getStatusCode();
		$createPassenger = json_decode($response->getBody(), true);
		// dd($createPassenger['passengerId']);
		

		//passengerDetails updateTrip/userGSTDetails/
		
		//profile image
		if ($request->profile != 'null') {

			$responsefile = $client->request('PATCH', $this->tripHost . 'passenger/upload/' . $createPassenger['passengerId'], [
				'headers' => [
					'Authorization' => 'Bearer ' . Session::get('logtoken'),
					'Accept' => 'application/json',
				],
				'multipart' => [
					[
						'name' => 'file',
						'contents' => fopen($request->file('profile')->getPathname(), 'r'),
						'filename' => $request->file('profile')->getClientOriginalName(),
					],
				],
			]);

			$statusCode = $response->getStatusCode();

			$profileDetails = json_decode($response->getBody(), true);
		}

		return response()->json($createPassenger['passengerId']);
		// return response()->json(['success'=>'.']);
	}
	public function step2next(Request $request) {
		// dd($request->insurance);
		$client = new Client();

		//passengerDetails
		$response = $client->patch($this->tripHost . 'updateTrip/passengerDetails/' . $request->tripDetailsId, [
			'headers' => [
				'Authorization' => 'Bearer ' . Session::get('logtoken'),
				'Content-Type' => 'application/json',
			],
			'json' => [
				'passengerId' => explode(",",$request->totelPassngerId),
			],
		]);
		$statusCode = $response->getStatusCode();
		$passengerDetails = json_decode($response->getBody(), true);
		// dd($passengerDetails);
		//gst details
		$response = $client->request('PATCH', $this->tripHost . 'updateTrip/userGSTDetails/' . $request->tripDetailsId, [
			'headers' => [
				'Authorization' => 'Bearer ' . Session::get('logtoken'),
				'Content-Type' => 'application/json',
			],
			'json' => [
				'gstNumber' => $request->registration,
				'gstName' => $request->company_name,
				'gstAddress' => $request->company_address,
			],
		]);

		$statusCode = $response->getStatusCode();
		$userGSTDetails = json_decode($response->getBody(), true);

		// updateTrip/insurance
		if ($request->insurance) {
			$response = $client->patch($this->tripHost . 'updateTrip/insurance/' . $request->tripDetailsId, [
				'headers' => [
					'Authorization' => 'Bearer ' . Session::get('logtoken'),
					'Content-Type' => 'application/json',
				],
				'json' => [
					'insuranceId' => $request->insurance,
				],
			]);

			$statusCode = $response->getStatusCode();
			$insurance = json_decode($response->getBody(), true);
		}

		//promotion
		if ($request->selectedCouponID) {
			$response = $client->patch($this->tripHost . 'updateTrip/promotion/' . $request->tripDetailsId, [
				'headers' => [
					'Authorization' => 'Bearer ' . Session::get('logtoken'),
					'Content-Type' => 'application/json',
				],
				'json' => [
					'promotionId' => $request->selectedCouponID,
				],
			]);
		}

		$statusCode = $response->getStatusCode();
		$promotion = json_decode($response->getBody(), true);




		return response()->json('success');
		// return response()->json();

	}
	public function step3next(Request $request) {
		// dd($request->all());
		$client = new Client();

		// baggage
		$response = $client->patch($this->tripHost . 'updateTrip/baggage/' . $request->tripDetailsId, [
			'headers' => [
				'Authorization' => 'Bearer ' . Session::get('logtoken'),
				'Content-Type' => 'application/json',
			],
			'json' => [
				'noofBags' => $request->numberbags,
				'totalWeight' => $request->totalweight,

			],
		]);

		$statusCode = $response->getStatusCode();
		$baggage = json_decode($response->getBody(), true);

		// hotel
		$response = $client->patch($this->tripHost . 'updateTrip/hotel/' . $request->tripDetailsId, [
			'headers' => [
				'Authorization' => 'Bearer ' . Session::get('logtoken'),
				'Content-Type' => 'application/json',
			],
			'json' => [
				'hotelId' =>$request->hotel_name,
				// 'hotelId' => 1,
				'noOfRooms' => $request->no_rooms,
				'noOfAdult' => $request->no_adults,
				'noOfChild' => $request->no_child,
				'checkinDate' => $request->check_in,
				'checkoutDate' => $request->check_out,

			],
		]);

		$statusCode = $response->getStatusCode();
		$hotel = json_decode($response->getBody(), true);
		// dd($hotel);

		// return response()->json(['success'=>'.']);
		return response()->json('success');

	}
	public function step4next(Request $request) {

		// itinerary
		$client = new Client();
		if ($request->itinerary_name) {
			$response = $client->patch($this->tripHost . 'updateTrip/itinerary/' . $request->tripDetailsId, [
				'headers' => [
					'Authorization' => 'Bearer ' . Session::get('logtoken'),
					'Content-Type' => 'application/json',
				],
				'json' => [
					'itineraryNames' => is_array($request->itinerary_name) ? $request->itinerary_name : [$request->itinerary_name],
					'noOfTickets' => is_array($request->number_ticket) ? $request->number_ticket : [$request->number_ticket],

				],
			]);

			$statusCode = $response->getStatusCode();
			$itinerary = json_decode($response->getBody(), true);
		}

		if ($request->meals) {
			$mealName = [];
			$mealCount = [];
			foreach ($request->meals as $meals) {
				$mealName[] = $meals['text'];
				$mealCount[] = $meals['count'];
			}
			// dd($mealName);
			$response = $client->patch($this->tripHost . 'updateTrip/meal/' . $request->tripDetailsId, [
				'headers' => [
					'Authorization' => 'Bearer ' . Session::get('logtoken'),
					'Content-Type' => 'application/json',
				],
				'json' => [
					'mealName' => $mealName,
					'noOfMeals' => $mealCount,

				],
			]);

			$statusCode = $response->getStatusCode();
			$meal = json_decode($response->getBody(), true);
		}
		if ($request->meals) {
			$response = $client->patch($this->tripHost . 'updateTrip/cars/' . $request->tripDetailsId, [
				'headers' => [
					'Authorization' => 'Bearer ' . Session::get('logtoken'),
					'Content-Type' => 'application/json',
				],
				'json' => [
					'carNames' => [implode(",", $request->car_name)],

				],
			]);

			$statusCode = $response->getStatusCode();
			$cars = json_decode($response->getBody(), true);
		}

		// dd($cars);
		return response()->json('success');
	}
	public function step5next(Request $request) {
		// dd($request->all());
		$client = new Client();

		$seatingArray = [];
		foreach ($request->seat_number as $key => $seatNumber) {

			$seatingArray[] = [
				'routeId' => $request->flightSeatingRoutId[$key],
				'seatingName' => $seatNumber,
			];
		}

		$response = $client->patch($this->tripHost . 'updateTrip/seating/' . $request->tripDetailsId, [
			'headers' => [
				'Authorization' => 'Bearer ' . Session::get('logtoken'),
				'Content-Type' => 'application/json',
			],
			'json' => [
				'routeSeats' => $seatingArray,

			],
		]);

		$statusCode = $response->getStatusCode();
		$seating = json_decode($response->getBody(), true);

		// dd($seating);
		
		foreach($request->flightSeatingRoutId as $key => $routId){
			$response = $client->post($this->airlineHost . 'blockAirlineSeats/'.$routId, [

				'headers' => [
					'Authorization' => 'Bearer ' . Session::get('logtoken'),
					'Content-Type' => 'application/json',
				],
				'json' => [
					'seatNames' => $request->seat_number[$key],

				],
			]);
			$statusCode = $response->getStatusCode();
			$blockAirlineSeats = json_decode($response->getBody(), true);
			// dd($blockAirlineSeats);
		}
		
		// return response()->json('success');
		return response()->json($seating);
		
	}
	public function releaseAirlineSeats(Request $request) {
		// dd($request->all());
		
		$client = new Client();
		foreach ($request->flightSeatingRoutId as $key => $routId) {
			$response = $client->post($this->airlineHost . 'releaseAirlineSeats/' . $routId, [
				'headers' => [
					'Authorization' => 'Bearer ' . Session::get('logtoken'),
					'Content-Type' => 'application/json',
				],
				'json' => [
					'seatNames' => $request->seat_number[$key],
				],
			]);
			$statusCode = $response->getStatusCode();
			$releaseAirlineSeats = json_decode($response->getBody(), true);
			// dd($releaseAirlineSeats);

		}
		return response()->json('success');

	}

	
	public function bookingConfirmed(Request $request) {
		// dd($request->all());
		$client = new Client();
		if($request->payment_method == 'upi'){
			$response = $client->post($this->tripHost . 'createBooking', [
				'headers' => [
					'Authorization' => 'Bearer ' . Session::get('logtoken'),
					'Content-Type' => 'application/json',
				],
				'json' => [
					'tripDetailsId' => $request->tripDetailsId,
					'payment_method' => $request->payment_method,
					'upi' => $request->upi,
					
	
				],
	
			]);
			$statusCode = $response->getStatusCode();
			$createBooking = json_decode($response->getBody(), true);
			// dd($createBooking);
		}else{
			$response = $client->post($this->tripHost . 'createBooking', [
				'headers' => [
					'Authorization' => 'Bearer ' . Session::get('logtoken'),
					'Content-Type' => 'application/json',
				],
				'json' => [
					'tripDetailsId' => $request->tripDetailsId,
					'payment_method' => $request->payment_method,
					'card_no' => $request->card_no,
					'payment_type' => $request->payment_type,
					'card_type' => $request->card_type,
					'cvv' => $request->cvv,
					'expiry_date' => $request->card_year.'-'.$request->card_month,
					'card_name' => $request->card_name,
	
				],
	
			]);
			$statusCode = $response->getStatusCode();
			$createBooking = json_decode($response->getBody(), true);
		}
		
		// dd($createBooking);

		// http://{{host}}:8086/omrbranch/createBooking
		if($createBooking['bookingStatus'] == 'Confirmed'){
			return view('front.flight.booking-confirmed',compact('createBooking'));
		}
		
	}

	public function viewBooking(Request $request, $no) {
				
		$client = new Client();
		$response = $client->get($this->bookingHost . 'bookings/'.$no, [
			'headers' => [
				'Authorization' => 'Bearer ' . Session::get('logtoken'),
			],
		]);
		$bookings = json_decode($response->getBody(), true);
		// dd($bookings['routeIds'][0]);
		
		$flightDetails =[];
		for($i=0;$i<count($bookings['routeIds']) ; $i++){
			$flightDetails [] = [
				'routeIds' => $bookings['routeIds'][$i],
				'deptDates' => $bookings['deptDates'][$i],
				'serviceDaysIds' => $bookings['serviceDaysIds'][$i],
				'airlineNames' => $bookings['airlineNames'][$i],
				'fromlocations' => $bookings['fromlocations'][$i],
				'tolocations' => $bookings['tolocations'][$i],
				'depatureTimes' => $bookings['depatureTimes'][$i],
				'arrivalTimes' => $bookings['arrivalTimes'][$i],
				'travelDurations' => $bookings['travelDurations'][$i],
				'totalSeats' => $bookings['totalSeats'][$i],
				'originalPrices' => $bookings['originalPrices'][$i],
				'offerPrices' => $bookings['offerPrices'][$i],
				'stopDetails' => $bookings['stopDetails'][$i],
				'serviceDays' => $bookings['serviceDays'][$i],
				// 'totalSeats' => $bookings['totalSeats'][$i],
			];
		}
		// dd($flightDetails);
		$passengerDetails =[];
		foreach($bookings['passengersId'] as $passengersId){

			$response = $client->get($this->tripHost . 'passenger/'.$passengersId, [
				'headers' => [
					'Authorization' => 'Bearer ' . Session::get('logtoken'),
				],
			]);
			$passenger = json_decode($response->getBody(), true);
			$passengerDetails []= $passenger ;
		}
		
		$itineraryArray = [];
		if (isset($bookings['itinerary']) && is_array($bookings['itinerary'])) {
			foreach ($bookings['itinerary'] as $key => $itineraryData) {
				$itineraryArray[] = [
					'itineraryName' => $itineraryData,
					'itineraryCount' => isset($bookings['itineraryCount'][$key]) ? $bookings['itineraryCount'][$key] : null,
				];
			}
		}

		$mealArray = [];
		if (isset($bookings['mealName']) && is_array($bookings['mealName'])) {
			foreach ($bookings['mealName'] as $key => $mealData) {
				$mealArray[] = [
					'mealName' => $mealData,
					'mealCount' => isset($bookings['noOfMeals'][$key]) ? $bookings['noOfMeals'][$key] : null,
				];
			}
		}
		// dd($mealArray);
		$response = $client->get($this->airlineHost . 'getAirports', [
			'headers' => [
				'Authorization' => 'Bearer ' . Session::get('logtoken'),
			],
		]);

		$apiData = json_decode($response->getBody(), true);

		$flightNameArray = [];
		foreach ($apiData as $api_data) {
			$flightNameArray[$api_data['airportId']] = $api_data['airportCode'];
		}
		// dd($passengerDetails)
		return view('front.flight.view-booking',compact('bookings','passengerDetails','flightDetails','flightNameArray','itineraryArray','mealArray'));
	}

	
	public function editBooking(Request $request, $id) {
		// dd($id);
		$client = new Client();
		$response = $client->get($this->bookingHost . 'bookings/'.$id, [
			'headers' => [
				'Authorization' => 'Bearer ' . Session::get('logtoken'),
			],
		]);
		$book = json_decode($response->getBody(), true);
		// dd($book);
		$currency = BusRuleRef::where('rule_name', 'currency')->first()->rule_value;
		$getAirlines = $client->get($this->airlineHost . 'getAirlines', [
			'headers' => [
				'Authorization' => 'Bearer ' . Session::get('logtoken'),
			],
		]);

		$getAirlines = json_decode($getAirlines->getBody(), true);

		$getAirlineDetails = [];
		foreach ($getAirlines as $key => $get_airline) {
			$getAirlineDetails[$get_airline['airlineId']] = [
				'airlineName' => $get_airline['airlineName'],
				'airlineLogo' => $get_airline['airlineLogo'],
			];
		}

		$response = $client->get($this->airlineHost . 'getAirports', [
			'headers' => [
				'Authorization' => 'Bearer ' . Session::get('logtoken'),
			],
		]);

		$apiData = json_decode($response->getBody(), true);

		$flightNameArray = [];
		foreach ($apiData as $api_data) {
			$flightNameArray[$api_data['airportId']] = $api_data['airportCode'];
		}

		return view('front.flight.edit-booking', compact('book', 'currency','getAirlineDetails','flightNameArray'));
		// if (isset($book->id) && $book->status == 'pending') {
		// 	$currency = BusRuleRef::where('rule_name', 'currency')->first()->rule_value;
		// 	return view('front.travel.edit-booking', compact('book', 'currency'));
		// } else {
		// 	$request->session()->flash('alertdanger', 'You cannot edit this booking now');
		// 	return redirect()->back();
		// }
	}

	public function updateBooking(Request $request, $id) {
		
		$client = new Client();
		$response = $client->post($this->bookingHost . 'updateBookingDate/'.$id, [
			'headers' => [
				'Authorization' => 'Bearer ' . Session::get('logtoken'),
				'Content-Type' => 'application/json',
			],
			'json' => [
				'deptDate' => $request->DeptDate,
			],
		]);
		$statusCode = $response->getStatusCode();
		
		
		if ($response->getStatusCode() == 200) {
			$updateBookingDate = json_decode($response->getBody(), true);
			$request->session()->flash(($response->getStatusCode() == 200) ? 'alertsuccess' : 'alertdanger', 'Your DeptDate update successfully');
			return redirect()->route('flight-my-bookings');
		} else {
			$request->session()->flash('alertdanger', 'Something went wrong. Please try again later!');
				return redirect()->back();
		}
		
	}

	//get state
	public function listState(Request $request) {
		if ($request->country_id) {
			$stateList = $this->api->stateList($request);
			return response()->json($stateList['data']);
		} else {
			$stateList['data'] = '';
			return response()->json($stateList['data']);
		}

	}

	public function listCity(Request $request) {
		if ($request->state_id) {
			$cityList = $this->api->cityList($request);
			return response()->json($cityList['data']);
		} else {
			$cityList['data'] = '';
			return response()->json($cityList['data']);
		}

	}
	//hotel Name
	public function hotelName(Request $request) {
		$data = $this->hotelapi->getAllHotels($request);
		// dd($data['data']);
		return response()->json($data['data']);
	}

	// Cancel Booking
	public function cancelBooking(Request $request, $id) {
		
		$client = new Client();

		$response = $client->delete($this->bookingHost . 'cancelBookingDate/' . $id, [
			'headers' => [
				'Authorization' => 'Bearer ' . Session::get('logtoken'),
			],
		]);

		if ($response->getStatusCode() == 200) {
			$apiData = json_decode($response->getBody(), true);
			
			$request->session()->flash(($response->getStatusCode() == 200) ? 'alertsuccess' : 'alertdanger', 'Your booking cancelled successfully');
			return redirect()->back();
		} else {
			$request->session()->flash('alertdanger', 'Something went wrong. Please try again later!');
				return redirect()->back();
		}

	}

	// Web-Checkin
	public function webCheckin(Request $request, $id){
		
		$client = new Client();

		$response = $client->post($this->bookingHost . 'webcheckin/' . $id, [
			'headers' => [
				'Authorization' => 'Bearer ' . Session::get('logtoken'),
				'Content-Type' => 'application/json',
			],
			'json' => [
				'webCheckin' => true,
			],
		]);

		if ($response->getStatusCode() == 200) {
			$apiData = json_decode($response->getBody(), true);
			
			$request->session()->flash(($response->getStatusCode() == 200) ? 'alertsuccess' : 'alertdanger', 'Your web-Checkin successfully');
			return redirect()->back();
		} else {
			$request->session()->flash('alertdanger', 'Something went wrong. Please try again later!');
				return redirect()->back();
		}
	}

	// public function getBookings(Request $request, $status = null) {
		
	// 	if ($status == 'confirmed') {
	// 		$request->merge(['status' => 'confirmed']);
	// 	}

	// 	$data = $this->api->getAllBookings($request);

	// 	if (isset($data['status'])) {
	// 		if ($status == 'confirmed') {
	// 			$confirmed = $data['data'];
	// 			$currency = $data['currency'];
	// 			echo view('front.travel.confirmed-bookinglist', compact('confirmed'))->render();
	// 		} else {
	// 			dd($request->all());
	// 			$bookings = $data['data'];
	// 			$currency = $data['currency'];
	// 			echo view('front.travel.all-bookinglist', compact('bookings'))->render();
	// 		}
	// 	} else {
	// 		echo 'error';
	// 	}
	// }
}

