<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex flex-row" id="topbar">
    <div class="navbar-menu-wrapper d-flex align-items-center ml-auto ml-lg-0">
        <ul class="navbar-nav navbar-nav-left w-75 d-none d-sm-flex">
            <li class="nav-item dropdown row w-100">
                {{-- <div class="p-t-10 p-r-10" style="display: inline-block;">
                    <strong>Country :</strong> <span id="countrySelected"class=" p-r-10">{{ucfirst(Session::get('globalCountry'))}}</span>|
                </div> --}}
                <div class="p-t-10 p-r-10" style="display: inline-block;">
                    <strong>State :</strong> <span id="stateSelected"class=" p-r-10">{{ucfirst(Session::get('globalState'))}}</span>|
                </div>
                <div class="p-t-10 p-r-10" style="display: inline-block;">
                    <strong>City :</strong> <span id="citySelected"class=" p-r-10">{{ucfirst(Session::get('globalCity'))}}</span>
                </div>
            </li>
        </ul>
        <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item dropdown">
                <a class="nav-link" id="messageDropdown" href="#" data-toggle="modal" data-target="#filtermodal" aria-expanded="false">
                    <i class="mdi mdi-filter"></i>
                </a>
                <div class="modal fade" id="filtermodal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myLargeModalLabel">State Filter</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body">
                                {{-- <div class="d-flex align-items-center" style="margin: 15px 0;">
                                    <label class="mb-0 col-md-3">Country:&nbsp;&nbsp;&nbsp;</label>
                                    <select class="form-control col-md-6" name="countryFilter" id="countryFilter">
                                        <option value="all" @if(session()->get('globalCountry')=='all') selected @endif>All</option>
                                        @foreach(config('statecity.countries') as $country)
                                            <option value="{{$country}}" @if(session()->get('globalCountry')==$country) selected @endif>{{$country}}</option>
                                        @endforeach
                                    </select>
                                </div> --}}
                                <div class="d-flex align-items-center" id="stateFilterCol" style="margin: 15px 0; @if(session()->get('globalCountry')=='all') display: none !important; @endif">
                                    <label class="mb-0 col-md-3">&nbsp;&nbsp;&nbsp;State:&nbsp;&nbsp;&nbsp;</label>
                                    <select class="form-control col-md-6" name="stateFilter" id="stateFilter">
                                        @if(session()->get('globalCountry')!='all')
                                            <option value="all" @if(session()->get('globalState')=='all') selected @endif>All</option>
                                            @foreach(config('statecity.states') as $state)
                                                <option value="{{$state}}" @if(session()->get('globalState')==$state) selected @endif>{{$state}}</option>
                                            @endforeach
                                        @else
                                            <option value="all">Select Country first</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="d-flex align-items-center" id="cityFilterCol" style="margin: 15px 0; @if(session()->get('globalState')=='all') display: none !important; @endif">
                                    <label class="mb-0 col-md-3">&nbsp;&nbsp;&nbsp;City:&nbsp;&nbsp;&nbsp;</label>
                                    <select class="form-control col-md-6" name="cityFilter" id="cityFilter">
                                        @if(session()->get('globalState')!='all')
                                            <option value="all" @if(session()->get('globalCity')=='all') selected @endif>All</option>
                                            @foreach(config('statecity.cities') as $city)
                                                <option value="{{$city}}" @if(session()->get('globalCity')==$city) selected @endif>{{$city}}</option>
                                            @endforeach
                                        @else
                                            <option value="all">Select State first</option>
                                        @endif
                                    </select>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary mt-3" id="applyFilter">Apply Filter</button>
                                <button type="button" class="btn btn-warning mt-3" id="resetFilter">Reset to All</button>
                                <button type="button" class="btn btn-danger mt-3 text-left" id="dismissFilter" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>
            </li>
            @if(Helper::checkAccess(route('notifications')))
                <li class="nav-item dropdown">
                    <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
                        <i class="mdi mdi-bell"></i>
                        @if(count($notifications)>0)
                        <span class="count" id="notCount">{{count($notifications)}}</span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" id="notificationList" aria-labelledby="notificationDropdown">
                        @if(count($notifications)>0)
                            <a class="dropdown-item" href="{{route('notifications')}}">
                                <p class="mb-0 font-weight-normal float-left">You have {{count($notifications)}} new notifications
                                </p>
                                <span class="badge badge-pill badge-warning float-right">View all</span>
                            </a>
                            @foreach($notifications->sortByDesc('created_at') as $not)
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item preview-item">
                                    <div class="preview-item-content">
                                        <h6 class="preview-subject font-weight-medium text-dark">{{str_limit($not->description,50,'...')}}</h6>
                                        <p class="font-weight-light small-text">
                                            {{Helper::get_time_ago($not->created_at)}}
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            <a class="dropdown-item">
                                <p class="mb-0 font-weight-normal float-left">No new notifications
                                </p>
                            </a>
                        @endif
                    </div>
                </li>
            @endif
            <li class="nav-item dropdown d-none d-lg-inline-block">
                <a class="nav-link dropdown-toggle" id="UserDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
                    <span class="profile-text">Hello,  {{ucfirst(Auth::guard('admin')->user()->first_name." ".Auth::guard('admin')->user()->last_name)}} !</span>
                    {{-- <img class="img-xs rounded-circle" src="{{URL::asset('/images/user-gray.png')}}" alt="Profile image"> --}}
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                    <ul class="dropdown-user">
                        <li>
                            <div class="dw-user-box">
                                <div class="u-img"><img src="{{URL::asset('/images/user-gray.png')}}" alt="user" class="profile-pic" /></div>
                                <div class="u-text">
                                    <h4>omrbranch</h4>
                                    <p class="text-muted">{{ucfirst(Auth::guard('admin')->user()->first_name." ".Auth::guard('admin')->user()->last_name)}}</p>
                                </div>
                            </div>
                        </li>
                        <li role="separator" class="divider"></li>
                        @if(Helper::checkAccess(route('changepassword')))
                            <li><a href="{{route('changepassword')}}"><i class="fa fa-pencil"></i> Change Password</a></li>
                        @endif
                        <li><a href="{{route('logout')}}"><i class="fa fa-power-off"></i> Logout</a></li>
                    </ul>
                </div>
            </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
        </button>
    </div>
</nav>
