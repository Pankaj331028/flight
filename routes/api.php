<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::post("login", "ApiController@login");
Route::get("updateCart", "ApiController@updateCart");

Route::middleware(['custombasicauth'])->group(function () {
	Route::post("postmanBasicAuthLogin", "ApiController@apilogin");
});

Route::post("userRegister", "ApiController@userRegister");
Route::post("checkSocialUser", "ApiController@checkSocialUser");
Route::post("socialLogin", "ApiController@socialLogin");
Route::post("forgotPassword", "ApiController@forgotPassword");
Route::get("stateList", "ApiController@stateList");
Route::get("countryList", "ApiController@countryList");
Route::post("cityList", "ApiController@cityList");
Route::post("locationList", "ApiController@locationList");
Route::get("categoryList", "ApiController@categoryList");
Route::post("productList", "ApiController@productList");
Route::post("searchProduct", "ApiController@search");

// Route::post("setCartAddress", "ApiController@setCartAddress");

Route::post("trackOrderNo", "ApiController@trackOrderNo");
Route::get("allowSocial", "ApiController@allowSocial");
// test apis
Route::post("sendTestNotification", "ApiController@sendTestNotification");
// new apis
// driver apis
Route::post('getDriverOrders', 'ApiController@getDriverOrders');
Route::post('getDriverOrderDetails', 'ApiController@getDriverOrderDetails');
Route::post('getDriverHomePage', 'ApiController@getDriverHomePage');
Route::post('updateOrderStatus', 'ApiController@updateOrderStatus');
Route::post('sendTestMail', 'ApiController@sendTestmail');
Route::post('sendOtp', 'ApiController@sendOtp')->name('sendOtp');
Route::post('ticket-generate-send-otp', 'ApiController@ticketSendOtp')->name('ticketSendOtp');
Route::post("ticketSubmit", "ApiController@ticketSubmit");
// Route::post("login", "ApiController@login");
Route::post("loginWithApiKey", "ApiController@login_api_key");
Route::post("accessToken", "ApiController@access_token");
Route::post("refreshToken", "ApiController@refresh_token");

// sample apis
Route::get('flights', 'SampleApiController@getFlights');
Route::get('flight/{id}', 'SampleApiController@getSingleFlight');
Route::post('flights', 'SampleApiController@createFlight');
Route::put('flight/{id}', 'SampleApiController@updateFlight');
Route::patch('flight/{id}', 'SampleApiController@updateFlight');
Route::delete('flight/{id}', 'SampleApiController@deleteFlight');

Route::group(['middleware' => 'auth:api'], function () {

	Route::get("getUserAddress", "ApiController@getUserAddress");
	Route::get("getUserDetail", "ApiController@getUserDetail");
    
	Route::get("getUserWallet", "ApiController@getUserWallet");
	Route::get("getCartItems", "ApiController@getCartItems");
	Route::post("setAddress", "ApiController@setAddress");
	Route::post("setCartWallet", "ApiController@setCartWallet");
	Route::post("addToCart", "ApiController@addToCart");
	Route::post("applyCouponCode", "ApiController@applyCouponCode");
	Route::get('getUserFavourites', "ApiController@getUserFavourites");
	Route::post("homePageData", "ApiController@homePageData");
	Route::post("getSearchResult", "ApiController@getSearchResult");
	Route::get("getNotifications", "ApiController@getNotifications");
	Route::post("getOfferDetails", "ApiController@getOfferDetails");
	Route::post("getAllOffers", "ApiController@getAllOffers");
	Route::post("getExcProducts", "ApiController@getExcProducts");
	Route::post("getDateProducts", "ApiController@getDateProducts");
	Route::post("getProductDescription", "ApiController@getProductDescription");
	Route::post("changeProfilePic", "ApiController@changeProfilePic");
	Route::put("changePassword", "ApiController@changePassword");
	Route::put("updateProfile", "ApiController@updateProfile");
	Route::delete("deleteCartItems", "ApiController@deleteCartItems");
	Route::post("createOrder", "ApiController@createOrder");
	Route::delete("deleteCouponCode", "ApiController@deleteCouponCode");
	Route::post("getDeliverySlots", "ApiController@getDeliverySlots");
	Route::post("setProductDateTime", "ApiController@setProductDateTime");
	Route::post("setCartPayment", "ApiController@setCartPayment");
	Route::post('manageUserFavourite', "ApiController@manageUserFavourite");
	Route::post("submitRating", "ApiController@submitRating");
	Route::put("updateUserAddress", "ApiController@updateUserAddress");
	Route::post("addUserAddress", "ApiController@addUserAddress");

	Route::post("cancelOrder", "ApiController@cancelOrder");
	Route::post("rescheduleOrderItem", "ApiController@rescheduleOrderItem");
	Route::post("getOrderStatus", "ApiController@getOrderStatus");
	Route::get("getAllOrders", "ApiController@getAllOrders");
	// Route::post("trackOrder/{order_id}/{item_id}", "ApiController@trackOrder");
	Route::get("trackOrder/{order_id}/{item_id}", "ApiController@trackOrder");
	Route::post("getOrderItemStatus", "ApiController@getOrderItemStatus");
	// Route::post("getOrderDetails", "ApiController@getOrderDetails");
	Route::get("getOrderDetails", "ApiController@getOrderDetails");

	Route::get("clearCart", "ApiController@clearCart");
	Route::put("updateDevice", "ApiController@updateDevice");
	Route::post("userDetail", "ApiController@userDetail");
	Route::post("logout", "ApiController@logout");
	Route::delete("deleteAddress", "ApiController@deleteAddress");

	//Travels Api
	Route::post("hotels", "HotelApiController@getAllHotels");
	Route::get("getHotelStates", "HotelApiController@getHotelStates");
	Route::get("getHotelCities/{state}", "HotelApiController@getHotelCities");
	Route::get("getHotelDetails/{hotel_id}", "HotelApiController@getHotelDetails");
	Route::get("bookings", "HotelApiController@getAllBookings");
	Route::get("bookings-details", "HotelApiController@getSingleBookings");
	Route::delete("cancel-booking", "HotelApiController@cancelBooking");
	Route::post("create-booking", "HotelApiController@bookHotel");
	// pj changes
	Route::post("bookingcreatestepone", "HotelApiController@bookingcreatestepone");
	Route::get("bookingwebview/{booking_id}", "HotelApiController@bookingwebview");

	Route::put("update-booking", "HotelApiController@updateBooking");

});

// Route::post('otp-expire', 'ApiController@otpExpire')->name('otpExpire'); // set time interval
