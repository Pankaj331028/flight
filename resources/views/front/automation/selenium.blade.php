@extends('front.automation.master')
@section('template_title','selenium')
@push('css')
<style>
    html,
    body {
        height: 100%;
        overflow: hidden;
        position: relative;
    }

    .loop {
        position: relative;
        height: 100%;
        overflow: scroll;
        -webkit-overflow-scrolling: touch;
        scroll-snap-type: y mandatory;
        scroll-behavior: smooth;
    }

    section {
        position: relative !important;
        text-align: center !important;
        height: 100% !important;
        scroll-snap-align: start !important;
        padding:50px;
    }

    ::scrollbar {
        display: none;
    }

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
        width: 20%;
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

    #section1,#section2,#section3 {
        margin: 0;
        width: 100%;
    }

    #main {
        border: 0px ridge #3399cc;
        width: 700px;
        padding: 10px;
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
        right: -20px;
        display: block;
        width: 72px;
        z-index: 100;
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
        -webkit-touch-callout: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        -o-user-select: none;
        user-select: none;
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
        font-weight: 600;
    }

    .button-click {
        height: 20px;
    }

    @media(max-width:767.98px) {
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
    }

    @media(max-width:575.98px) {
        .table-responsive-sm {
            display: table;
        }

        .ui-widget-header {
            font-size: 14px !important;
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

        .button-alignment div {
            height: 80px;
        }

        .button-alignment h6 {
            font-size: 12px;
        }
    }

    @media(max-width:479.98px) {

        .cursorimgd {
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
            /* margin-top: 0; */
        }

        .section1 {
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

    /* ------ */
    #products {
        color: #000000;
        background: #ffffff;
    }

    .ui-widget-content ul li {
        display: block;
        float: left;
        line-height: 30px;
        list-style: none;
        margin: 0 0px;
        text-decoration: blink;
    }

    .ui-widget-content {
        min-height: 100%;
        text-align: center;
    }

    table {
        width: 100%;
    }

    #e1,
    #result {
        display: none;
        text-align:left;
    }

    .button {
        position: relative;
        display: inline-block;
        vertical-align: top;

        height: 36px;
        line-height: 35px;
        padding: 0 20px;
        font-size: 150%;
        color: #000000;
        text-align: center;
        text-decoration: none;
        text-shadow: 0 -1px rgba(0, 0, 0, 0.4);
        background-clip: padding-box;
        border: 1px solid;
        border-radius: 2px;
        cursor: pointer;
        -webkit-box-shadow: inset 0 1px rgba(255, 255, 255, 0.1), inset 0 0 0 1px rgba(255, 255, 255, 0.08), 0 1px 2px rgba(0, 0, 0, 0.25);
        box-shadow: inset 0 1px rgba(255, 255, 255, 0.1), inset 0 0 0 1px rgba(255, 255, 255, 0.08), 0 1px 2px rgba(0, 0, 0, 0.25);
    }

    .button:before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        color: #FFFFFF;
        pointer-events: none;
        background-image: -webkit-radial-gradient(center top, farthest-corner, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0));
        background-image: -moz-radial-gradient(center top, farthest-corner, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0));
        background-image: -o-radial-gradient(center top, farthest-corner, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0));
        background-image: radial-gradient(center top, farthest-corner, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0));
    }

    .button:hover:before {
        background-image: -webkit-radial-gradient(farthest-corner, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0.03));
        background-image: -moz-radial-gradient(farthest-corner, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0.03));
        background-image: -o-radial-gradient(farthest-corner, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0.03));
        background-image: radial-gradient(farthest-corner, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0.03));
    }

    .button:active {
        -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.2);
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.2);
        color: #FFFFFF;
    }

    .button:active:before {
        content: none;
        color: #FFFFFF;
    }

    .button-orange {
        background: #4DA6A6;
        margin-right: 10px;
        font-weight: 1900;
        border-color: #2f3034 #2f3034 #232427;
    }

    .button-orange:active {
        background: #4DA6A6;
        border-color: #232427 #2f3034 #2f3034;
    }

    li.ui-draggable-dragging {
        list-style: none;
    }

    .ui-widget-header {
        position: relative;
        line-height: auto;
        width: auto;
        padding: 7px 15px;
        font-weight: bold;
        color: #5d6b6c;
        text-shadow: 0 1px rgba(255, 255, 255, 0.7);
        background: white;
        border-bottom: 1px solid #d1d1d1;
        border-radius: 3px 3px 0 0;
        background-image: -webkit-linear-gradient(top, #f5f7fd, #e6eaec);
        background-image: -moz-linear-gradient(top, #f5f7fd, #e6eaec);
        background-image: -o-linear-gradient(top, #f5f7fd, #e6eaec);
        background-image: linear-gradient(to bottom, #f5f7fd, #e6eaec);
        -webkit-box-shadow: inset 0 1px rgba(255, 255, 255, 0.5), 0 1px rgba(0, 0, 0, 0.03);
        box-shadow: inset 0 1px rgba(255, 255, 255, 0.5), 0 1px rgba(0, 0, 0, 0.03);
    }

    .tasks {
        margin: 50px auto;
        width: 240px;
        background: white;
        border: 1px solid #cdd3d7;
        border-radius: 4px;
        -webkit-box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .tasks-title {
        line-height: inherit;
        font-size: 14px;
        font-weight: bold;
        color: inherit;
    }

    /* Highlight related placeholder background color when appropriate block is selected */
    .content-active {
        background-color: #98AFC7;
    }

    /*  highlight related placeholder color on hovering appropriate block*/
    .content-hover {
        background-color: #98AFC7;
    }

    .ui-droppable {
        min-height: 50px;

    }

    #message1,
    #message5,
    #message7,
    #message11,
    #message14 {
        display: none;
        color: Red
    }

    #message2,
    #message4,
    #message8,
    #message10,
    #message13 {
        display: none;
        color: green
    }

    #message3,
    #message6,
    #message9,
    #message12,
    #message15,
    #notequal {
        display: none;
    }

    #s2 {
        display: none;
    }

    input[type=text] {
        background: transparent;
        border: none;
        border-bottom: 1px solid #000000;
        padding: 2px 5px;
    }

    #eb,
    #ebdiv,
    #ebdivnext#asset,
    #s1,
    #s2 {
        display: none;
    }

    .eb1 {
        display: none;
    }

    .ebdiv1 {
        display: none;
    }

    .ebdivnext1 {
        display: none;
    }

    .asset {
        display: none;
    }

    .s1 {
        display: none;
    }

    .s2 {
        display: none;
    }

    input[type=text] {
        background: transparent;
        border: none;
        border-bottom: 1px solid #000000;
        padding: 2px 5px;
    }

    .e11,
    .e12,
    .e13,
    .e14,
    .e15,
    .e16.e17,
    .e18,
    .e19,
    .e110,
    .e111,
    .e112,
    .e113,
    .e114,
    .e115,
    .e116,
    .e117,
    .e118,
    .e119 {
        color: red;
    }
