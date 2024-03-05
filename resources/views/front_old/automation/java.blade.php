@extends('front.automation.master')
@section('template_title','java')

@section('content')
<section class="TextBox mb-5">
    <div class="container">
        <h4 class="mt-5 text-capitalize">{{ $title ?? '' }}</h4>
        <div class="followImg py-2">
            <a href="#"><img src="{{ asset('front/images/facebook.png') }}" alt="image of facebook" class="fb-img"></a>
            <a href="#"><img src="{{ asset('front/images/twitter.png') }}" alt="image of twitter" class="twitter-img"></a>
        </div>
        {{-- <h4 class="discount">"Call us for course fees and attractive discounts"</h4> --}}
        <div class="row ">
            <div class="col-md-9 box-div">
                <div class="boxContent">
                    <p>We undertake <strong>Classroom Training, Corporate Training and Video Based Training </strong>on
                        latest Technologies on latest versions. we make sure that all
                        our sessions are very much interactive and well structured. We encourage every participant to
                        come up with his / her
                        own queries during & after the training sessions. We prefer practical depth of the technology.
                        We provide excellent Lab Handouts
                        for practice including. Realtime Case Studies and Projects on ALL courses. Specific <strong>DAY
                            to DAY Course Plan</strong> will also be shared prior
                        to training registration to ensure transparency of our Training services.</p>
                </div>
                <div>
                    <iframe name="java-content" id="java-content" class="iframe" src="{{ route('java-content') }}" frameborder="0"></iframe>
                    <div id="message"></div>
                </div>
            </div>
            <div class="col-md-3 mt-5">
                <div class="sideBar">
                    <iframe class="iframe" src="{{ asset('public/html/automation.html') }}" frameborder="0" height="500"></iframe>
                    {{-- <ol>
                        <li class="li-sub-heading">Core Java
                            <ul>
                                <span>1.1 OOPS</span>
                                <li><a href="">Inheritance</a></li>
                                <li><a href="">Abstraction</a></li>
                                <li><a href="">Polymorphism</a></li>
                                <li><a href="">Encapsulation</a></li>
                            </ul>

                            <ul>
                                <span>1.2 Collections</span>
                                <li><a href="">List</a></li>
                                <li><a href="">Set</a></li>
                                <li><a href="">Map</a></li>
                            </ul>
                        </li>
                        <li class="li-sub-heading">Selenium
                            <ul>
                                <span>2.1 Actions</span>
                                <li><a href="">MovetoEle</a></li>
                                <li><a href="">Click</a></li>
                                <li><a href="">DoubleClick</a></li>
                                <li><a href="">Right Click</a></li>
                            </ul>
                        </li>
                    </ol> --}}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('js')
<script>
    window.onload = function() {
    var oFrame = document.getElementById("java-content");
        oFrame.contentWindow.document.onclick = function() {
            window.location.href = "https://www.omrbranch.com";
        };
    };
</script>
@endpush