<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

use App\Model\Role;
use App\Model\User;
use Illuminate\Support\Facades\Artisan;
// use Session;

// use Illuminate\Routing\Route;
// use PDF;

Route::get('create-userrole', function () {
	$users = User::whereIn('status', ['AC', 'IN'])->doesntHave('user_role')->get();

	foreach ($users as $user) {

		$user->user_role()->sync(
			[Role::whereRole('user')->firstOrFail()->id]
		);
	}
});

Route::get('/clear-cache', function () {
	Artisan::call('config:clear');
	Artisan::call('view:clear');
	Artisan::call('route:clear');
	Artisan::call('cache:clear');
});

Route::get('/resetPassword', ['uses' => 'LoginController@resetPasswordApp'])->name('resetGetApp');
Route::post('/resetPassword', ['uses' => 'LoginController@resetPasswordApp'])->name('resetPostApp');
Route::get('/success', ['uses' => 'LoginController@afterAppReset'])->name('appIndex');
// Route::get('/', 'LoginController@home')->name('home');
Route::get('/privacy-policy', 'CMSController@privacypolicy');

Route::get('/verify_email', ['uses' => 'UserController@verifyEmail'])->name('verifyEmail');
Route::get('/payment', ['uses' => 'TransactionsController@payment'])->name('payment');
Route::post('/ccavRequestHandler', ['uses' => 'TransactionsController@ccavRequestHandler'])->name('ccavRequestHandler');
Route::post('/payment-success', ['uses' => 'TransactionsController@paymentSuccess'])->name('paymentSuccess');
Route::post('/payment-failure', ['uses' => 'TransactionsController@paymentFailure'])->name('paymentFailure');

Route::get('/chat-support', function () {
	return view('chat-support');
});