</style>
@endpush
@section('automation-content')
<main class="loop js-loop">
    <section class="one" id="one">
        <h4 class="section-heading mb-5">Section 1</h4>
        <div class="row" id="section1">
            <div class="col-md-12">
                <div id="products" bis_skin_checked="1">
                    <div class="ui-widget-content" style="min-height: 0%;" bis_skin_checked="1">
                        <ul>
                            <li class="non-draggable4 ui-draggable" data-id="1" id="credit">
                                <a class="button button-orange" style="color:#FFFFFF;"> Enum </a>
                            </li>
                            <li class="block11 ui-draggable" data-id="2" id="fourth">
                                <a class="button button-orange" style="color:#FFFFFF;"> List </a>
                            </li>
                            <li class="non-draggable4 ui-draggable" data-id="3" id="credit0">
                                <a class="button button-orange" style="color:#FFFFFF;"> Int </a>
                            </li>
                            <li class="block12 ui-draggable" data-id="4" id="fifth">
                                <a class="button button-orange" style="color:#FFFFFF;"> Actions </a>
                            </li>
                            <li class="block14 ui-draggable" data-id="5" id="credit2">
                                <a class="button button-orange" style="color:#FFFFFF;"> String </a>
                            </li>
                            <li class="block15 ui-draggable" data-id="6" id="credit1">
                                <a class="button button-orange" style="color:#FFFFFF;"> Webdriver </a>
                            </li>
                            <li class="non-draggable4 ui-draggable" data-id="7" id="credit3">
                                <a class="button button-orange" style="color:#FFFFFF;"> Keys </a>
                            </li>
                            <li class="non-draggable4 ui-draggable" data-id="8" id="credit4">
                                <a class="button button-orange" style="color:#FFFFFF;"> Testing </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <table width="100%" class="mt-5 pt-5">
                    <tbody>
                        <tr>
                            <td>
                                <h3 align="center" class="ui-widget-header">
                                    Java</h3>
                                <table border="1" id="table4" width="100%">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="box" id="java" bis_skin_checked="1">
                                                    <h3 align="center" class="ui-widget-header">
                                                        Class</h3>
                                                    <div class="ui-widget-content" bis_skin_checked="1">
                                                        <ol align="center" class="field14 ui-droppable ui-sortable"
                                                            id="java-class" style="list-style:none">
                                                            <li class="placeholder">
                                                                &nbsp;<br>
                                                                &nbsp;</li>
                                                        </ol>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="box" id="selenium" bis_skin_checked="1">
                                                    <h3 align="center" class="ui-widget-header">
                                                        Interface</h3>
                                                    <div class="ui-widget-content" bis_skin_checked="1">
                                                        <ol align="center" class="field11 ui-droppable ui-sortable"
                                                            id="java-interface" style="list-style:none">
                                                            <li class="placeholder">
                                                                &nbsp;<br>
                                                                &nbsp;</li>
                                                        </ol>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td>
                                <h3 align="center" class="ui-widget-header">
                                    Selenium</h3>
                                <table border="1" id="table4" width="100%">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="box" id="shoppingCart3" bis_skin_checked="1">
                                                    <h3 align="center" class="ui-widget-header">
                                                        Class</h3>
                                                    <div class="ui-widget-content" bis_skin_checked="1">
                                                        <ol align="center" class="field12 ui-droppable ui-sortable"
                                                            id="selenium-class" style="list-style:none">
                                                            <li class="placeholder">
                                                                &nbsp;<br>
                                                                &nbsp;</li>
                                                        </ol>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="box" id="selenium" bis_skin_checked="1">
                                                    <h3 align="center" class="ui-widget-header">
                                                        Interface</h3>
                                                    <div class="ui-widget-content" bis_skin_checked="1">
                                                        <ol align="center" class="field15 ui-droppable ui-sortable"
                                                            id="selenium-interface" style="list-style:none">
                                                            <li class="placeholder">
                                                                &nbsp;<br>
                                                                &nbsp;</li>
                                                        </ol>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="table4_result mt-5" id="result" style="display: none;"><a class="button button-green"></a>
                </div>
                <div class="e14" id="e1" style="display: none;">Please select another block</div>
            </div>
        </div>
    </section>
    <section class="two" id="two">
        <h4 class="section-heading">Section 2</h4>
        <div class="button-alignment col-md-12">
            <div class="col-md-4">
                <button class="btn bld mb-4" type="button" onclick="current_date();">Click to see current date</button>
                <h6 id="display" class="button-click"></h6>
            </div>
            <div class="col-md-4">
                <button class="btn bld mb-4" type="button" ondblclick="future_date();">Double click to see future date</button>
                <h6 id="display1" class="button-click"></h6>
            </div>
            <div class="col-md-4">
                <button class="btn bld mb-4" type="button" oncontextmenu="myFunction()">Right click to enter past date</button>
                <h6 id="demo" class="button-click"></h6>
            </div>
        </div>
    </section>
    <section class="three" id="three">
        <h4 class="sec1">Section 3</h4>
        <div class="col-md-12 mt-5">
            <center>
                <div style="background: #6cc36c;" class="position-relative">
                    <h5 class="perform-click" id="display-watch">Perform js click to display clock</h5>
                </div>
                <img src="{{ asset('front/images/cursor-img.png') }}" alt="img of cursor poing" class="cursorimgd">
                <img src="{{ asset('front/images/stopwatch.jpg')}}" alt="" width="200" id="image" class="d-none">
                <div id="show-contact">
                    <h5 class="perform-click" style="background: #6cc36c;">Perform js click to enable contact field</h5>
                   <img src="{{ asset('front/images/cursor-img.png') }}" alt="img of cursor poing" class="cursorimgd">
                </div><br>
                <form action="" style="text-align:left;">
                    <label class="contugormt my-2" for=""><b>Contact Number</b></label>
                    <div class="input-group  mb-2" style="width: 100%;">
                        <input type="text" class="form-control contugorm" id="inlineFormInputGroup"
                            placeholder="Contact Number" maxlength="10" disabled>
                    </div>
                </form>
            </center>
        </div>
    </section>
