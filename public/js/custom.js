// Swith Location
function switchText() {
    var obj1 = document.getElementById('left').value;
    var obj2 = document.getElementById('right').value;

    var temp = obj1;
    obj1 = obj2;
    obj2 = temp;

    // Save the swapped values to the input element.
    document.getElementById('left').value = obj1;
    document.getElementById('right').value = obj2;
}

// Add/Less Passanger Qty
// $('.add').click(function () {		
//     var th = $(this).closest('.wrap').find('.count');    	
//     // th.val(+th.val() + 1);
//     if (th.val() < 9) th.val(+th.val() + 1);

//     if(th.val() > 8){
//         alert ("Save on bookings with more than 9 travellers");	
//     }
    
// });
// $('.sub').click(function () {
//     var th = $(this).closest('.wrap').find('.count');    	
//     if (th.val() > 1) th.val(+th.val() - 1);
// });

// $('.add, .sub').click(function () {
//         var target = $(this).data('target');
//         var input = $('#' + target + 'Count');

//         if ($(this).hasClass('add')) {
//             if (input.val() < 9){
//                 $('#adultId').val(+input.val() + 1);
//             }
//             //  input.val();
//             if (input.val() > 8) alert("Save on bookings with more than 9 travelers");
//         } else {
//             if (input.val() > 1) input.val(+input.val() - 1);
//         }
//     });
// $('.add').click(function () {		
//     var th = $(this).closest('.wrap').find('.count');
    
//     if (th.val() < 9) {
//         th.val(+th.val() + 1);
//         // Call a function to update other parts of your UI or perform additional logic
//         updateCount(th);
//     } else {
//         alert("Save on bookings with more than 9 travelers");
//     }
// });

// $('.sub').click(function () {
//     var th = $(this).closest('.wrap').find('.count');

//     if (th.val() > 1) {
//         th.val(+th.val() - 1);
//         // Call a function to update other parts of your UI or perform additional logic
//         updateCount(th);
//     }
// });

// function updateCount(element) {
//     // Add any additional logic you need when the count is updated
//     $('#adultId').val(element);
//     console.log("Count updated:", element.val());
//     // For example, you can update other parts of your UI based on the count
// }

$(".add").on("click", function () {
    var inputField = $(this).siblings(".count");
    var count = parseInt(inputField.val());
    inputField.val(count + 1);
});

$(".sub").on("click", function () {
    var inputField = $(this).siblings(".count");
    var count = parseInt(inputField.val());
    if (count > 1) {
        inputField.val(count - 1);
    }
});

// Add and remove active class (in travel class selection)
$(function(){
    var $h3s = $('.treavelClass button').click(function(){
        $h3s.removeClass('active');
        $(this).addClass('active');
    });
});

// Add and remove active class (in special fare selection)
$(function(){
    var $h3s = $('.specialFare button').click(function(){
        $h3s.removeClass('active');
        $(this).addClass('active');
    });
});

// Hide and Show
function showInfo(idName){
    $("#"+idName).show();
}

function hideInfo(idName){
    $("#"+idName).hide();
}

// Trip selection buttons
$(document).ready(function () {
    $('.tripBtn').on('click', function () {
        // var buttonClass = $(this).attr('class');
        // var $active = $('.tripBtn').click(function(){
        //     $active.removeClass('active');
        //     $(this).addClass('active');
        // });

          
          $('.tripBtn').removeClass('active');
        $(this).addClass('active');
          var buttonClass = $(this).attr('class');

        if (buttonClass.includes('oneWay')) {
            $('#returnDate').attr('readonly');
            $('#return').addClass('blockReturn');

            console.log("Button oneWay!");
        }else if (buttonClass.includes('roundTrip')) {
            $('#returnDate').removeAttr('readonly');
            $('.blockReturn').removeClass('blockReturn');

            console.log("Button roundTrip!");
        }else if (buttonClass.includes('multiTrip')) {
            $('#readonly').attr('readonly');
            $('#return').addClass('blockReturn');

            console.log("Button multiTrip!");
        }
    });
});


$(function() {
    $("#departureDate").datepicker({
        dateFormat: 'dd-mm-yy', // Customize the date format
        minDate: 0, // Disable past dates
        onSelect: function(selectedDate) {
            $("#returnDate").datepicker("option", "minDate", selectedDate);
        }
    });

    $("#returnDate").datepicker({
        dateFormat: 'dd-mm-yy', // Customize the date format
        minDate: 0, // Disable past dates
    });


    // $(".DatedepartureClass").datepicker({
       
    //     dateFormat: 'd-m-yy', // Customize the date format
    //     minDate: 0, // Disable past dates
    //     // onSelect: function(selectedDate) {
    //     //     $("#returnDate").datepicker("option", "minDate", selectedDate);
    //     // }
       
    // });
    
});

function redirectionURL(url){
    window.location.href = url;
}











// Active on radio

    $(".travelClass").on("click", function () {
        $(".travelClass").removeClass("active");
        $(this).addClass("active");
    });


  $(".specialFare").on("change", function () {
    // Toggle the active class based on checkbox state
    $(this).toggleClass("active", this.checked);
  });

  $(document).on('focus', '.DatedepartureClass', function() {
        $(this).datepicker({
            dateFormat: 'dd-mm-yy', 
            minDate: 0, 
        });
  });

  $(document).on('focus', '.dobClass', function() {
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',  
            maxDate: 0
        });
    });

$(document).on('focus', '.validClassDate', function() {
    $(this).datepicker({
        dateFormat: 'dd-mm-yy', 
        // minDate: 0, 
        minDate: 1,
       
    });
});

/* Multiselectbox */
$(document).ready(function() {
    $("#multi-select").select2(); 
});

$(document).ready(function() {
    $("#image-upload").change(function() {
        readURL(this);
    });
});
  