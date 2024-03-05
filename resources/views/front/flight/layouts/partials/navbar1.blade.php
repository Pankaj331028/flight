


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
                     <li><a href="{{ url('track-order-no') }}" class="trans_btn" >Track Order</a></li>
                     <li class="dropdown">
                        <a href="#" class="icoTwitter mr-2 dropdown-toggle" data-testid="username" data-toggle="dropdown">
                        {{ $auth_user->first_name }}
                        </a>
                        <div class="dropdown-menu pro_display">
                           <div class="userInfo d-flex align-items-center bgf7 px-3 py-2">
                              <div class="acHolderImg"><img src="{{ !empty($auth_user->profile_picture) ? asset('uploads/profiles/'.$auth_user->profile_picture) : asset('uploads/profiles/no_avatar.jpg') }}" alt="profile"></div>
                              <div class="holderNameandMail flex-fill ml-3 text-left">
                                 <p class="font16 color28 mb-0 font-weight-semibold">{{ $auth_user->fullname }}</p>
                                 <p class="font14 color28 mb-0">{{ $auth_user->email }}</p>
                              </div>
                           </div>
                           <a class="dropdown-item padding_manage acc_anchor border-bottom {{ Request::segment(1)=='account-settings' ? 'pro_rel' : '' }}" href="{{ url('account-settings') }}">My Account</a>
                           <a class="dropdown-item padding_manage {{ Request::segment(1)=='my-favourites' ? 'pro_rel' : '' }}"" href="{{ url('my-favourites') }}">Favourite</a>
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
                     <li class="relative">
                        <div class="cart_dropdown">
                           <a class="cart_btn" href="#"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a>
                           <span class="cart badge badge-xs badge-danger position-relative mr-0 cart_count"> {{ $cart_count }} </span>
                           <div class="cart_innerbox" id="cart_popup">
                              <!-- cart box  -->
                              <div class="row cart_innerbox_main" id="cart_innerbox_main">
                                 <div class="col-12">
                                    <a href="#" class="cart_btn_remove"> Close </a>
                                    @if(isset($usercartsitem))
                                    @foreach ($usercartsitem as $item)
                                    @php
                                    $str = public_path(substr($item->product_image, strpos($item->product_image, 'uploads')));
                                    if (File::exists($str)) {
                                    $medium_image = $item->product_image;
                                    } else {
                                    $medium_image = $item->product_image;
                                    }
                                    @endphp
                                    <div class="row cart_drop_box align-items-center py-3 border-bottom mx-0">
                                       <div class="col-auto pl-2">
                                          <a href="#"><img src="{{ $medium_image }}" alt="cate img"></a>
                                       </div>
                                       <div class="col-6 px-0">
                                          <div class="cart_drop_text text-left">
                                             <a class="ellipsis-1" data-testid="productname" href="#">{{ $item->product_name }}</a>
                                             <p class="font16 color20 fontsemibold mb-1">
                                                @if($item->price == $item->special_price || $item->special_price=='0')
                                                <span>{{ $currency }} {{ $item->price }}</span>
                                                @else
                                                <span>{{ $currency }} {{ $item->special_price }}</span>
                                                <strike class="colorb7 ml-2">{{ $currency }} {{ $item->price }}</strike>
                                                @endif
                                             </p>
                                             <!-- <span>$90 <del>$130</del></span> -->
                                             <div class="product_quantity mt-2">
                                                <div class="input_number_form input-group text_icon mx-0" id="count-{{ $item->product_variation_id }}">
                                                   <div class="value-button input-number-decrement btn-minus" data-product="{{ $item->product_id }}" onclick="addToCartProduct(this,{{ $item->product_variation_id }},'minus')">-</div>
                                                   <span class="number_input col-md-2" id="qty-{{ $item->product_id.$item->product_variation_id }}">{{ $item->qty }}</span>
                                                   <div class="value-button input-number-increment" data-product="{{ $item->product_id }}" onclick="addToCartProduct(this,{{ $item->product_variation_id }},'plus',null,{{ $item->max_qty }})">+</div>
                                                </div>
                                             </div>

                                          </div>
                                       </div>
                                       <div class="col-auto pl-2 pr-1">
                                          <div class="favtag">
                                             <p class="font20 fontsemibold mb-0 p-0">

                                              @if($item->is_favorite==1 )
                                              <a class="favdark" href="javascript:void(0)" data-product="{{ $item->product_id }}" onclick="addToFavourite(this,{{ $item->product_variation_id }})"  data-testid="favorites">
                                                   <i class="fa fa-heart" aria-hidden="true"></i>
                                                   </a>
                                                @else
                                                <a href="javascript:void(0)" data-product="{{ $item->product_id }}" onclick="addToFavourite(this,{{ $item->product_variation_id }})"  data-testid="favorites">
                                                   <i class="fa fa-heart" aria-hidden="true" style="color: #b4bbbb;"></i>
                                                   </a>

                                                @endif

                                             </p>
                                          </div>
                                          <div class="scheduleClose">
                                             <a href="javascript:;" class="allinone close" onclick="deleteCartItem({{ $item->id }})">
                                             <i class="fa fa-close" aria-hidden="true"></i>
                                             </a>
                                          </div>
                                       </div>
                                    </div>
                                    @endforeach
                                    @else
                                    <section class="cartwrapper1 my-5">
                                       <div class="container">
                                          <div class="container text-center mt-5">
                                             <p class="font18 color11">Oops! Looks like you don't have any product in your cart.</p>
                                          </div>
                                       </div>
                                    </section>
                                    @endif
                                 </div>
                              </div>
                              <!-- No Product Available -->
                              <div class="row empty_product">
                                 <div class="col-12">
                                    <p>0 Product Available in cart</p>
                                 </div>
                              </div>
                              <div class="check_out_btn pb-4">
                                 <a class="hover1" href="{{route('my_cart')}}"> Go To Cart </a>
                              </div>
                           </div>
                        </div>
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
            <div class="row justify-content-between w-100 align-items-center">
               <div class="">
                  <a class="navbar-brand" href="{{ url('/') }}"><img src="{{ URL::asset('front/images/logo.png') }}" class="img-fluid" width="30%"></a>
               </div>
               <?php