Route::prefix('admin')->group(function () {
	Route::get('/', ['middleware' => ['adminAuth', 'shareMiddleware'], 'uses' => 'LoginController@index'])->name('index');
	Route::post('/', ['uses' => 'LoginController@login'])->name('login');
	Route::get('/logout', ['middleware' => ['adminAuth', 'shareMiddleware'], 'uses' => 'LoginController@logout'])->name('logout');
	Route::post('/getBarGraphData', ['middleware' => ['adminAuth', 'shareMiddleware'], 'uses' => 'LoginController@getBarGraphData'])->name('getBarGraphData');
	Route::post('/getPaymentGraphData', ['middleware' => ['adminAuth', 'shareMiddleware'], 'uses' => 'LoginController@getPaymentGraphData'])->name('getPaymentGraphData');
	Route::post('/getCancelGraphData', ['middleware' => ['adminAuth', 'shareMiddleware'], 'uses' => 'LoginController@getCancelGraphData'])->name('getCancelGraphData');
	Route::get('/change-password/edit', ['middleware' => ['adminAuth', 'shareMiddleware'], 'uses' => 'LoginController@changePassword'])->name('changepassword');
	Route::post('/change-password', ['middleware' => ['adminAuth', 'shareMiddleware'], 'uses' => 'LoginController@changePassword'])->name('changepasswordPost');
	Route::get('/forgot-password', ['uses' => 'LoginController@sendPasswordMail'])->name('forgotGet');
	Route::post('/forgot-password', ['uses' => 'LoginController@sendPasswordMail'])->name('forgotPost');
	Route::get('/reset-password', ['uses' => 'LoginController@resetPassword'])->name('resetGet');
	Route::post('/reset-password', ['uses' => 'LoginController@resetPassword'])->name('resetPost');

	Route::middleware(['loginAuth', 'auth:admin', 'adminAuth', 'shareMiddleware'])->group(function () {

		// Index and dashboard
		Route::get('/dashboard', ['uses' => 'LoginController@index'])->name('dashboard');

		//Hotel Bookings
		Route::get('/bookings/list', ['uses' => 'BookingController@index'])->name('bookings');
		Route::get('/bookingAjax', ['uses' => 'BookingController@bookingAjax'])->name('bookingAjax');
		Route::post('/booking-status/{id}', ['uses' => 'BookingController@bookingStatus'])->name('booking-status');

		//Users Module
		Route::get('/users/list', ['uses' => 'UserController@index'])->name('users');
		Route::get('/userAjax', ['uses' => 'UserController@userAjax'])->name('userAjax');

		// Route::get('/user/add', ['uses' => 'UserController@create'])->name('createUser');
		// Route::get('/users/checkUser/{id?}', ['uses' => 'UserController@checkUser'])->name('checkUser');
		// Route::post('/user/store', ['uses' => 'UserController@store'])->name('addUser');
		Route::get('/user/view/{id?}', ['uses' => 'UserController@show'])->name('viewUser');
		Route::get('/user/edit/{id?}', ['uses' => 'UserController@edit'])->name('editUser');
		Route::post('/user/update/{id?}', ['uses' => 'UserController@update'])->name('updateUser');
		Route::post('/user/changeStatus', ['uses' => 'UserController@updateStatus'])->name('changeStatusUser');
		// Route::post('/user/changeVerifyStatus', ['uses' => 'UserController@changeVerifyStatus'])->name('changeVerifyStatusUser');
		Route::post('/user/changeStatusAjax', ['uses' => 'UserController@updateStatusAjax'])->name('changeStatusAjaxUser');
		Route::post('/user/changeVerifyStatusAjaxUser', ['uses' => 'UserController@updateVerifyStatusAjax'])->name('changeVerifyStatusAjaxUser');
		Route::post('/user/changeTravelAcessAjaxUser', ['uses' => 'UserController@updateTravelAccessAjax'])->name('changeTravelAcessAjaxUser');
		Route::post('/user/changeFlightAcessAjaxUser', ['uses' => 'UserController@updateFlightAccessAjax'])->name('changeFlightAcessAjaxUser');
		Route::post('/user/changeGroceryWithApiAcessAjaxUser', ['uses' => 'UserController@updateGroceryWithApiAccessAjax'])->name('changeGroceryWithApiAcessAjaxUser');
		Route::post('/user/changeGroceryAcessAjaxUser', ['uses' => 'UserController@updateGroceryAccessAjax'])->name('changeGroceryAcessAjaxUser');
		Route::post('/user/changeAdminPanelAcessAjaxUser', ['uses' => 'UserController@updateAdminPanelAccessAjax'])->name('changeAdminPanelAcessAjaxUser');
		Route::post('/user/changeMobileAppAcessAjaxUser', ['uses' => 'UserController@updateMobileAppAccessAjax'])->name('changeMobileAppAcessAjaxUser');
		Route::post('/user/changeDatabaseAcessAjaxUser', ['uses' => 'UserController@updateDatabaseAccessAjax'])->name('changeDatabaseAcessAjaxUser');
		Route::post('/user/changeSampleApiAcessAjaxUser', ['uses' => 'UserController@updateSampleApiAccessAjax'])->name('changeSampleApiAcessAjaxUser');

		Route::get('/user/delete/{id?}', ['uses' => 'UserController@delete'])->name('deleteUser');
		Route::get('/user/bulkdelete', ['uses' => 'UserController@bulkdelete'])->name('deleteUsers');
		Route::post('/user/bulkchangeStatus', ['uses' => 'UserController@bulkchangeStatus'])->name('changeStatusUsers');

		//Store state and city session throughout the admin panel
		Route::post('/getState', ['uses' => 'StateCityController@fetchStates'])->name('getState');
		Route::post('/getCity', ['uses' => 'StateCityController@fetch'])->name('getCity');
		Route::post('/setCitySession', ['uses' => 'StateCityController@setSession'])->name('setCitySession');
		Route::post('/resetCitySession', ['uses' => 'StateCityController@resetSession'])->name('resetCitySession');

		//Setting Module
		Route::get('/settings/edit', ['uses' => 'BusRuleRefController@index'])->name('setting');
		Route::get('/setting/store', ['uses' => 'BusRuleRefController@store'])->name('storeSetting');

		//Role Module
		Route::get('/roles/list', ['uses' => 'RoleController@index'])->name('roles');
		Route::get('/roleAjax', ['uses' => 'RoleController@roleAjax'])->name('roleAjax');
		Route::get('/roles/add', ['uses' => 'RoleController@create'])->name('createRole');
		Route::get('/roles/checkRole/{id?}', ['uses' => 'RoleController@checkRole'])->name('checkRole');
		Route::post('/roles/store', ['uses' => 'RoleController@store'])->name('addRole');
		Route::get('/roles/view/{id?}', ['uses' => 'RoleController@show'])->name('viewRole');
		Route::get('/roles/edit/{id?}', ['uses' => 'RoleController@edit'])->name('editRole');
		Route::post('/roles/update/{id?}', ['uses' => 'RoleController@update'])->name('updateRole');
		Route::post('/roles/update_status', ['uses' => 'RoleController@updateStatus'])->name('changeStatusRole');
		Route::post('/roles/update_statusAjax', ['uses' => 'RoleController@updateStatusAjax'])->name('changeStatusAjaxRole');
		Route::post('/roles/delete', ['uses' => 'RoleController@destroy'])->name('deleteRole');
		Route::post('/roles/bulkdelete', ['uses' => 'RoleController@bulkdelete'])->name('deleteRoles');
		Route::post('/roles/bulkupdate_status', ['uses' => 'RoleController@bulkchangeStatus'])->name('changeStatusRoles');

		//Staff Module
		Route::get('/staffs/list', ['uses' => 'StaffController@index'])->name('staffs');
		Route::get('/staffAjax', ['uses' => 'StaffController@staffAjax'])->name('staffAjax');
		Route::get('/staffs/add', ['uses' => 'StaffController@create'])->name('createStaff');
		Route::get('/staffs/checkStaff/{id?}', ['uses' => 'StaffController@checkStaff'])->name('checkStaff');
		Route::post('/staffs/store', ['uses' => 'StaffController@store'])->name('addStaff');
		Route::get('/staffs/view/{id?}', ['uses' => 'StaffController@show'])->name('viewStaff');
		Route::get('/staffs/edit/{id?}', ['uses' => 'StaffController@edit'])->name('editStaff');
		Route::post('/staffs/update/{id?}', ['uses' => 'StaffController@update'])->name('updateStaff');
		Route::post('/staffs/update_status', ['uses' => 'StaffController@updateStatus'])->name('changeStatusStaff');
		Route::post('/staffs/update_statusAjax', ['uses' => 'StaffController@updateStatusAjax'])->name('changeStatusAjaxStaff');
		Route::post('/staffs/delete', ['uses' => 'StaffController@destroy'])->name('deleteStaff');
		Route::post('/staffs/bulkdelete', ['uses' => 'StaffController@bulkdelete'])->name('deleteStaffs');
		Route::post('/staffs/bulkupdate_status', ['uses' => 'StaffController@bulkchangeStatus'])->name('changeStatusStaffs');

		// Driver Module
		Route::get('/drivers/list', ['uses' => 'DriverController@index'])->name('drivers');
		Route::get('/driverAjax', ['uses' => 'DriverController@driverAjax'])->name('driverAjax');
		Route::get('/drivers/add', ['uses' => 'DriverController@create'])->name('createDriver');
		Route::get('/drivers/checkDriver/{id?}', ['uses' => 'DriverController@checkDriver'])->name('checkDriver');
		Route::get('/drivers/checkPhone/{id?}', ['uses' => 'DriverController@checkPhone'])->name('checkPhone');
		Route::post('/drivers/store', ['uses' => 'DriverController@store'])->name('addDriver');
		Route::get('/drivers/view/{id?}', ['uses' => 'DriverController@show'])->name('viewDriver');
		Route::get('/drivers/edit/{id?}', ['uses' => 'DriverController@edit'])->name('editDriver');
		Route::post('/drivers/update/{id?}', ['uses' => 'DriverController@update'])->name('updateDriver');
		Route::post('/drivers/update_status', ['uses' => 'DriverController@updateStatus'])->name('changeStatusDriver');
		Route::post('/drivers/update_statusAjax', ['uses' => 'DriverController@updateStatusAjax'])->name('changeStatusAjaxDriver');
		Route::post('/drivers/delete', ['uses' => 'DriverController@destroy'])->name('deleteDriver');
		Route::post('/drivers/bulkdelete', ['uses' => 'DriverController@bulkdelete'])->name('deletedrivers');
		Route::post('/drivers/bulkupdate_status', ['uses' => 'DriverController@bulkchangeStatus'])->name('changeStatusDrivers');

		//CMS Module
		Route::get('/cms/list', ['uses' => 'CMSController@index'])->name('cms');
		Route::get('/cmsAjax', ['uses' => 'CMSController@cmsAjax'])->name('cmsAjax');
		Route::get('/cms/view/{id?}', ['uses' => 'CMSController@show'])->name('viewCMS');
		Route::get('/cms/edit/{id?}', ['uses' => 'CMSController@edit'])->name('editCMS');
		Route::post('/cms/update/{id?}', ['uses' => 'CMSController@update'])->name('updateCMS');
		Route::post('/cms/deleteFAQ/{id?}', ['uses' => 'CMSController@destroy'])->name('deleteFAQ');

		//Units Module
		Route::get('/units/list', ['uses' => 'UnitController@index'])->name('units');
		Route::get('/unitsAjax', ['uses' => 'UnitController@unitsAjax'])->name('unitsAjax');
		Route::get('/units/add', ['uses' => 'UnitController@create'])->name('createUnit');
		Route::get('/units/checkUnit/{id?}', ['uses' => 'UnitController@checkUnit'])->name('checkUnit');
		Route::post('/units/store', ['uses' => 'UnitController@store'])->name('addUnit');
		// Route::get('/units/view/{id?}', ['uses' => 'UnitController@show'])->name('viewUnit');
		Route::get('/units/edit/{id?}', ['uses' => 'UnitController@edit'])->name('editUnit');
		Route::post('/units/update/{id?}', ['uses' => 'UnitController@update'])->name('updateUnit');
		Route::post('/units/update_status', ['uses' => 'UnitController@updateStatus'])->name('changeStatusUnit');
		Route::post('/units/update_statusAjax', ['uses' => 'UnitController@updateStatusAjax'])->name('changeStatusAjaxUnit');
		Route::post('/units/delete', ['uses' => 'UnitController@destroy'])->name('deleteUnit');
		Route::post('/units/bulkdelete', ['uses' => 'UnitController@bulkdelete'])->name('deleteUnits');
		Route::post('/units/bulkupdate_status', ['uses' => 'UnitController@bulkchangeStatus'])->name('changeStatusUnits');

		//Category/Subcategory Module
		Route::get('/categories/list', ['uses' => 'CategoryController@index'])->name('categories');
		Route::get('/categoryAjax', ['uses' => 'CategoryController@categoryAjax'])->name('categoryAjax');
		Route::get('/categories/add', ['uses' => 'CategoryController@create'])->name('createCategory');
		Route::post('/categories/checkCategory/{id?}', ['uses' => 'CategoryController@checkCategory'])->name('checkCategory');
		Route::post('/categories/store', ['uses' => 'CategoryController@store'])->name('addCategory');
		Route::get('/categories/view/{id?}', ['uses' => 'CategoryController@show'])->name('viewCategory');
		Route::get('/categories/edit/{id?}', ['uses' => 'CategoryController@edit'])->name('editCategory');
		Route::post('/categories/update/{id?}', ['uses' => 'CategoryController@update'])->name('updateCategory');
		Route::post('/categories/update_status', ['uses' => 'CategoryController@updateStatus'])->name('changeStatusCategory');
		Route::post('/categories/update_statusAjax', ['uses' => 'CategoryController@updateStatusAjax'])->name('changeStatusAjaxCategory');
		Route::post('/categories/delete', ['uses' => 'CategoryController@destroy'])->name('deleteCategory');
		Route::post('/categories/bulkdelete', ['uses' => 'CategoryController@bulkdelete'])->name('deleteCategories');
		Route::post('/categories/bulkupdate_status', ['uses' => 'CategoryController@bulkchangeStatus'])->name('changeStatusCategories');
		Route::post('/categories/fetchCategoryOffer', ['uses' => 'CategoryController@fetchCategoryOffer'])->name('fetchCategoryOffer');

		//Product Brand Module
		/*Route::get('/brands/list', ['uses' => 'BrandController@index'])->name('brands');
	        Route::get('/brandAjax', ['uses' => 'BrandController@brandAjax'])->name('brandAjax');
	        Route::get('/brands/add', ['uses' => 'BrandController@create'])->name('createBrand');
	        Route::get('/brands/checkBrand/{id?}', ['uses' => 'BrandController@checkBrand'])->name('checkBrand');
	        Route::post('/brands/store', ['uses' => 'BrandController@store'])->name('addBrand');
	        Route::get('/brands/view/{id?}', ['uses' => 'BrandController@show'])->name('viewBrand');
	        Route::get('/brands/edit/{id?}', ['uses' => 'BrandController@edit'])->name('editBrand');
	        Route::post('/brands/update/{id?}', ['uses' => 'BrandController@update'])->name('updateBrand');
	        Route::post('/brands/delete', ['uses' => 'BrandController@destroy'])->name('deleteBrand');
	        Route::post('/brands/update_status', ['uses' => 'BrandController@updateStatus'])->name('changeStatusBrand');
	        Route::post('/brands/update_statusAjax', ['uses' => 'BrandController@updateStatusAjax'])->name('changeStatusAjaxBrand');
	        Route::post('/brands/bulkdelete', ['uses' => 'BrandController@bulkdelete'])->name('deleteBrands');
	        Route::post('/brands/bulkupdate_status', ['uses' => 'BrandController@bulkchangeStatus'])->name('changeStatusBrands');
*/
		//Area Module
		Route::get('/areas/list', ['uses' => 'LocationController@index'])->name('areas');
		Route::get('/areaAjax', ['uses' => 'LocationController@areaAjax'])->name('areaAjax');
		Route::get('/areas/add', ['uses' => 'LocationController@create'])->name('createArea');
		Route::post('/areas/checkArea/{id?}', ['uses' => 'LocationController@checkArea'])->name('checkArea');
		Route::post('/areas/store', ['uses' => 'LocationController@store'])->name('addArea');
		// Route::get('/areas/view/{id?}', ['uses' => 'LocationController@show'])->name('viewArea');
		Route::get('/areas/edit/{id?}', ['uses' => 'LocationController@edit'])->name('editArea');
		Route::post('/areas/update/{id?}', ['uses' => 'LocationController@update'])->name('updateArea');
		// Route::post('/areas/delete', ['uses' => 'LocationController@destroy'])->name('deleteArea');
		Route::post('/areas/update_status', ['uses' => 'LocationController@updateStatus'])->name('changeStatusArea');
		Route::post('/areas/update_statusAjax', ['uses' => 'LocationController@updateStatusAjax'])->name('changeStatusAjaxArea');
		// Route::post('/areas/bulkdelete', ['uses' => 'LocationController@bulkdelete'])->name('deleteAreas');
		Route::post('/areas/bulkupdate_status', ['uses' => 'LocationController@bulkchangeStatus'])->name('changeStatusAreas');

		//Coupon Code module
		Route::get('/couponCodes/list', ['uses' => 'CouponCodeController@index'])->name('couponCodes');
		Route::get('/couponCodesAjax', ['uses' => 'CouponCodeController@couponCodesAjax'])->name('couponCodesAjax');
		Route::get('/couponCodes/add', ['uses' => 'CouponCodeController@create'])->name('createCouponCode');
		Route::get('/couponCodes/checkCouponCode/{id?}', ['uses' => 'CouponCodeController@checkCouponCode'])->name('checkCouponCode');
		Route::post('/couponCodes/store', ['uses' => 'CouponCodeController@store'])->name('addCouponCode');
		Route::get('/couponCodes/view/{id?}', ['uses' => 'CouponCodeController@show'])->name('viewCouponCode');
		Route::get('/couponCodes/edit/{id?}', ['uses' => 'CouponCodeController@edit'])->name('editCouponCode');
		Route::post('/couponCodes/update/{id?}', ['uses' => 'CouponCodeController@update'])->name('updateCouponCode');
		Route::post('/couponCodes/update_status', ['uses' => 'CouponCodeController@updateStatus'])->name('changeStatusCouponCode');
		Route::post('/couponCodes/update_statusAjax', ['uses' => 'CouponCodeController@updateStatusAjax'])->name('changeStatusAjaxCouponCode');
		// Route::post('/couponCodes/delete', ['uses' => 'CouponCodeController@destroy'])->name('deleteCouponCode');
		// Route::post('/couponCodes/bulkdelete', ['uses' => 'CouponCodeController@bulkdelete'])->name('deleteCouponCodes');
		Route::post('/couponCodes/bulkupdate_status', ['uses' => 'CouponCodeController@bulkchangeStatus'])->name('changeStatusCouponCodes');
		Route::post('/couponCodes/bulkupdate_status', ['uses' => 'CouponCodeController@bulkchangeStatus'])->name('changeStatusCouponCodes');

		//Coupon Ranges module
		Route::get('/rangesAjax', ['uses' => 'CouponRangeController@rangesAjax'])->name('rangesAjax');
		Route::post('/ranges/update_statusAjax', ['uses' => 'CouponRangeController@updateStatusAjax'])->name('changeStatusAjaxRange');
		Route::post('/ranges/bulkupdate_status', ['uses' => 'CouponRangeController@bulkchangeStatus'])->name('changeStatusRanges');
		Route::post('/ranges/delete', ['uses' => 'CouponRangeController@destroy'])->name('deleteRange');

		//Offers module
		Route::get('/offers/list', ['uses' => 'OfferController@index'])->name('offers');
		Route::get('/offersAjax', ['uses' => 'OfferController@offersAjax'])->name('offersAjax');
		Route::get('/offers/add', ['uses' => 'OfferController@create'])->name('createOffer');
		Route::post('/offers/store', ['uses' => 'OfferController@store'])->name('addOffer');
		Route::get('/offers/view/{id?}', ['uses' => 'OfferController@show'])->name('viewOffer');
		Route::get('/offers/edit/{id?}', ['uses' => 'OfferController@edit'])->name('editOffer');
		Route::post('/offers/update/{id?}', ['uses' => 'OfferController@update'])->name('updateOffer');
		Route::post('/offers/update_status', ['uses' => 'OfferController@updateStatus'])->name('changeStatusOffer');
		Route::post('/offers/update_statusAjax', ['uses' => 'OfferController@updateStatusAjax'])->name('changeStatusAjaxOffer');
		// Route::post('/offers/delete', ['uses' => 'OfferController@destroy'])->name('deleteOffer');
		// Route::post('/offers/bulkdelete', ['uses' => 'OfferController@bulkdelete'])->name('deleteOffers');
		Route::post('/offers/bulkupdate_status', ['uses' => 'OfferController@bulkchangeStatus'])->name('changeStatusOffers');

		//Shipping Charge module
		Route::get('/shippingCharges/list', ['uses' => 'ShippingChargeController@index'])->name('shippingCharges');
		Route::get('/shippingChargesAjax', ['uses' => 'ShippingChargeController@shippingChargesAjax'])->name('shippingChargesAjax');
		Route::post('/shippingCharges/checkShippingCharge/{id?}', ['uses' => 'ShippingChargeController@checkShippingCharge'])->name('checkShippingCharge');
		Route::get('/shippingCharges/add', ['uses' => 'ShippingChargeController@create'])->name('createShippingCharge');
		Route::post('/shippingCharges/store', ['uses' => 'ShippingChargeController@store'])->name('addShippingCharge');
		// Route::get('/shippingCharges/view/{id?}', ['uses' => 'ShippingChargeController@show'])->name('viewShippingCharge');
		Route::get('/shippingCharges/edit/{id?}', ['uses' => 'ShippingChargeController@edit'])->name('editShippingCharge');
		Route::post('/shippingCharges/update/{id?}', ['uses' => 'ShippingChargeController@update'])->name('updateShippingCharge');
		Route::post('/shippingCharges/update_status', ['uses' => 'ShippingChargeController@updateStatus'])->name('changeStatusShippingCharge');
		Route::post('/shippingCharges/update_statusAjax', ['uses' => 'ShippingChargeController@updateStatusAjax'])->name('changeStatusAjaxShippingCharge');
		Route::post('/shippingCharges/delete', ['uses' => 'ShippingChargeController@destroy'])->name('deleteShippingCharge');
		Route::post('/shippingCharges/bulkdelete', ['uses' => 'ShippingChargeController@bulkdelete'])->name('deleteShippingCharges');
		Route::post('/shippingCharges/bulkupdate_status', ['uses' => 'ShippingChargeController@bulkchangeStatus'])->name('changeStatusShippingCharges');

		//Taxes module
		/*Route::get('/taxes/list', ['uses' => 'TaxController@index'])->name('taxes');
	        Route::get('/taxesAjax', ['uses' => 'TaxController@taxesAjax'])->name('taxesAjax');
	        Route::post('/taxes/checkTax/{id?}', ['uses' => 'TaxController@checkTax'])->name('checkTax');
	        Route::get('/taxes/add', ['uses' => 'TaxController@create'])->name('createTax');
	        Route::post('/taxes/store', ['uses' => 'TaxController@store'])->name('addTax');
	        Route::get('/taxes/edit/{id?}', ['uses' => 'TaxController@edit'])->name('editTax');
	        Route::post('/taxes/update/{id?}', ['uses' => 'TaxController@update'])->name('updateTax');
	        Route::get('/taxes/update_status', ['uses' => 'TaxController@updateStatus'])->name('changeStatusTax');
	        Route::post('/taxes/update_statusAjax', ['uses' => 'TaxController@updateStatusAjax'])->name('changeStatusAjaxTax');
	        Route::post('/taxes/delete', ['uses' => 'TaxController@destroy'])->name('deleteTax');
	        Route::post('/taxes/bulkdelete', ['uses' => 'TaxController@bulkdelete'])->name('deleteTaxes');
*/

		//Sliders module
		Route::get('/sliders/list', ['uses' => 'SliderController@index'])->name('sliders');
		Route::get('/slidersAjax', ['uses' => 'SliderController@slidersAjax'])->name('slidersAjax');
		Route::post('/sliders/checkBanner/{id?}', ['uses' => 'SliderController@checkBanner'])->name('checkBanner');
		Route::get('/sliders/add', ['uses' => 'SliderController@create'])->name('createSlider');
		Route::post('/sliders/store', ['uses' => 'SliderController@store'])->name('addSlider');
		// Route::get('/sliders/view/{id?}', ['uses' => 'SliderController@show'])->name('viewSlider');
		Route::get('/sliders/edit/{id?}', ['uses' => 'SliderController@edit'])->name('editSlider');
		Route::post('/sliders/update/{id?}', ['uses' => 'SliderController@update'])->name('updateSlider');
		Route::get('/sliders/update_status', ['uses' => 'SliderController@updateStatus'])->name('changeStatusSlider');
		Route::post('/sliders/update_statusAjax', ['uses' => 'SliderController@updateStatusAjax'])->name('changeStatusAjaxSlider');
		Route::post('/sliders/delete', ['uses' => 'SliderController@destroy'])->name('deleteSlider');
		Route::post('/sliders/bulkdelete', ['uses' => 'SliderController@bulkdelete'])->name('deleteSliders');
		Route::post('/sliders/bulkupdate_status', ['uses' => 'SliderController@bulkchangeStatus'])->name('changeStatusSliders');

		//Product variations module
		Route::get('/variationsAjax', ['uses' => 'VariationController@variationsAjax'])->name('variationsAjax');
		Route::post('/variations/update_statusAjax', ['uses' => 'VariationController@updateStatusAjax'])->name('changeStatusAjaxVariation');
		Route::post('/variations/bulkupdate_status', ['uses' => 'VariationController@bulkchangeStatus'])->name('changeStatusVariations');
		Route::post('/variations/delete', ['uses' => 'VariationController@destroy'])->name('deleteVariation');

		//Attribute Module
		Route::get('/attributes/list', ['uses' => 'AttributeController@index'])->name('attributes');
		Route::get('/AttributeAjax', ['uses' => 'AttributeController@AttributeAjax'])->name('AttributeAjax');
		Route::get('/attributes/add', ['uses' => 'AttributeController@create'])->name('createAttribute');

		Route::get('/admin/attributes/checkAttribute/{id?}', ['uses' => 'AttributeController@checkAttribute'])->name('checkAttribute');

		Route::post('/attributes/store', ['uses' => 'AttributeController@store'])->name('addAttribute');
		Route::get('/attributes/view/{id?}', ['uses' => 'AttributeController@show'])->name('viewAttribute');
		Route::get('/attributes/edit/{id?}', ['uses' => 'AttributeController@edit'])->name('editAttribute');
		Route::post('/attributes/update/{id?}', ['uses' => 'AttributeController@update'])->name('updateAttribute');
		Route::post('/attributes/update_status', ['uses' => 'AttributeController@updateStatus'])->name('changeStatusAttribute');
		Route::post('/attributes/update_statusAjax', ['uses' => 'AttributeController@updateStatusAjax'])->name('changeStatusAjaxAttribute');
		Route::post('/attributes/delete', ['uses' => 'AttributeController@destroy'])->name('deleteAttribute');
		Route::post('/attributes/bulkdelete', ['uses' => 'AttributeController@bulkdelete'])->name('deleteattributes');
		Route::post('/attributes/bulkupdate_status', ['uses' => 'AttributeController@bulkchangeStatus'])->name('changeStatusattributes');

		//AttributesSet Module
		Route::get('/attributes-sets/list', ['uses' => 'AttributesSetController@index'])->name('attributes-sets');
		Route::get('/AttributesSetAjax', ['uses' => 'AttributesSetController@AttributesSetAjax'])->name('AttributesSetAjax');
		Route::get('/attributes-sets/add', ['uses' => 'AttributesSetController@create'])->name('createAttributesSet');
		Route::get('/attributes-sets/checkAttributesSet/{id?}', ['uses' => 'AttributesSetController@checkAttributesSet'])->name('checkAttributesSet');
		Route::post('/attributes-sets/store', ['uses' => 'AttributesSetController@store'])->name('addAttributesSet');
		Route::get('/attributes-sets/view/{id?}', ['uses' => 'AttributesSetController@show'])->name('viewAttributesSet');
		Route::get('/attributes-sets/edit/{id?}', ['uses' => 'AttributesSetController@edit'])->name('editAttributesSet');
		Route::post('/attributes-sets/update/{id?}', ['uses' => 'AttributesSetController@update'])->name('updateAttributesSet');
		Route::post('/attributes-sets/update_status', ['uses' => 'AttributesSetController@updateStatus'])->name('changeStatusAttribute1');
		Route::post('/attributes-sets/update_statusAjax', ['uses' => 'AttributesSetController@updateStatusAjax'])->name('changeStatusAjaxAttributesSet');
		Route::post('/attributes-sets/delete', ['uses' => 'AttributesSetController@destroy'])->name('deleteAttributesSet');
		Route::post('/attributes-sets/bulkdelete', ['uses' => 'AttributesSetController@bulkdelete'])->name('deleteattributes-sets');
		Route::post('/attributes-sets/bulkupdate_status', ['uses' => 'AttributesSetController@bulkchangeStatus'])->name('changeStatusattributes-sets');
		Route::get('/variationSend', ['uses' => 'AttributeController@variationSend'])->name('variationSend');

		//Products module
		Route::post('/attribute/option/get', ['uses' => 'ProductController@attributeOptionGet'])->name('attributeOptionGet');
		Route::get('/products/list', ['uses' => 'ProductController@index'])->name('products');
		Route::get('/productsAjax', ['uses' => 'ProductController@productsAjax'])->name('productsAjax');
		Route::get('/products/add', ['uses' => 'ProductController@create'])->name('createProduct');
		Route::post('/products/checkProduct/{id?}', ['uses' => 'ProductController@checkProduct'])->name('checkProduct');
		Route::post('/products/store', ['uses' => 'ProductController@store'])->name('addProduct');
		Route::get('/products/view/{id?}', ['uses' => 'ProductController@show'])->name('viewProduct');
		Route::get('/products/edit/{id?}', ['uses' => 'ProductController@edit'])->name('editProduct');
		Route::post('/products/update/{id?}', ['uses' => 'ProductController@update'])->name('updateProduct');
		Route::post('/products/update_status', ['uses' => 'ProductController@updateStatus'])->name('changeStatusProduct');
		Route::post('/products/update_statusAjax', ['uses' => 'ProductController@updateStatusAjax'])->name('changeStatusAjaxProduct');
		Route::post('/products/delete', ['uses' => 'ProductController@destroy'])->name('deleteProduct');
		Route::post('/products/bulkdelete', ['uses' => 'ProductController@bulkdelete'])->name('deleteProducts');
		Route::post('/products/bulkupdate_status', ['uses' => 'ProductController@bulkchangeStatus'])->name('changeStatusProducts');
		Route::any('/products/import', ['uses' => 'ProductController@import'])->name('importProducts');
		Route::get('/products/exportView', ['uses' => 'ProductController@exportView'])->name('exportView');
		Route::get('/products/export', ['uses' => 'ProductController@export'])->name('exportProducts');
		Route::post('/products/bulkUpdate', ['uses' => 'ProductController@bulkUpdate'])->name('bulkProductUpdate');

		//Delivery Module module
		Route::get('/delivery_slots/list', ['uses' => 'DeliverySlotController@index'])->name('deliverySlots');
		Route::get('/deliverySlotsAjax', ['uses' => 'DeliverySlotController@deliverySlotsAjax'])->name('deliverySlotsAjax');
		Route::get('/delivery_slots/add', ['uses' => 'DeliverySlotController@create'])->name('createDeliverySlot');
		Route::post('/delivery_slots/checkDeliverySlot/{id?}', ['uses' => 'DeliverySlotController@checkDeliverySlot'])->name('checkDeliverySlot');
		Route::post('/delivery_slots/store', ['uses' => 'DeliverySlotController@store'])->name('addDeliverySlot');
		Route::get('/delivery_slots/view/{id?}', ['uses' => 'DeliverySlotController@show'])->name('viewDeliverySlot');
		Route::get('/delivery_slots/edit/{id?}', ['uses' => 'DeliverySlotController@edit'])->name('editDeliverySlot');
		Route::post('/delivery_slots/update/{id?}', ['uses' => 'DeliverySlotController@update'])->name('updateDeliverySlot');
		Route::post('/delivery_slots/update_status', ['uses' => 'DeliverySlotController@updateStatus'])->name('changeStatusDeliverySlot');
		Route::post('/delivery_slots/update_statusAjax', ['uses' => 'DeliverySlotController@updateStatusAjax'])->name('changeStatusAjaxDeliverySlot');
		// Route::post('/delivery_slots/delete', ['uses' => 'ProductController@destroy'])->name('deleteDeliverySlot');
		// Route::post('/delivery_slots/bulkdelete', ['uses' => 'ProductController@bulkdelete'])->name('deleteDeliverySlots');
		Route::post('/delivery_slots/bulkupdate_status', ['uses' => 'DeliverySlotController@bulkchangeStatus'])->name('changeStatusDeliverySlots');

		//Orders module
		Route::get('/orders/list', ['uses' => 'OrderController@index'])->name('orders');
		Route::get('/ordersAjax', ['uses' => 'OrderController@ordersAjax'])->name('ordersAjax');
		Route::get('/orders/view/{id?}', ['uses' => 'OrderController@show'])->name('viewOrder');
		Route::post('/orders/update/{id?}', ['uses' => 'OrderController@update'])->name('updateOrder');
		Route::post('/orders/sendInvoice/{id?}', ['uses' => 'OrderController@sendInvoice'])->name('sendInvoiceOrder');
		Route::post('/orders/assignDriver/{id?}', ['uses' => 'OrderController@assignDriver'])->name('assignDriver');
		Route::get('/orders/returnOrder/{id?}', ['uses' => 'OrderController@returnOrder'])->name('returnOrder');

		Route::get('/orders/completeOrder/{id?}', ['uses' => 'OrderController@completeOrder'])->name('completeOrder');

		//Order Returns module
		Route::get('/orderReturns/list', ['uses' => 'OrderController@return_index'])->name('returns');
		Route::get('/returnsAjax', ['uses' => 'OrderController@returnsAjax'])->name('returnsAjax');
		Route::get('/orderReturns/view/{id?}', ['uses' => 'OrderController@return_show'])->name('viewOrderReturn');
		Route::post('/orderReturns/update/{id?}', ['uses' => 'OrderController@return_update'])->name('updateOrderReturn');

		//Ratings Module
		Route::get('/ratings/list', ['uses' => 'RatingController@index'])->name('ratings');
		Route::get('/ratingsAjax', ['uses' => 'RatingController@ratingsAjax'])->name('ratingsAjax');

		//Transactions Module
		Route::get('/payments/list', ['uses' => 'TransactionsController@index'])->name('payments');
		Route::get('/paymentsAjax', ['uses' => 'TransactionsController@paymentsAjax'])->name('paymentsAjax');
		Route::get('/payments/view/{id?}', ['uses' => 'TransactionsController@show'])->name('viewPayment');

		//Notification Module
		Route::get('/notifications/list', ['uses' => 'NotificationController@index'])->name('notifications');
		Route::get('/notificationsAjax', ['uses' => 'NotificationController@notificationsAjax'])->name('notificationsAjax');
		Route::get('/notification/add', ['uses' => 'NotificationController@create'])->name('createNotification');
		Route::get('/notifications/add', ['uses' => 'NotificationController@create'])->name('createNotifications');
		Route::post('/notification/store', ['uses' => 'NotificationController@store'])->name('storeNotification');
	});
});

