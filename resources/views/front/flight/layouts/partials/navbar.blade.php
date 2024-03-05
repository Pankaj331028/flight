


<header class="header">
   <div class="top_navbar_wrapper">
      <div class="container">
         <div class="row custom_row align-items-center">
            <div class="col-lg-6 col-md-6 col-sm-12">
               <div class="location_wrapper">
                  <div class="social_icons text-md-left text-center">
                     <ul class="social_top_network social-circle">
                        <li><a href="#" class="icoLinkedin"><i class="fa fa-map-marker mr-3" aria-hidden="true"></i> <span class="area">Enable Location</span></a></li>
                     </ul>
                  </div>
               </div>
            </div>
            @if(Auth::guard('front')->check())
            <div class="col-lg-6 col-md-6 col-sm-12">
               <div class="social_icons text-md-right text-center">
                  <ul class="social_top_network social-circle top_network_res">
                     @if(in_array('grocery',Session::get('access')))
                     <li><a href="{{ url('grocery') }}" class="trans_btn" >Grocery</a></li>
                     @endif
                     @if(in_array('travel',Session::get('access')))
                     <li><a href="{{ url('hotel-booking') }}" class="trans_btn" >Book Hotel</a></li>
                     @endif
                     @if(in_array('flight',Session::get('access')))
                     <li><a href="{{ url('flight-booking') }}" class="trans_btn" >Book Flight</a></li>
                     @endif
                     <li class="dropdown">
                        <a href="#" class="icoTwitter mr-2 dropdown-toggle" data-testid="username" data-toggle="dropdown">
                        Welcome {{ $auth_user->first_name }}
                        </a>
                        <div class="dropdown-menu pro_display">
                           <div class="userInfo d-flex align-items-center bgf7 px-3 py-2">
                              <div class="acHolderImg"><img src="{{ !empty($auth_user->profile_picture) ? asset('uploads/profiles/'.$auth_user->profile_picture) : asset('uploads/profiles/no_avatar.jpg') }}" alt="profile"></div>
                              <div class="holderNameandMail flex-fill ml-3 text-left">
                                 <p class="font16 color28 mb-0 font-weight-semibold">{{ $auth_user->fullname }}</p>
                                 <p class="font14 color28 mb-0">{{ $auth_user->email }}</p>
                              </div>
                           </div>
                           <a class="dropdown-item padding_manage acc_anchor border-bottom {{ Request::segment(1)=='flight-bookings' ? 'pro_rel' : '' }}" href="{{ url('flight-bookings') }}">My Account</a>
                           @if(in_array('grocery',Session::get('access')))
                           <a class="dropdown-item padding_manage acc_anchor border-bottom {{ Request::segment(1)=='account-settings' ? 'pro_rel' : '' }}" href="{{ url('account-settings') }}">Grocery Settings</a>
                           @endif
                           @if(in_array('travel',Session::get('access')))
                           <a class="dropdown-item padding_manage acc_anchor border-bottom {{ Request::segment(1)=='my-bookings' ? 'pro_rel' : '' }}" href="{{ url('my-bookings') }}">Hotel Bookings</a>
                           @endif
                           <a class="dropdown-item padding_manage" href="#" data-toggle="modal" data-target="#rateusModal">Rate Us</a>
                           <a class="dropdown-item padding_manage" href="#" data-toggle="modal" data-target="#referModal">Refer & Earn </a>
                           <a class="dropdown-item padding_manage" href="{{ url('chat-support') }}">Chat Support </a>
                           <a class="dropdown-item padding_manage" href="{{ url('chat-support') }}">Help & Support </a>
                           <a class="dropdown-item padding_manage" href="{{ url('javatraininginchennaiomr') }}">Java </a>
                           <a class="dropdown-item padding_manage" href="{{ url('seleniumtraininginchennaiomr') }}">Selenium </a>
                           <a class="dropdown-item padding_manage" href="{{ url('apitestingtraininginchennaiomr') }}">Automation Form </a>
                           <a class="dropdown-item padding_manage" href="{{ url('softwaretestingtraininginchennaiomr') }}">Software Testing </a>
                           <div class="pro_acc">
                              <a class="dropdown-item acc_anchor border-top color_log" href="{{ route('weblogout') }}">Logout</a>
                           </div>
                        </div>
                     </li>
                     <li class="position-relative"><a href="{{ url('notifications') }}" class="icoTwitter" ><i class="fa fa-bell" aria-hidden="true"></i></a><span class="noty badge badge-xs badge-danger position-relative mr-0">{{ $unreadnotification }}</span></li>

                  </div>
               </ul>
            </div>
         </div>
         @endif
         @if(!Auth::guard('front')->check())
         <div class="col-lg-6">
            <div class="social_icons text-right">
               <ul class="social_top_network social-circle">
                  {{--
                  <li><a href="#" class="trans_btn" >Track Order</a></li>
                  --}}
                  <li>
                     <a href="{{ url('/') }}" class="icoLinkedin colorTheme mr-2 btn-modal"> Login</a>
                     <span>|</span>
                     <a href="{{ url('/') }}" class="icoLinkedin btn-modal"> Sign up</a>
                  </li>
                  <li><a href="{{ url('/') }}" class="icoTwitter mr-2 btn-modal"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a></li>
               </ul>
            </div>
         </div>
         @endif
      </div>
   </div>
   </div>
   <div class="navbar_wrapper">
      <nav class="navbar navbar-expand-lg navbar-light bg-white custom_navs">
         <div class="container">
            <div class="row w-100 align-items-center">
               <div class="">
                  <a class="navbar-brand" href="{{ url('/') }}"><img src="{{ URL::asset('front/images/logo.png') }}" class="img-fluid" width="30%"></a>
               </div>
               <div class="nav_menu_wrapper" style="width:50%;justify-content: center;display: grid;">
                  <div class="collapse navbar-collapse" id="navbarResponsive">
                     <ul class="navbar-nav ml-auto custom_nav"  style="display:contents;">
                        <li class="nav-item @if(Request::segment(1)=='')active @endif" style="padding: 10px 15px;">
                           <a class="nav-link" href="{{ url('/') }}"><span>HOME</span></a>
                        </li>
                        {{-- <li class="nav-item @if(Request::segment(1)=='certifications')active @endif" style="padding: 10px 15px;">
                           <a class="nav-link" href="{{ url('/certifications') }}"><span>CERTIFICATIONS</span></a>
                        </li> --}}
                        <li class="dropdown" style="padding: 10px 15px;">
                           <a href="#" class="dropdown-toggle" data-toggle="dropdown">COURSES <b class="caret"></b></a>
                           <ul class="dropdown-menu multi-level">
                              <!-- <li><a href="#">Level 1</a></li> -->
                              <li class="dropdown-submenu">
                                 <a href="{{ url('javatraininginchennaiomr') }}" style="margin: 10px;">Java</a><br/>
                              </li>
                              <li class="dropdown-submenu">
                                 <a href="{{ url('seleniumtraininginchennaiomr') }}" style="margin: 10px;">Selenium</a><br/>
                              </li>
                              <li class="dropdown-submenu">
                                 <a href="{{ url('apitestingtraininginchennaiomr') }}" style="margin: 10px;">Automation Form</a><br/>
                              </li>
                              <li class="dropdown-submenu">
                                 <a href="{{ url('softwaretestingtraininginchennaiomr') }}" style="margin: 10px;">Software Testing</a><br/>
                              </li>
                           </ul>
                        </li>
                        @if(in_array('database',Session::get('access')))
                        <li class="nav-item" style="padding: 10px 15px;">
                           <a href="https://omrbranch.com/user-database" class="nav-link" target="_blank">Database</a>
                        </li>
                        @endif
                        @if(in_array('admin_panel',Session::get('access')))
                        <li class="nav-item" style="padding: 10px 15px;">
                           <a href="/user-admin" class="nav-link" target="_blank">Admin Panel</a>
                        </li>
                        @endif
                        @if(in_array('sample_api',Session::get('access')))
                        <li class="nav-item @if(Request::segment(1)=='sample-api')active @endif" style="padding: 10px 15px;">
                           <a href="{{ url('sample-api') }}" class="nav-link" >Sample API</a>
                        </li>
                        @endif
                     </ul>
                  </div>
               </div>
            </div>
         </div>
         <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
         <span class="navbar-toggler-icon"></span>
         </button>
   </div>
   </nav>
   </div>
</header>
@if(isset($usercartsitem))
@include('front.products.partial-script')
@include('front.cart.partial-script')
@include('front.travel.layouts.partials.partial-address-script')
@endif