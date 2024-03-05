@extends('front.layouts.master')
@section('template_title', 'About Us')
@section('content')
<section class="wrapperabout1 my-5">
    <div class="container">
        <div class="font35 font-weight-bold color11 text-center pageTitle">About Us</div>

        <!-- <img src="img_girl.jpg" alt="Girl in a jacket" width="500" height="600"> -->


        <p class="font18 color36 mt-3 mt-md-5">Lorem Ipsum is simply dummy text of the printing and typesetting
            industry. Lorem
            Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a
            galley of type and scrambled it to make a type specimen book. It has survived not only five centuries,
            but also the leap into electronic typesetting, remaining essentially unchanged. </p>
        <p class="font18 color36">It was popularised in the 1960s with the release of Letraset sheets containing
            Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including
            versions of Lorem Ipsum.</p>
    </div>

    <div class="container-fluid p-0">
        <div class="row mt-3 mt-md-5 no-gutters">
            <div class="col-md-6">
                <div class="missionBlock">
                    <div class="sectionTitleBlock d-flex mb-4">
                        <div class="sectionTitle font35 font-weight-bold color11">Our Mission</div>
                    </div>
                    <div class="missionContent">
                        <p class="font14 color36">Lorem Ipsum is simply dummy text of the printing and
                            typesetting
                            industry. Lorem Ipsum has been the industry's standard dummy text ever since the
                            1500s,
                            when an unknown printer took a galley of type and scrambled it to make a type
                            specimen
                            book. It has survived not only five centuries, but also the leap into electronic
                            typesetting, remaining essentially unchanged. </p>
                        <p class="font14 color36">It was popularised in the 1960s with the release of Letraset
                            sheets containing Lorem Ipsum passages, and more recently with desktop publishing
                            software like Aldus PageMaker including versions of Lorem Ipsum.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="missionImgBlock float_ad">
                    <img src="{{ asset('/front/images/about/img1.jpg') }}" alt="">
                </div>
            </div>
        </div>


        <div class="row mt-3 mt-md-5 no-gutters grid_res">
            <div class="col-md-6">
                <div class="vissionImgBlock">
                    <img src="{{ asset('/front/images/about/img2.jpg') }}" alt="">
                </div>
            </div>
            <div class="col-md-6">
                <div class="vissionBlock">
                    <div class="sectionTitleBlock d-flex mb-4">
                        <div class="sectionTitle font35 font-weight-bold color11">Our Vision</div>
                    </div>
                    <div class="vissionContent">
                        <p class="font14 color36">Lorem Ipsum is simply dummy text of the printing and
                            typesetting
                            industry. Lorem Ipsum has been the industry's standard dummy text ever since the
                            1500s,
                            when an unknown printer took a galley of type and scrambled it to make a type
                            specimen
                            book. It has survived not only five centuries, but also the leap into electronic
                            typesetting, remaining essentially unchanged. </p>
                        <p class="font14 color36">It was popularised in the 1960s with the release of Letraset
                            sheets containing Lorem Ipsum passages, and more recently with desktop publishing
                            software like Aldus PageMaker including versions of Lorem Ipsum.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
