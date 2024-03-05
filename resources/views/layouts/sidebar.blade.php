<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <div class="text-center navbar-brand-wrapper d-flex align-items-top justify-content-center">
                <a class="navbar-brand brand-logo" href="{{ route('index') }}">
                    <!-- <b><img src="{{URL::asset('/images/logo.png')}}" alt="homepage" class="dark-logo" style="width: 70%;"/></b> -->
                    <h3>omrbranch</h3>
                </a>
                <a class="navbar-brand brand-logo-mini" href="index.html">
                    <!-- <b><img src="{{URL::asset('/images/logo.png')}}" alt="homepage" class="dark-logo" style="width: 70%;" /></b> -->
                    <h3>omrbranch</h3>
                </a>
            </div>
            <div class="nav-link d-flex d-lg-none">
                <div class="user-wrapper">
                    <div class="text-wrapper">
                        <p class="profile-name">omrbranch</p>
                        <div>
                            <small class="designation text-muted">{{ucfirst(Auth::guard('admin')->user()->first_name." ".Auth::guard('admin')->user()->last_name)}}</small>
                            <span class="status-indicator online"></span>
                        </div>
                    </div>
                </div>
            </div>
        </li>
    </ul>
    <ul class="nav sidebarLinks">
        <li class="nav-item">
            <a class="nav-link" href="{{route('index')}}"><i class="menu-icon mdi mdi-gauge"></i><span class="menu-title">Dashboard</a>
        </li>


        @if(Helper::checkAccess(route('roles')))
            <li class="nav-item">
                <a class="nav-link" href="{{route('roles')}}"><i class="menu-icon fa fa-unlock-alt"></i><span class="menu-title">Role Management</span></a>
            </li>
        @endif

        @if(Helper::checkAccess(route('attributes')))
            <li class="nav-item">
                <a class="nav-link" href="{{route('attributes')}}"><i class="menu-icon fa fa-briefcase"></i><span class="menu-title">Attributes</span></a>
            </li>
        @endif



        @if(Helper::checkAccess(route('staffs')))
            <li class="nav-item">
                <a class="nav-link" href="{{route('staffs')}}"><i class="menu-icon fa fa-users"></i><span class="menu-title">Staff Management</span></a>
            </li>
        @endif
        @if(Helper::checkAccess(route('sliders')))
            <li class="nav-item">
                <a class="nav-link" href="{{route('sliders')}}"><i class="menu-icon fa fa-image"></i><span class="menu-title">Sliders/Banners</span></a>
            </li>
        @endif
        @if(Helper::checkAccess(route('categories')))
            <li class="nav-item">
                <a class="nav-link" href="{{route('categories')}}"><i class="menu-icon fa fa-list-alt"></i><span class="menu-title">Categories</span></a>
            </li>
        @endif
        @if(Helper::checkAccess(route('units')))
            <li class="nav-item">
                <a class="nav-link" href="{{route('units')}}"><i class="menu-icon fa fa-balance-scale"></i><span class="menu-title">Product Units</span></a>
            </li>
        @endif
       {{--  @if(Helper::checkAccess(route('taxes')))
            <li class="nav-item">
                <a class="nav-link" href="{{route('taxes')}}"><i class="menu-icon fa fa-rupee"></i><span class="menu-title">Taxes</span></a>
            </li>
        @endif --}}
        @if(Helper::checkAccess(route('cms')))
            <li class="nav-item">
                <a class="nav-link" href="{{route('cms')}}"><i class="menu-icon fa fa-code"></i><span class="menu-title">Content Management</span></a>
            </li>
        @endif
        @if(Helper::checkAccess(route('couponCodes')))
            <li class="nav-item">
                <a class="nav-link" href="{{route('couponCodes')}}"><i class="menu-icon fa fa-percent"></i><span class="menu-title">Coupon Codes</span></a>
            </li>
        @endif
        @if(Helper::checkAccess(route('offers')))
            <li class="nav-item">
                <a class="nav-link" href="{{route('offers')}}"><i class="menu-icon fa fa-percent"></i><span class="menu-title">Offers</span></a>
            </li>
        @endif

        @if(Helper::checkAccess(route('shippingCharges')))
            <li class="nav-item">
                <a class="nav-link" href="{{route('shippingCharges')}}"><i class="menu-icon fa fa-money"></i><span class="menu-title">Shipping Charges</span></a>
            </li>
        @endif
        @if(Helper::checkAccess(route('deliverySlots')))
            <li class="nav-item">
                <a class="nav-link" href="{{route('deliverySlots')}}"><i class="menu-icon fa fa-clock-o"></i><span class="menu-title">Delivery Slots</span></a>
            </li>
        @endif
        @if(Helper::checkAccess(route('products')))
            <li class="nav-item">
                <a class="nav-link" href="{{route('products')}}"><i class="menu-icon fa fa-product-hunt"></i><span class="menu-title">Products</span></a>
            </li>
        @endif
        @if(Helper::checkAccess(route('orders')))
            <li class="nav-item">
                <a class="nav-link" href="{{route('orders')}}"><i class="menu-icon fa fa-shopping-bag"></i><span class="menu-title">Orders</span></a>
            </li>
        @endif
        @if(Helper::checkAccess(route('returns')))
            <li class="nav-item">
                <a class="nav-link" href="{{route('returns')}}"><i class="menu-icon fa fa-reply"></i><span class="menu-title">Order Cancel Requests</span></a>
            </li>
        @endif
        @if(Helper::checkAccess(route('payments')))
            <li class="nav-item">
                <a class="nav-link" href="{{route('payments')}}"><i class="menu-icon fa fa-credit-card"></i><span class="menu-title">Payments</span></a>
            </li>
        @endif
        @if(Helper::checkAccess(route('users')))
            <li class="nav-item">
                <a class="nav-link" href="{{route('users')}}"><i class="menu-icon fa fa-user"></i><span class="menu-title">Users</span></a>
            </li>
        @endif
        @if(Helper::checkAccess(route('bookings')))
            <li class="nav-item">
                <a class="nav-link" href="{{route('bookings')}}"><i class="menu-icon fa fa-book"></i><span class="menu-title">Bookings</span></a>
            </li>
        @endif
        @if(Helper::checkAccess(route('drivers')))
            <li class="nav-item">
                <a class="nav-link" href="{{route('drivers')}}"><i class="menu-icon fa fa-id-card-o"></i><span class="menu-title">Drivers</span></a>
            </li>
        @endif
        @if(Helper::checkAccess(route('ratings')))
            <li class="nav-item">
                <a class="nav-link" href="{{route('ratings')}}"><i class="menu-icon fa fa-star-half-full"></i><span class="menu-title">App Ratings</span></a>
            </li>
        @endif
        @if(Helper::checkAccess(route('notifications')))
            <li class="nav-item">
                <a class="nav-link" href="{{route('notifications')}}"><i class="menu-icon fa fa-bell"></i><span class="menu-title">Notifications</span></a>
            </li>
        @endif
        @if(Helper::checkAccess(route('setting')))
            <li class="nav-item">
                <a class="nav-link" href="{{route('setting')}}"><i class="menu-icon fa fa-gear"></i><span class="menu-title">Settings</span></a>
            </li>
        @endif
    </ul>
</nav>