$categories = \App\Model\Category::where('parent_id', null)->active()->get();
?>
               <div class="nav_menu_wrapper">
                  <div class="collapse navbar-collapse" id="navbarResponsive">
                     <ul class="navbar-nav ml-auto custom_nav"  style="display:contents;">
                        <li class="nav-item @if(Request::segment(1)=='')active @endif">
                           <a class="nav-link" href="{{ url('/') }}"><span>HOME</span></a>
                        </li>
                        <li class="dropdown">
                           <a href="#" class="dropdown-toggle" data-toggle="dropdown">CATEGORIES <b class="caret"></b></a>
                           <ul class="dropdown-menu multi-level">
                              <!-- <li><a href="#">Level 1</a></li> -->
                              @if(isset($categories))
                              @foreach ($categories as $category)
                              <li class="dropdown-submenu">
                                 @if($category->childCatList->count()<=0)
                                 <a href="{{ url('category/groceries/'.$category->id) }}" style="margin: 10px;">{{ $category->name }}</a><br/>
                                 @else
                                 <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="margin: 10px;">{{ $category->name }}</a><br/>
                                 <ul class="dropdown-menu">
                                    @foreach ($category->childCatList as $d)
                                    @php
                                    if($category->subcategory_count>0){
                                    $url = url('category/groceries/'.$d->id);
                                    }else{
                                    $url = url('category/groceries/product/'. $d->id);
                                    }
                                    @endphp
                                    <li><a href="{{$url}}" style="margin: 10px;">{{ $d->name }}</a></li>
                                    @endforeach
                                 </ul>
                                 @endif
                              </li>
                              @endforeach
                              @endif
                           </ul>
                        </li>

                        <li class="nav-item @if(Request::segment(2)=='offer' && Request::segment(3)=='0')active @endif">
                           <a class="nav-link" href="{{ url('view/offer/0') }}"><span>OFFERS</span></a>
                        </li>
                        <li class="nav-item @if(Request::segment(2)=='about-us')active @endif">
                           <a class="nav-link" href="{{ url('pages/about-us') }}"><span>ABOUT US</span></a>
                        </li>
                        {{--
                        <li class="nav-item @if(Request::segment(2)=='contact-us')active @endif">
                           <a class="nav-link" href="{{ url('pages/contact-us') }}"><span>CONTACT US</span></a>
                        </li>
                        --}}
                        <li class="nav-item @if(Request::segment(2)=='customer-care')active @endif">
                           <a class="nav-link" href="{{ url('pages/customer-care') }}"><span>Customer Care</span></a>
                        </li>
                        <li class="nav-item form_nav searchBar ml-md-5 p-relative">
                           <form action="{{url('listing')}}" class="p-relative d-flex">
                           <input type="text" class="search form-control" placeholder="Search Here" name="search" id="search" value="{{Request::get('search')}}">
                           <button data-testid="searchbtn" type="submit" href="javascript:;" class="btn font16 bgTheme colorWhite radius50" style="line-height: 1;position: absolute;right: 0px;border-top-left-radius: 0;border-bottom-left-radius: 0;padding: 9px;"><i class="fa fa-search colorWhite"></i></button>
                        </form>
                           {{--
                           <form>
                              <div class="form_nav">
                                 <label for="keyword">
                                 <span><i class="fa fa-search" aria-hidden="true"></i></span>
                                 </label>
                                 <input type="text" class="form-control" placeholder="Search Here" id="keyword">
                              </div>
                           </form>
                           --}}
                        </li>
                     </ul>
                  </div>
               </div>
               {{--
               <div class="nav-input">
                  <div class="nav_form_wrapper">
                     <form>
                        <div class="form_nav">
                           <label for="email">
                           <span><i class="fa fa-search" aria-hidden="true"></i></span>
                           </label>
                           <input type="text" class="search form-control" placeholder="Search Here" id="email">
                        </div>
                     </form>
                  </div>
               </div>
               --}}
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