/*-------Front Page Routes ------*/
Route::get('refresh-csrf', function () {
	return csrf_token();
});
Route::get('javatraininginchennaiomr', function () {
	if (!Session::has('access')) {
		Session::put('access', []);
	}

	return view('front.automation.java');
})->name('java');

// Route::get('javatraininginchennaiomr', function () {

// 	return view('front.automation.java');
// })->name('java');

Route::get('hotelsearch-iframe', function () {
	return view('front.travel.hotelsearch_iframe');
})->name('hotelsearch-iframe');

Route::get('page/{slug}', function ($slug) {
	$title = str_replace('-', ' ', $slug);
	return view('front.automation.java', compact('title'));
})->name('pages');

Route::get('java-content', function () {
	return view('front.automation.java-content');
})->name('java-content');

Route::get('seleniumtraininginchennaiomr', function () {
	if (!Session::has('access')) {
		Session::put('access', []);
	}
	return view('front.automation.selenium');
})->name('selenium');

Route::get('apitestingtraininginchennaiomr', function () {
	if (!Session::has('access')) {
		Session::put('access', []);
	}
	$countries = \App\Model\Country::get();
	return view('front.automation.signup', compact('countries'));
})->name('automation');

Route::get('softwaretestingtraininginchennaiomr', function () {
	if (!Session::has('access')) {
		Session::put('access', []);
	}
	return view('front.automation.software');
})->name('automation');

