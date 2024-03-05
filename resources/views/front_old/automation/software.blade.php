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
        height: 200px;
    }

    #section2,#section3 {
        margin: 0;
        width: 100%;
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

    .left_side {
        width: 220px;
        border: 2px solid #039;
        text-align: center !important;
        float: left;
        margin: 0px auto 0px 7px;

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

    #logo {
        border: 0px ridge #33ff00;
        float: right;
        width: 158px;
        padding: 0px 0px 0px 0px;
        text-align: center;
    }

    .button-click {
        height: 20px;
    }

    @media(max-width:767.98px) {
        .button-alignment {
            height: 100%;
            display: block;
        }

        button.btn.bld {
            position: relative;
        }

        button.btn.bld {
            width: 233px;
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
            right: -110px;
            width: 44px;
            bottom: 38px;
        }

        button.btn.bld {
            font-size: 13px !important;
            padding: 0px;
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
    table {
        width: 100%;
    }

    .hidden {
        visibility: hidden;
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

    input[type=text] {
        background: transparent;
        border: none;
        border-bottom: 1px solid #000000;
        padding: 2px 5px;
    }

    input[type=text] {
        background: transparent;
        border: none;
        border-bottom: 1px solid #000000;
        padding: 2px 5px;
    }
</style>
@endpush
@section('automation-content')
<div class="container">
    <section class="two" id="two">
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
        <div class="col-md-12 mt-2">
            <center>
                <div class="position-relative">
                    <h5 class="perform-click" style="background: #6cc36c;" id="display-watch">Perform js click to display clock</h5>
                    <img src="{{ asset('front/images/cursor-img.png') }}" alt="img of cursor poing" class="cursorimgd">
                    <img src="{{ asset('front/images/stopwatch.jpg')}}" alt="" width="200" id="image" class="hidden" style="margin-top: -60px;">
                </div>
                
                <div style="background: #6cc36c;" id="show-contact">
                    <h5 class="perform-click">Perform click to enable contact field</h5>
                </div><br>
                <div style="text-align:left;">
                    <label class="contugormt my-2" for=""><b>Contact Number</b></label>
                    <div class="input-group  mb-2" style="width: 100%;">
                        <input type="text" class="form-control contugorm" id="inlineFormInputGroup"
                            placeholder="Contact Number" maxlength="10" disabled>
                    </div>
                </div>
            </center>
        </div>
    </section>
</div>
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
                    jQuery("."+result+" a").html("Perfect!");
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
            $("#image").removeClass("hidden");
        });
        $("#show-contact").on('click', function () {
            $("#inlineFormInputGroup").removeAttr("disabled");
        });

</script>
@endpush