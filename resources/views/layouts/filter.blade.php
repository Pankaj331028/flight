
<div class="row">
    <div class="col-12">
        <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Filters</h4>
                    <div class="row">
                        <div class="col-lg-3 col-md-4">
                            <div class="form-group">
                                <label for="from_date">From Date</label>
                                <input type="text" id="from_date" name="from_date" class="form-control" placeholder="Enter from date" value='{{ (isset($_GET['from_date'])) ? ($_GET["from_date"]) ? $_GET["from_date"] : "" : "" }}'>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4">
                            <div class="form-group">
                                <label for="end_date">End Date</label>
                                <input type="text" id="end_date" name="end_date" class="form-control" placeholder="Enter end date" value='{{ (isset($_GET['end_date'])) ? ($_GET["end_date"]) ? $_GET["end_date"] : "" :"" }}'>
                            </div>
                        </div>

                        @if(Request::segment(2)=="staffs")
                            <div class="col-lg-3 col-md-4">
                                <div class="form-group">
                                    <label for="role">Role</label>
                                    <select class="form-control role" name="role" id="roleFilter">
                                        <option value="">Select Role</option>
                                        @foreach($roles as $role)
                                            <option value="{{$role->role}}" {{ (isset($_GET['role'])) ? ($_GET['role']==$role->role) ? "selected" : "" : "" }}>{{$role->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif

                        @if(in_array(Request::segment(2),["sliders"]))
                            <div class="col-lg-3 col-md-4">
                                <div class="form-group">
                                    <label for="role">Category</label>
                                    <select class="form-control unit" name="category_filter" id="category_filter">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{$category->id}}" {{ (isset($_GET['category_filter'])) ? ($_GET['category_filter']==$category->id) ? "selected" : "" : "" }}>{{ucfirst($category->name)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif

                        @if(in_array(Request::segment(2),["notifications"]))
                            <div class="col-lg-3 col-md-4">
                                <div class="form-group">
                                    <label for="role">Type</label>
                                    <select class="form-control unit" name="notification_type" id="notification_type">
                                        <option value="">Select Type</option>
                                        @foreach($types as $type)
                                            <option value="{{$type}}" {{ (isset($_GET['notification_type'])) ? ($_GET['notification_type']==$type) ? "selected" : "" : "" }}>{{ucfirst($type)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif

                        @if(in_array(Request::segment(2),["delivery_slots"]))
                            <div class="col-lg-3 col-md-4">
                                <div class="form-group">
                                    <label for="role">Type</label>
                                    <select class="form-control unit" name="slot_type" id="slot_type">
                                        <option value="">Select Type</option>
                                        @foreach(config('constants.SLOT_TYPE') as $key=>$type)
                                            <option value="{{$key}}" {{ (isset($_GET['slot_type'])) ? ($_GET['slot_type']==$key) ? "selected" : "" : "" }}>{{$type}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4">
                                <div class="form-group">
                                    <label for="role">Day</label>
                                    <select class="form-control unit" name="slot_day" id="slot_day">
                                        <option value="">Select Day</option>
                                        @foreach(config('constants.WEEK_DAY') as $key=>$type)
                                            <option value="{{$key}}" {{ (isset($_GET['slot_day'])) ? ($_GET['slot_day']==$key) ? "selected" : "" : "" }}>{{$type}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif

                        @if(in_array(Request::segment(2),["offers","products","couponCodes"]))
                            <div class="col-lg-3 col-md-4">
                                <div class="form-group">
                                    <label for="role">Category</label>
                                    <select class="form-control unit" name="categoryFilter" id="categoryFilter">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $key=>$category)
                                            <option value="{{$key}}" {{ (isset($_GET['categoryFilter'])) ? ($_GET['categoryFilter']==$key) ? "selected" : "" : "" }}>{{ucfirst($category)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif

                        @if(in_array(Request::segment(2), ["offers"]))
                            <div class="col-lg-3 col-md-4">
                                <div class="form-group">
                                    <label for="status">Top Offer</label>
                                    <select class="form-control is_top_offer" name="is_top_offer">
                                        <option value="">Select Option</option>
                                        <option value="0" {{ (isset($_GET['is_top_offer'])) ? ($_GET['is_top_offer']=='0') ? "selected" : "" : "" }}>No</option>
                                        <option value="1" {{ (isset($_GET['is_top_offer'])) ? ($_GET['is_top_offer']=='1') ? "selected" : "" : "" }}>Yes</option>
                                    </select>
                                </div>
                            </div>
                        @endif

                        @if(in_array(Request::segment(2), ["products"]))
                            <div class="col-lg-3 col-md-4" style="display: none;">
                                <div class="form-group">
                                    <label for="status">Manage Stock</label>
                                    <select class="form-control manage_stock" name="manage_stock">
                                        <option value="">Select Option</option>
                                        <option value="0" {{ (isset($_GET['manage_stock'])) ? ($_GET['manage_stock']=='0') ? "selected" : "" : "" }}>No</option>
                                        <option value="1" {{ (isset($_GET['manage_stock'])) ? ($_GET['manage_stock']=='1') ? "selected" : "" : "" }}>Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4">
                                <div class="form-group">
                                    <label for="status">Quick Grab</label>
                                    <select class="form-control quick_grab" name="quick_grab">
                                        <option value="">Select Option</option>
                                        <option value="0" {{ (isset($_GET['quick_grab'])) ? ($_GET['quick_grab']=='0') ? "selected" : "" : "" }}>No</option>
                                        <option value="1" {{ (isset($_GET['quick_grab'])) ? ($_GET['quick_grab']=='1') ? "selected" : "" : "" }}>Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4">
                                <div class="form-group">
                                    <label for="status">Is Exclusive</label>
                                    <select class="form-control is_exclusive" name="is_exclusive">
                                        <option value="">Select Option</option>
                                        <option value="0" {{ (isset($_GET['is_exclusive'])) ? ($_GET['is_exclusive']=='0') ? "selected" : "" : "" }}>No</option>
                                        <option value="1" {{ (isset($_GET['is_exclusive'])) ? ($_GET['is_exclusive']=='1') ? "selected" : "" : "" }}>Yes</option>
                                    </select>
                                </div>
                            </div>
                        @endif
                        @if(in_array(Request::segment(2), ["users"]))
                            <div class="col-lg-3 col-md-4">
                                <div class="form-group">
                                    <label for="status">Verified</label>
                                    <select class="form-control is_verified" name="is_verified">
                                        <option value="">Select Option</option>
                                        <option value="0" {{ (isset($_GET['is_verified'])) ? ($_GET['is_verified']=='0') ? "selected" : "" : "" }}>No</option>
                                        <option value="1" {{ (isset($_GET['is_verified'])) ? ($_GET['is_verified']=='1') ? "selected" : "" : "" }}>Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4">
                                <div class="form-group">
                                    <label for="status">Google SignIn</label>
                                    <select class="form-control google_signin" name="google_signin">
                                        <option value="">Select Option</option>
                                        <option value="0" {{ (isset($_GET['google_signin'])) ? ($_GET['google_signin']=='0') ? "selected" : "" : "" }}>No</option>
                                        <option value="1" {{ (isset($_GET['google_signin'])) ? ($_GET['google_signin']=='1') ? "selected" : "" : "" }}>Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4">
                                <div class="form-group">
                                    <label for="status">Facebook SignIn</label>
                                    <select class="form-control facebook_signin" name="facebook_signin">
                                        <option value="">Select Option</option>
                                        <option value="0" {{ (isset($_GET['facebook_signin'])) ? ($_GET['facebook_signin']=='0') ? "selected" : "" : "" }}>No</option>
                                        <option value="1" {{ (isset($_GET['facebook_signin'])) ? ($_GET['facebook_signin']=='1') ? "selected" : "" : "" }}>Yes</option>
                                    </select>
                                </div>
                            </div>
                        @endif

                        @if(in_array(Request::segment(2), ["roles","users","staffs","categories","brands","sliders","offers","units","taxes","products","delivery_slots","couponCodes","shippingCharges","notifications","payments"]))
                            <div class="col-lg-3 col-md-4">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control status" name="status">
                                        <option value="">Select Status</option>
                                        <option value="AC" {{ (isset($_GET['status'])) ? ($_GET['status']=='AC') ? "selected" : "" : "" }}>Active</option>
                                        <option value="IN" {{ (isset($_GET['status'])) ? ($_GET['status']=='IN') ? "selected" : "" : "" }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        @endif

                        @if(in_array(Request::segment(2), ["orders"]))
                            <div class="col-lg-3 col-md-4">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control status" name="status">
                                        <option value="">Select Status</option>
                                        <option value="PN" {{ (isset($_GET['status'])) ? ($_GET['status']=='PN') ? "selected" : "" : "" }}>Pending</option>
                                        <option value="CM" {{ (isset($_GET['status'])) ? ($_GET['status']=='CM') ? "selected" : "" : "" }}>Complete</option>
                                        <option value="RN" {{ (isset($_GET['status'])) ? ($_GET['status']=='RN') ? "selected" : "" : "" }}>Returned</option>
                                        <option value="RFIN" {{ (isset($_GET['status'])) ? ($_GET['status']=='RFIN') ? "selected" : "" : "" }}>Cancel Request Pending</option>
                                        <option value="RFCM" {{ (isset($_GET['status'])) ? ($_GET['status']=='RFCM') ? "selected" : "" : "" }}>Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        @endif

                        @if(in_array(Request::segment(2), ["orderReturns"]))
                            <div class="col-lg-3 col-md-4">
                                <div class="form-group">
                                    <label for="return_type">Return Type</label>
                                    <select class="form-control return_type" name="return_type">
                                        <option value="">Select Status</option>
                                        <option value="product" {{ (isset($_GET['return_type'])) ? ($_GET['return_type']=='product') ? "selected" : "" : "" }}>Product</option>
                                        <option value="order" {{ (isset($_GET['return_type'])) ? ($_GET['return_type']=='order') ? "selected" : "" : "" }}>Order</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control status" name="status">
                                        <option value="">Select Status</option>
                                        <option value="PN" {{ (isset($_GET['status'])) ? ($_GET['status']=='PN') ? "selected" : "" : "" }}>Pending</option>
                                        <option value="AP" {{ (isset($_GET['status'])) ? ($_GET['status']=='AP') ? "selected" : "" : "" }}>Approved</option>
                                        <option value="RJ" {{ (isset($_GET['status'])) ? ($_GET['status']=='RJ') ? "selected" : "" : "" }}>Rejected</option>
                                    </select>
                                </div>
                            </div>
                        @endif

                        <div class="col-lg-3 col-md-12 d-flex align-items-center">
                            <div class="form-group flex-column flex-md-row">
                                <input type="submit" class="btn btn-success mt-2" value="Filter">
                                <a href="{{ URL::current() }}" class="btn waves-effect waves-light btn-primary mt-2">Reset</a>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