</main>
@endsection
@push('js')
<script>
    (function(jQuery) {  
        jQuery(document).ready(function(){
            
        jQuery("#products li").draggable({
            appendTo: "body",
            helper: "clone"
        
        });
        attachPostToBank(1);
        attachPostToBank(2);
        attachPostToBank(3);
        attachPostToBank(4);
        attachPostToBank(5);
        attachPostToBank(6);
        attachPostToBank(7);
        attachPostToBank(8);
        attachPostToBank(9);
        attachPostToBank(10);
        attachPostToBank(11);
        attachPostToBank(12);
        attachPostToBank(13);
        attachPostToBank(14);
        attachPostToBank(15);
        attachPostToBank(16);
        attachPostToBank(17);
        attachPostToBank(18);
        attachPostToBank(19);
        attachPostToBank(20);
        attachPostToBank(21);

        selectanother(1);
        selectanother(2);
        selectanother(3);
        selectanother(4);
        selectanother(5);
        selectanother(6);

        function test(event, ui)
        {
            var self = jQuery(this);
                self.find(".placeholder").remove();
                var productid = ui.draggable.attr("data-id");
                if (self.find("[data-id=" + productid + "]").length) return;
                jQuery("<li></li>", {
                    "text": ui.draggable.text(),
                    "data-id": productid
                }).appendTo(this);
                    //jQuery("#result").hide();
                
                // To remove item from other shopping chart do this
                var cartid = self.closest('.box').attr('id');
                jQuery(".box:not(#"+cartid+") [data-id="+productid+"]").remove();
                var isAllFilled = checkCompletionStatus(getParent(this));
                var s = getParent(this);

            
                if(isAllFilled)
                {
                    var result=getParent(this)+"_result";
                    jQuery("."+result).show();
                    jQuery("."+result+" a").html("Succesfully Matched!");
                }

        }

        function attachPostToBank(i){
            var bankable = "field"+i;
            console.log("#"+bankable);
            jQuery("."+bankable).droppable({
                activeClass: "content-active",
                hoverClass: "content-hover",
                accept : ".block"+i,
                drop: function(event, ui) {
                    test.call(this,event, ui);
                }
            }).sortable({
                items: "li:not(.placeholder)",
                sort: function() {
                    jQuery(this).removeClass("content-active");
                }
            });
        }

        function selectanother(j){
            var sel="non-draggable"+j;
            console.log("#"+sel);
            jQuery("."+sel).draggable({
            revert: true,
            start: function( event, ui ) {
                var s="Please select another block";
                jQuery(".e1"+j).show();
                jQuery(".e1"+j).html(s);
                setTimeout(function(){jQuery(".e1"+j).hide();},1200);
            }
        });
        }

        function getParent(draggedElem)
        {
            var tableId=jQuery(draggedElem).parents("table").attr("id");
            console.log(tableId);
            return tableId;
        }
        function checkCompletionStatus(tableId){
            
            var isAllFilled = true;
            jQuery("#"+tableId+" .box").each(function(){
                    if(jQuery(this).find('ol .placeholder').length > 0)
                    {
                        isAllFilled = false;
                        return
                    }
            });
            return isAllFilled
        }
        });
        })(jQuery);

        function current_date() {
            var current_date = new Date();
            alert(current_date);
            document.getElementById('display').innerHTML = "You pressed ok! Current date is - "+current_date
        }
        function future_date() {
            var date = new Date();
            var future_date = new Date(date. getFullYear(), date. getMonth() + 1, 1)

            if (confirm("Future date is - "+future_date)){
            document.getElementById('display1').innerHTML = "You pressed ok! Future date is - "+future_date
            } else {
            document.getElementById('display1').innerHTML = "Wooh! You Pressed Cancel!"
            }
        }
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
        $("#display-watch").on('click', function () {
            $("#image").removeClass("d-none");
        });
        $("#show-contact").on('click', function () {
            $("#inlineFormInputGroup").removeAttr("disabled");
        });
        // $('.section2 , .section3').hide();

        var doc = window.document,
            context = doc.querySelector('.js-loop'),
            clones = context.querySelectorAll('.is-clone'),
            disableScroll = false,
            scrollHeight = 0,
            scrollPos = 0,
            clonesHeight = 0,
            i = 0;

        function getScrollPos() {
            return (context.pageYOffset || context.scrollTop) - (context.clientTop || 0);
        }

        function setScrollPos(pos) {
            context.scrollTop = pos;
        }

        function getClonesHeight() {
            clonesHeight = 0;

            for (i = 0; i < clones.length; i += 1) {
                clonesHeight = clonesHeight + clones[i].offsetHeight;
            }

            return clonesHeight;
        }

        function reCalc() {
            scrollPos = getScrollPos();
            scrollHeight = context.scrollHeight;
            clonesHeight = getClonesHeight();

            if (scrollPos <= 0) {
                setScrollPos(1); // Scroll 1 pixel to allow upwards scrolling
            }
        }

        function scrollUpdate() {
            if (!disableScroll) {
                scrollPos = getScrollPos();

                if (clonesHeight + scrollPos >= scrollHeight) {
                    // Scroll to the top when you’ve reached the bottom
                    setScrollPos(1); // Scroll down 1 pixel to allow upwards scrolling
                    disableScroll = true;
                } else if (scrollPos <= 0) {
                    // Scroll to the bottom when you reach the top
                    // setScrollPos(scrollHeight - clonesHeight);
                    disableScroll = true;
                }
            }

            if (disableScroll) {
                // Disable scroll-jumping for a short time to avoid flickering
                window.setTimeout(function () {
                    disableScroll = false;
                }, 40);
            }
        }

        function init() {
            reCalc();

            context.addEventListener('scroll', function () {
                window.requestAnimationFrame(scrollUpdate);
            }, false);

            window.addEventListener('resize', function () {
                window.requestAnimationFrame(reCalc);
            }, false);
        }

        if (document.readyState !== 'loading') {
            init()
        } else {
            doc.addEventListener('DOMContentLoaded', init, false)
        }

        // $(window).scroll(function () {
        //     console.log(window.innerHeight + window.scrollY);

        //     if (window.innerHeight + window.scrollY > 640 && window.innerHeight + window.scrollY < 790) {
        //     console.log('2')
        //     $('.section1').hide(1000)
        //     $('.section2').show(1000)
        //     }else if (window.innerHeight + window.scrollY > 790) {
        //     console.log('3')
        //     $('.section2').hide(1000)
        //     $('.section3').show(1000)
        //     }else if(window.innerHeight + window.scrollY <= 640)
        //     {
        //     console.log('1')
        //     $('.section1').show(1000)
        //     $('.section2').show(1000)
        //     $('.section3').hide(1000)
        //     }
        // });
</script>
@endpush