@extends('front.automation.master')
@section('template_title','java')

@section('content')
<section class="TextBox mb-5">
    <div class="container">
        <div class="followImg py-2">
            <a href="#"><img src="{{ ('front/images/facebook.png') }}" alt="image of facebook" class="fb-img"></a>
            <a href="#"><img src="{{ ('front/images/twitter.png') }}" alt="image of twitter" class="twitter-img"></a>
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
                    <iframe class="iframe" src="{{ route('java-content') }}" frameborder="0"></iframe>
                    <div id="message"></div>
                </div>
            </div>
            <div class="col-md-3 mt-5">
                <div class="sideBar">
                    <h4>Automation</h4>
                    <ol>
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
                    </ol>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('js')
<script>
    const message = document.getElementById("message");
    window.focus()

    window.addEventListener("blur", () => {
    setTimeout(() => {
        if (document.activeElement.tagName === "IFRAME") {
            window.location.href = "http://velsbusinessclub.vlcare.com";
        }
    });
    }, { once: true });
</script>
@endpush