Route::get('certifications', function () {
	return view('front.automation.software');
})->name('certifications');
//

Route::get("bookingwebview/{user_id}/{booking_id}", 'Front\HotelController@bookingwebview')->name('bookingwebview');
Route::post('updateBookingwebview/{user_id}/{booking_id}', 'Front\HotelController@updateBookingwebview')->name('updateBookingwebview');
Route::get("bookingsuccess", 'Front\HotelController@bookingsuccess')->name('bookingsuccess');

// user specific routing
Route::middleware(['shareMiddleware', 'preventBackHistory', 'webSession'])->group(function () {
	Route::get('/', 'Front\LoginController@index')->name('home');

	//Route::get('/', 'Front\LoginController@index')->name('home');
	Route::match(['get', 'post'], 'weblogin', 'Front\LoginController@login')->name('weblogin');

	#social login
	Route::match(['get', 'post'], 'social-login/{type}', 'Front\LoginController@social_login');
	Route::match(['get', 'post'], 'social-login-callback/{type}', 'Front\LoginController@social_login_callback');
	Route::post('social-signup', 'Front\LoginController@socialSignup'); //when no email found

	#category
	Route::get('category/groceries/{id}', 'Front\CategoryController@index')->name('category_groceries');

	#add product from homepage
	Route::post('addToCartHome', 'Front\LoginController@addToCartHome');

	#product
	Route::get('category/groceries/product/{id}', 'Front\ProductController@index');
	Route::get('product/{type}/{id}', 'Front\ProductController@show');
	Route::get('view/{type}/{id}', 'Front\OfferController@details');

	#search product listing
	Route::post('filter-product', 'Front\LoginController@filter');
	Route::get('listing/{type?}/{id?}/{catId?}', 'Front\LoginController@searchResult');

	#pages
	// Route::get('pages/offers', 'Front\OfferController@index');
	// Route::get('pages/contact-us', 'Front\ContactController@index');
	// Route::post('contact/store', 'Front\ContactController@store');

	Route::get('/pages/{slug}', 'Front\PagesController@index');

	Route::middleware(['webAuth', 'web'])->group(function () {
		Route::get('grocery', 'Front\LoginController@grocery')->name('grocery');
		Route::get('hotel-booking', 'Front\HotelController@travel')->name('travel');
		Route::get('flight-booking', 'Front\FlightController@flight')->name('flight');
		Route::get('search-hotel', 'Front\HotelController@searchHotel')->name('search-hotel');
		Route::get('view-hotel/{id}', 'Front\HotelController@viewHotel')->name('view-hotel');
		Route::post('book-hotel', 'Front\HotelController@bookHotel')->name('book-hotel');

		//flight checkout
		Route::get('view-flight/{id}', 'Front\FlightController@viewFlight')->name('view-flight');
		//flight booking-confirmed
		Route::post('booking-confirmed', 'Front\FlightController@bookingConfirmed')->name('booking-confirmed');
		// flight search
		Route::get('search-flight', 'Front\FlightController@searchflight')->name('search-flight');
		//flight booking
		Route::post('book-flight', 'Front\FlightController@bookFlight')->name('book-flight');
		// ajax flight
		Route::post('step1next', 'Front\FlightController@step1next')->name('step1next');
		Route::post('step2next', 'Front\FlightController@step2next')->name('step2next');
		Route::post('step3next', 'Front\FlightController@step3next')->name('step3next');
		Route::post('step4next', 'Front\FlightController@step4next')->name('step4next');
		Route::post('step5next', 'Front\FlightController@step5next')->name('step5next');
		Route::post('releaseAirlineSeats', 'Front\FlightController@releaseAirlineSeats')->name('releaseAirlineSeats');
		
		// ajax get state
		Route::get('state-list', 'Front\FlightController@listState')->name('state-list');
		// ajax get city
		Route::get('city-list', 'Front\FlightController@listCity')->name('city-list');
		//ajax hotel Name
		Route::post('hotel-name', 'Front\FlightController@hotelName')->name('hotel-name');
		//flight my booking 
		Route::get('flight-my-bookings', 'Front\FlightController@bookings')->name('flight-my-bookings');
		//flight viewBooking
		Route::get('flight-view-booking/{no}', 'Front\FlightController@viewBooking')->name('flight-view-booking');
		//flight Cancel
		Route::get('flight-cancel-booking/{id}', 'Front\FlightController@cancelBooking')->name('flight-cancel-booking');
		//flight Web-Checkin
		Route::get('flight-web-checkin/{id}', 'Front\FlightController@webCheckin')->name('flight-web-checkin');
		//flight booking edit
		Route::get('flight-edit-booking/{id}', 'Front\FlightController@editBooking')->name('flight-edit-booking');
		// flight booking update
		Route::post('flight-update-booking/{id}', 'Front\FlightController@updateBooking')->name('flight-update-booking');
		
		Route::get('my-bookings', 'Front\HotelController@bookings')->name('my-bookings');
		

		Route::get('booking-confirmed/{hotel}/{no}', function ($hotel, $no) {
			$booking_no = $no;
			return view('front.travel.confirmation', compact('booking_no', 'hotel'));
		})->name('confirmed');
		Route::get('cancel-booking/{id}', 'Front\HotelController@cancelBooking')->name('cancel-booking');
		Route::get('edit-booking/{id}', 'Front\HotelController@editBooking')->name('edit-booking');
		Route::get('view-booking/{no}', 'Front\HotelController@viewBooking')->name('view-booking');
		Route::post('update-booking/{id}', 'Front\HotelController@updateBooking')->name('update-booking');
		Route::get('get-bookings/{status?}', 'Front\HotelController@getBookings')->name('get-bookings');

		Route::get('sample-api', 'Front\LoginController@sampleApi')->name('sample-api');
		Route::get('user-admin', function () {
			if (Auth::guard('admin')->check()) {
				Auth::guard('admin')->logout();
			}

			$user = Auth::guard('front')->user();
			Auth::guard('admin')->login($user);

			Session::put('globalCity', 'all');
			Session::put('globalState', 'all');
			Session::put('lastlogin', time());
			Session::put('currency', config('constants.currency'));
			return redirect('/admin');
		})->name('user-admin');

		Route::get('/weblogout', function () {
			// Auth::guard('web');
			Auth::guard('web')->logout();
			if (Auth::guard('api')->check()) {
				Auth::guard('api')->logout();
			}
			Session::flush('user');
			Session::flush('access');
			setcookie("login_session", "", time() - 3600);
			// Session::flush();
			return redirect()->route('home');
		})->name('weblogout');

		#cart
		Route::get('my-cart', 'Front\CartController@index')->name('my_cart');
		Route::post('update-address', 'Front\CartController@setAddress');
		Route::post('setCartWallet', 'Front\CartController@setCartWallet');

		Route::post('addToCartProduct', 'Front\CartController@addToCartProduct');

		Route::post('applyCouponCode', 'Front\CartController@applyCouponCode');

		#account
		Route::get('account-settings', 'Front\AccountController@index');

		Route::post('removeAddress', 'Front\AccountController@removeAddress')->name('removeAddress');

		#notification
		Route::get('notifications', 'Front\NotificationController@index');

		#Favourite
		Route::get('my-favourites', 'Front\FavouriteController@index');

		#Order details
		Route::get('order-detail/{id}', 'Front\OrderController@index');
		Route::post('product-date-order', 'Front\OrderController@getProductDateWise');
		Route::get('track-order-no', 'Front\OrderController@trackOrderByNo'); //search by order no
		Route::post('track-order', 'Front\OrderController@trackOrder'); // redirect to tracker page acc. to order_no
		Route::get('track-order/{order_id}/{item_id}', 'Front\OrderController@trackOrderByItem'); //redirect to tracker page acc. to order_id

		#Order status
		Route::get('order-status/{id}', 'Front\OrderController@getStatus');
		Route::get('order-status-detail/{id}/{item_id}', 'Front\OrderController@getStatusDetail');

		Route::get('productFilter', 'Front\ProductController@productFilter')->name('productFilter');

		Route::get('ticketGenerate', 'Front\LoginController@ticketGenerate');

		Route::get('ticketGenerateOtp', 'Front\LoginController@ticketGenerateOtp');

		Route::post('closeTicket', 'Front\LoginController@closeTicket')->name('closeTicket');
		Route::post('moveBinTicket', 'Front\LoginController@moveBinTicket')->name('moveBinTicket');

		Route::post("getCartItem", "Front\CartController@getCartItem_ajax")->name('getCartItem');

	});
});

/*-------Front Page Routes End------*/
Route::get('cropImage/{type}', 'TestController@cropImage');

/*-------  frontend  ------*/
