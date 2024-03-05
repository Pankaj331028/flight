@extends('front.layouts.master')
@section('template_title','Offer')

@section('content')
<section class="inner_heading my-5">
    <div class="container">
      <div class="font35 font-weight-bold color11 text-center pageTitle">Offer</div>
    </div>
  </section>
  <section class="offer_wrapper">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="sectionTitleBlock d-flex mb-5">
              
              <p class=" font14 font-weight-bold color11">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
              tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
              quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
              consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
              cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
              proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="offer_grid">
            <div class="offer_img_wrapper">
              <img src="images/offer1.jpg" style="width : 100% ; height : 250px; object-fit: cover;">
            </div>
            <div class="offer_content">
              <div class="rel_content font20 font-weight-bold">
                Offer Title Here !!
              </div>
              <p>Up to 25% off on Grocery & Staples & much more</p>
              <div class="offer_btn text-center">
                 <a class="hover1 font16 fontsemibold colorWhite bgTheme px-4 py-1 radius50" href="javascript:;">Avail Offer</a>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="offer_grid">
            <div class="offer_img_wrapper">
              <img src="images/offer2.jpg" style="width : 100% ; height : 250px; object-fit: cover;">
            </div>
            <div class="offer_content">
              <div class="rel_content font20 font-weight-bold">
                Offer Title Here !!
              </div>
              <p>Up to 25% off on Grocery & Staples & much more</p>
              <div class="offer_btn text-center">
                 <a class="hover1 font16 fontsemibold colorWhite bgTheme px-4 py-1 radius50" href="javascript:;">Avail Offer</a>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="offer_grid">
            <div class="offer_img_wrapper">
              <img src="images/offer3.jpg" style="width : 100% ; height : 250px; object-fit: cover;">
            </div>
            <div class="offer_content">
              <div class="rel_content font20 font-weight-bold">
                Offer Title Here !!
              </div>
              <p>Up to 25% off on Grocery & Staples & much more</p>
              <div class="offer_btn text-center">
                 <a class="hover1 font16 fontsemibold colorWhite bgTheme px-4 py-1 radius50" href="javascript:;">Avail Offer</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection