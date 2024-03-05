@extends('front.automation.master')
@section('template_title','selenium')
@push('css')
<style>
  .congrat {
    background: #27c148;
    color: #fff;
    border-radius: 5px;
    text-align: center;
    font-size: 22px;
    padding: 9px 0px;
    margin-bottom: 20px;
  }

  .table td,
  .table tr {
    padding: 0px;
  }

  .table th {
    padding: 0px;
  }

  .contugorm {
    background-color: #eff1f1;
    font-size: 18px;
  }

  .contugorm:focus {
    box-shadow: none;
    border: 1px solid #6cc36c;
  }

  .contugormt {
    font-size: 18px;
  }

  button.btn.bld {
    width: 284px;
    color: #fff;
    position: relative;
    background-color: #ff7200;
    border: 1px solid #ff7200;
    font-weight: 600;
  }

  button.btn.bld:focus {
    box-shadow: none;
  }

  .button-alignment {
    display: flex;
    text-align: center;
    justify-content: space-between;
    align-items: center;
    height: 500px;
  }

  .ui-widget-header {
    position: relative;
    line-height: auto;
    width: auto;
    padding: 6px;
    margin: 0;
    font-size: 20px;
    font-weight: bold;
    color: #5d6b6c;
    font-size: 18px !important;
    text-shadow: 0 1px rgba(255, 255, 255, 0.7);
    background: white;
    border-radius: 3px 3px 0 0;
    background-image: -webkit-linear-gradient(top, #f5f7fd, #e6eaec);
    background-image: -moz-linear-gradient(top, #f5f7fd, #e6eaec);
    background-image: -o-linear-gradient(top, #f5f7fd, #e6eaec);
    background-image: linear-gradient(to bottom, #f5f7fd, #e6eaec);
    -webkit-box-shadow: inset 0 1px rgba(255, 255, 255, 0.5), 0 1px rgba(0, 0, 0, 0.03);
    box-shadow: inset 0 1px rgba(255, 255, 255, 0.5), 0 1px rgba(0, 0, 0, 0.03);
  }

  .section2
  {
    height: 100vh;
  }

  .shoppingCart {
    width: 100%;
    border: 0px solid #ccc;
  }

  .clear {
    clear: both;
    display: block;
  }

  .f_right {
    float: right;
  }

  .f_left {
    float: left;
  }

  .hidden {
    display: none;
  }

  #main {
    border: 0px ridge #3399cc;
    width: 700px;
    padding: 10px;
  }

  #one {
    height: 280px;
  }

  #two {
    height: 280px;
  }

  #three {
    height: 280px;
  }

  #four {
    height: 280px;
  }

  .perform-click {
    padding: 10px;
    color: #fff;
    font-weight: 500;
    cursor: default;
    font-size: 18px;
  }

  .cursorimgd {
    position: relative;
    bottom: 48px;
    right: -144px;
    display: block;
    width: 72px;
    z-index: 100;
  }

  .left_side {
    width: 220px;
    border: 2px solid #039;
    text-align: center !important;
    float: left;
    margin: 0px auto 0px 7px;

  }

  #dragcenter {
    padding: 0px auto 0px auto;
    text-align: center;
    border: 0px dashed #ff0000;
    width: 100%;
    height: 150px;
    margin-top: 10px;
    margin-left: 5px;
  }

  .section3{
    margin-top: 20%;
  }

  #object1,
  #object2,
  #object3,
  #object4,
  #object5,
  #object6,
  #object7,
  #object8 {
    font-size: 18px !important;
    width: 146px;
    background-color: #1e8da8 !important;
    color: #fff !important;
    cursor: pointer;
    text-align: center;
    padding: 6px;
    margin: 4px;
    float: left;
    -webkit-border-radius: 10px;
    -moz-border-radius: 10px;
    border-radius: 10px;
    /* Prevent text from being selectable */
    -webkit-touch-callout: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    -o-user-select: none;
    user-select: none;
  }

  #feedback {
    border: 0px solid #ff0000;
    float: left;
    background-color: transparent;
    color: #000000;
    width: 40%;
    margin: 0px;
    padding: 0px 0px 0px 0px;
  }

  #logo {
    border: 0px ridge #33ff00;
    float: right;
    width: 158px;
    padding: 0px 0px 0px 0px;
    text-align: center;
  }

  .object-dragon {
    border-radius: 0px !important;
    background: #f1f3f8 !important;
    color: #747c7c !important;
    border: 0px !important;
    font-weight: 600;
    box-shadow: 0px 0px 2px 0px #b5cdcf;
    font-size: 20px !important;
  }

  .sec1 {
    font-size: 22px;
    font-weight: 600;
  }

  .button-click
  {
    height: 20px;
  }

  @media(max-width:1399.98px) {
    .shoppingCart {
      width: 100% !important;
    }
  }

  @media(max-width:767.98px) {
    .shoppingCart {
      width: 100% !important;
      height: 86px;
    }

    #object1,
    #object2,
    #object3,
    #object4,
    #object5,
    #object6,
    #object7,
    #object8 {
      font-size: 13px !important;
      width: 84px;
    }

    .button-alignment {
      height: 100%;
      display: block;
    }

    button.btn.bld {
      position: relative;
    }

    .ui-widget-header {
      padding: 2px;
      font-size: 15px !important;
    }

    .sec1 {
      font-size: 18px;
    }

    button.btn.bld {
      width: 233px;
    }

    #dragcenter {
      height: 66px;
    }

    .perform-click {
      font-size: 16px;
    }

    .contugormt {
      font-size: 16px;
    }

    .contugorm {
      font-size: 16px;
    }

    .section3 {
      margin-top: 0;
    }
  }

  @media(max-width:575.98px) {
    .table-responsive-sm {
        display: table;
    }
    .ui-widget-header {
        font-size: 14px !important;
    }

    #object1, #object2, #object3, #object4, #object5, #object6, #object7, #object8 {
        font-size: 10px !important;
        padding: 2px 0px;
        width: 65px;
    }
    .button-click {
        height: 0;
    }
    button.btn.bld {
        font-size: 13px;
        padding: 3px;
    }

    .perform-click {
        font-size: 13px;
        padding: 7px;
    }
    .cursorimgd {
        width: 40px;
        bottom: 36px;
        right: -100px
    }
    .sec1 {
        font-size: 16px;
    }
    .contugorm {
        font-size: 13px;
    }
    .contugormt {
        font-size: 14px;
    }

    .button-alignment div
    {
      height: 80px;
    }

    .button-alignment h6
    {
      font-size: 12px;
    }
  }

  @media(max-width:479.98px) {
    
    .cursorimgd {
      right: -110px;
      width: 44px;
      bottom: 38px;
    }

    .ui-widget-header {
      font-size: 13px !important;
    }

    #object1,
    #object2,
    #object3,
    #object4,
    #object5,
    #object6,
    #object7,
    #object8 {
      font-size: 10px !important;
      width: 64px;
    }

    .sec1 {
      font-size: 16px;
    }

    button.btn.bld {
      font-size: 13px !important;
      padding: 0px;
    }

    .section3 {
      margin-top: 0;
    }.section1 {
      padding: 0;
    }

    .button-alignment {
      display: block;
      margin-top: 27px;
    }

    .perform-click {
      font-size: 13px;
    }
  }
</style>
@endpush
@section('content')
<div class="container my-5">
  <section class="section1">
    <div class="container">
      <h2 class="sec1">Section 1</h2>
      <div class="row" id="section1">
        <div class="col-md-12">
          <table class="table table-bordered  table-responsive-sm">
            <thead>
              <tr>
                <th colspan="2">
                  <h3 align="center" class="ui-widget-header border-bootm ">Java</h3>
                </th>
                <th colspan="2">
                  <h3 align="center" class="ui-widget-header border-bootm ">Selenium</h3>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <h3 align="center" class="ui-widget-header">Class</h3>
                </td>
                <td>
                  <h3 align="center" class="ui-widget-header">Interface</h3>
                </td>
                <td>
                  <h3 align="center" class="ui-widget-header">Class</h3>
                </td>
                <td>
                  <h3 align="center" class="ui-widget-header">Interface</h3>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="shoppingCart" id="shoppingCart1">
                    <div class=" def-1" id="one">
                      <div class="card-body">
                      </div>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="shoppingCart" id="shoppingCart4">
                    <div class=" def-2" id="two">
                      <div class="card-body">
                      </div>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="shoppingCart" id="shoppingCart3">
                    <div class=" def-4" id="three">
                      <div class="card-body">
                      </div>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="shoppingCart" id="shoppingCart4">
                    <div class=" def-4 ref-right" id="four">
                      <div class="card-body">
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>

        </div>
        <div class="col-md-12">
          <div class="clear"></div>

          <div id="dragcenter">
            <div><span id="object1" class="object-dragon">List</span></div>
            <div><span id="object2" class="object-dragon">String</span></div>
            <div><span id="object3" class="object-dragon">Enum</span></div>
            <div><span id="object4" class="object-dragon">Int</span></div>
            <div><span id="object5" class="object-dragon">Webdriver</span></div>
            <div><span id="object6" class="object-dragon">Actions</span></div>
            <div><span id="object7" class="object-dragon">Keys</span></div>
            <div><span id="object8" class="object-dragon">Testing</span></div>
          </div>
          <!--end dragcenter-->

          <div class="clear"></div>

          <div id="feedback"></div>
          <div class="clear"></div>
        </div>
      </div>
      <!--end main-->
    </div>
  </section>
  <section class="section2 px-4 mx-2" id="sec2">
      <div class="row" id="section2">
        <h2 class="sec1">Section 2</h2>
        <div class="button-alignment col-md-12">
          <div class="col-md-4">
            <button class="btn bld mb-4" type="button" onclick="current_date();">Click to see current date</button>
            <h6 id="display" class="button-click"></h6>
          </div>
          <div class="col-md-4">
            <button class="btn bld mb-4" type="button" onclick="future_date();">Click to see future date</button>
            <h6 id="display1" class="button-click"></h6>
          </div>
          <div class="col-md-4">
            <button class="btn bld mb-4" type="button" onclick="myFunction()">Right click to enter past date</button>
            <h6 id="demo" class="button-click"></h6>
          </div>
        </div>
      </div>
  </section>
  <section class="section3 px-4 mx-2">
    <div class="container">
      <div class="row" id="section3">
        <h2 class="sec1">Section 3</h2>
        <div class="col-md-12 mt-5">
          <center>
            <div style="background: #6cc36c;" class="position-relative">
              <h5 class="perform-click" id="display-watch">Perform js click to display clock</h5>
            </div>
            <img src="{{ asset('front/images/cursor-img.png') }}" alt="img of cursor poing" class="cursorimgd">
            <img src="{{ asset('front/images/stopwatch.jpg')}}" alt="" width="200" id="image" class="d-none">
            <div style="background: #6cc36c;" id="show-contact">
              <h5 class="perform-click">Perform js click to enable contact field</h5>
            </div><br>
            <form action="" style="text-align:left;">
              <label class="contugormt my-2" for=""><b>Contact Number</b></label>
              <div class="input-group  mb-2" style="width: 100%;">
                <input type="text" class="form-control contugorm" id="inlineFormInputGroup" placeholder="Contact Number"
                  maxlength="10" disabled>
              </div>
            </form>
          </center>


        </div>
      </div>
    </div>
  </section>

</div>
@endsection
@push('js')
<script>
  var object1 = document.getElementById('object1');
  var object2 = document.getElementById('object2');
  var object3 = document.getElementById('object3');
  var object4 = document.getElementById('object4');
  var object5 = document.getElementById('object5');
  var object6 = document.getElementById('object6');
  var object7 = document.getElementById('object7');
  var object8 = document.getElementById('object8');

  var objArray = ['#object1', '#object2', '#object3', '#object4', '#object5', '#object6', '#object7', '#object8'];
  var startDrag = '';
  var resetter = 0;

  //jQ
  var j = jQuery.noConflict();
  j(document).ready(function () {
    //draggin
    j('#object1, #object2, #object3, #object4,#object5, #object6, #object7, #object8').draggable({
      start: function (event, ui) {
        startDrag = ui.position;
      },
      containment: '#main',
      cursor: 'move',
      revert: function(valid)
      {
      if(!valid)
        {
        j('#feedback').html('<p style="color:red;" class="tries">Try again!</p>');
        return true;
        }
      },
      stack: 'div', //bring it to the top by adjusting z-index of the element
      drag: clearer,
      stop: function (event, ui) {}
    });

    //droppables
    j('#one').droppable({
      drop: right,
      accept: '#object2'
    });
    j('#two').droppable({
      drop: right,
      accept: '#object1'
    });
    j('#three').droppable({
      drop: right,
      accept: '#object6'
    });
    j('#four').droppable({
      drop: right,
      accept: '#object5'
    });


  }); //end on doc load

  //EXTERNAL METHODS

  //clear the feedback div onDrag
  function clearer(event, ui) {
    j('#feedback').html('');
  }

  //if the dropTarget is correct
  function right(event, ui) {
    var draggable = ui.draggable;
    draggable.draggable('disable');
    draggable.draggable('option', 'revert', false); //turn revert off
    draggable.css('background-color', '#ffffff');
    draggable.css('color', '#09C');
    resetter++;
    if (resetter == 4) {
      j('#feedback').html('<h3 class="greentext congrat">Perfect Match !</h3>');
      j('#logo').html('<button type="button" onclick="resetIt()">Reset</button>');
    } else {
      //do nothing
    }
  }

  //on reset button click
  function resetIt() {
    resetter = 0;
    j('#feedback').html('');
    //console.log(objArray);
    for (x = 0; x < objArray.length; x++) {
      j(objArray[x]).css('left', '0');
      j(objArray[x]).css('top', '0');
      //j(objArray[x]).css('float', 'left');
      j(objArray[x]).css('background-color', '#09C');
      j(objArray[x]).css('color', '#fff');
      j(objArray[x]).draggable('enable'); //re-enable the draggable state
      j(objArray[x]).draggable({
        revert: function (valid) //gotta recall the revert function
        {
          if (!valid) {
            //this.remove();
            j('#feedback').html('<h3>Try again!</h3>');
            return true;
          }
        }
      });
    }
  }
</script>

<script type="text/javascript">
  function current_date() {
    var current_date = new Date();
    alert(current_date);
    document.getElementById('display').innerHTML = "You pressed ok! Current date is - "+current_date
  }
</script>
<script type="text/javascript">
  function future_date() {
    var date = new Date();
    var future_date = new Date(date. getFullYear(), date. getMonth() + 1, 1)

    if (confirm("Future date is - "+future_date)){
      document.getElementById('display1').innerHTML = "You pressed ok! Future date is - "+future_date
    } else {
      document.getElementById('display1').innerHTML = "Wooh! You Pressed Cancel!"
    }
  }
</script>
<script>
  function myFunction() {
    let text;
    let person = prompt("Please enter your past date:");
    if (person == null || person == "") {
      text = "Wooh! You Pressed Cancel !";
    } else {
      text = "You entered " + person + "as past date and pressed OK!";
    }
    document.getElementById("demo").innerHTML = text;
  }
</script>
<script>
  $("#display-watch").on('click', function () {
    $("#image").removeClass("d-none");
  });
  $("#show-contact").on('click', function () {
    $("#inlineFormInputGroup").removeAttr("disabled");
  });
</script>
<script>
  $('.section2 , .section3').hide();

  $(window).scroll(function () {
    if (window.innerHeight + window.scrollY > 640 && window.innerHeight + window.scrollY < 790) {
      console.log('2')
      $('.section1').hide(1000)
      $('.section2').show(1000)
    }else if (window.innerHeight + window.scrollY > 790) {
      console.log('3')
      $('.section2').hide(1000)
      $('.section3').show(1000)
    }else if(window.innerHeight + window.scrollY <= 640)
    {
      console.log('1')
      $('.section1').show(1000)
      $('.section2').show(1000)
      $('.section3').hide(1000)
    }

    console.log(window.innerHeight + window.scrollY);
  });

</script>
@endpush