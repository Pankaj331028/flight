<footer class="footer">
    <div class="footer_bg">
        <div class="footer_top">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="f_logo_wrapper text-center">
                            <img src="{{URL::asset('/front/images/f-logo.png')}}" >
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="footer_grid_wrapper">
                            <div class="footer_para">
                            </div>
                            <div class="footer_heading mb-4">
                                <h2> <span>Follow Us On</span> </h2>
                            </div>
                            <ul class="footer_media">
                                <li>
                                    <a href="https://www.facebook.com/gogroceryclub/">
                                        <i class="fa fa-facebook" aria-hidden="true"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://www.instagram.com/GoGroceryClub/">
                                        <i class="fa fa-instagram" aria-hidden="true"></i>
                                    </a>
                                </li>
                            </ul>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="footer_grid_wrapper">
                            <div class="footer_heading mb-5">
                                <h2> <span>Useful Links</span> </h2>
                            </div>
                            <ul class="footer_list">
                                <li><a href="{{ url('pages/about-us') }}">About Us </a></li>
                                <li><a href="{{ url('view/offer/0') }}" >Offers </a></li>
                                <li><a href="{{ url('pages/faq') }}">FAQs </a></li>
                                <li><a href="{{ url('pages/delivery-faq') }}">Delivery FAQ </a></li>
                                <li><a href="{{ url('pages/privacy-policy') }}">Privacy Policy </a></a></li>
                                <li><a href="{{ url('pages/customer-care') }}">Customer Care </a></li>
                                <li><a href="{{ url('/pages/terms-conditions') }}">Terms & Conditions </a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="footer_grid_wrapper">
                            <div class="footer_heading mb-5">
                                 <h2> <span>Download Our App</span> </h2>
                            </div>
                            <div class="app_btn_wrapper ">
                                <a href="{{\App\Library\Notify::getBusRuleRef('ios_url_user') }}"><img class="mr-3" src="{{URL::asset('front/images/app1.png')}}" alt=""></a>
                                <a href="{{\App\Library\Notify::getBusRuleRef('android_url_user') }}"><img src="{{URL::asset('front/images/app2.png')}}" alt=""></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer_bottom text-center">
            <p>Copyright © omrbranch, {{ date('Y') .'-'. date('Y', strtotime('+1 year'))}}</p>
        </div>
    </div>
</footer>
