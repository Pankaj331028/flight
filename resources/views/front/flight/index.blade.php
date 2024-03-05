@extends('front.flight.layouts.master')
@section('template_title','Flight Booking')
@section('content')
<section class="flightBooking">
	<div id="page-wrapper">
		<!-- Flight Searching -->
		<section class="wrapper1 render-QG quickGrab bgf7 topBanner">
			<div class="black_overlay">
				<div class="container mb-5">
					<div class="cPadding">
						<?php $currentDate = date("d-m-Y");?>
						<div class="flightOuterBox bg-light shadow p-5">


							<form id="myform" action="{{route('search-flight')}}" class="flight-searching-form" method="get">
							<div class="pb-4">
								<button type="button" class="oneWay tripBtn active mr-3  me-1" id="one-way-trip"> One-Way Trip</button>
								<button type="button" class="roundTrip tripBtn  mr-3" id="removeReadonly">Round Trip</button>
								<button type="button" class="multiTrip tripBtn" id="multi-Trip">Multi Trip</button>
								<input type="hidden" id="trip_type" name="trip_type" value="one_way_trip">
							</div>
							<section class="" id="oneRoundTrip">

									<div class="row">
										<div class="col-lg-3 px-2 position-relative">
											<div class="form-group fromBox pl-4 py-2">
												<label class="text-secondary mb-1">
													<i class="fa-solid fa-plane-departure" style="color: #6c757d;"></i> &nbsp;
													Flying from
												</label>
												<input type="text" class="form-control font700 left" id="left" name="flyingfrom" value="">
											</div>
											<div class=" Flyingleft text-danger"></div>
											<button type="button" class="swapLocation position-absolute" id="swaploction" onClick="switchText()"></button>
										</div>
										<div class="form-group col-lg-3 px-2">
											<div class="toBox pl-4 py-2">
												<label class="text-secondary mb-1">
													<i class="fa-solid fa-plane-arrival" style="color: #6c757d;"></i> &nbsp;
													Flying to
												</label>
												<input type="text" class="form-control font700" id="right" name="flyingTo" value="">
											</div>
											<!-- <div class="Flyingright text-danger"></div> -->
										</div>
										<div class="form-group col-lg px-2">
											<div class="px-3 py-2 depart">
												<label class="text-secondary mb-1">
													<i class="fa-solid fa-calendar-days" style="color: #6c757d;"></i> &nbsp;
													Depart
												</label>
												<input type="text" class="form-control font700 w-100" id="departureDate" name="departureDates"  value="<?=$currentDate;?>">
											</div>
										</div>
										<div class="form-group col-lg px-2 dNone" id="returnId">
											<div class="px-3 py-2 depart blockReturn" id="return">
												<label class="text-secondary mb-1">
													<i class="fa-solid fa-calendar-days" style="color: #6c757d;"></i> &nbsp;
													Return
												</label>
												<input type="text" class="form-control font700" id="returnDate" name="returnDates" readonly value="<?=$currentDate;?>">
											</div>
										</div>
										<div class="col-lg px-2 position-relative form-group addPassangerClass" id="">
											<div class="px-3 py-2 depart" onClick="showInfo('addPassanger')">
												<label class="text-secondary mb-1">
													<i class="fa-solid fa-users" style="color: #6c757d;"></i> &nbsp;
													Travelers
												</label>
												<!-- <input type="text" class="font700" readonly id="passenger" name="passenger" value="1 Passenger(s)"> -->
												<input type="text" class="font700" readonly id="passenger" name="passenger" value="1">
											</div>

											<div class="position-absolute addPassanger bg-light shadow OneId" id="addPassanger">
											<!-- ... Your existing HTML ... -->

											<div class="p-3 border-bottom">
												<div class="row mx-auto mb-3">
													<div class="float-start w-50 text-secondary font14 addPanel">ADULTS (12y +)</div>
													<div class="float-end w-50 text-right">
														<select name="adultsCount" class="w-25" id="adultsCount">
															@for($i=1; $i<=10; $i++)
															<option value="{{$i}}">{{$i}}</option>
															@endfor
														</select>
													</div>
												</div>
												<div class="row mx-auto mb-3">
													<div class="float-start w-50 text-secondary font14 addPanel">CHILDREN (2y - 12y )</div>
													<div class="float-end w-50 text-right">
														<select name="childrenCount" class="w-25" id="childrenCount">
															@for($i=0; $i<=10; $i++)
															<option value="{{$i}}">{{$i}}</option>
															@endfor
														</select>
													</div>
												</div>
												<div class="row mx-auto">
													<div class="float-start w-50 text-secondary font14 addPanel">INFANTS (below 2y)</div>
													<div class="float-end w-50 text-right">
														<select name="infantsCount" class="w-25" id="infantsCount">
															@for($i=0; $i<=10; $i++)
															<option value="{{$i}}">{{$i}}</option>
															@endfor
														</select>
													</div>
												</div>
											</div>

											<!-- ... Your existing JavaScript ... -->

												<div class="p-3 border-bottom">
													<div class="row mx-auto">
														<div class="float-start w-100 text-secondary font14 pb-3">CHOOSE TRAVEL CLASS</div>
														<div class="float-end w-100">
															<div class="treavelClass row mx-auto text-center">
																<label class="travelClass economy font14 active mb-0  col-sm-4 col-xs-4 mr-0">
																	<input class="form-check-input dNone" type="radio" name="travelclass" id="economy" checked value="economy">
																	Economy
																</label>
																<label class="travelClass premium font14 mb-0  col-sm-4 col-xs-4 mr-0">
																	<input class="form-check-input dNone" type="radio" name="travelclass" id="premium" value="premium">
																	Premium
																</label>
																<label class="travelClass business font14 mb-0  col-sm-4 col-xs-4 mr-0">
																	<input class="form-check-input dNone" type="radio" name="travelclass" id="business" value="business">
																	Business
																</label>
															</div>
														</div>
													</div>
												</div>
												<div class="p-3 border-bottom">
													<div class="row mx-auto">
														<div class="float-start w-100 text-secondary font14 pb-3">SPECIAL FARE</div>
														<div class="float-end w-100">
															<label class="specialFare">
																<label class="specialFare seniorCitizen font14  mb-0">
																	<input class="form-check-input dNone" type="checkbox" name="specialFare[]" id="seniorCitizen"  value="seniorCitizen">
																	Senior Citizen
																</label>
																<label class="specialFare economy font14 mb-0">
																	<input class="form-check-input dNone" type="checkbox" name="specialFare[]" id="armedForces"  value="armedForces">
																	Armed Forces
																</label>
																<label class="specialFare economy font14 mb-0">
																	<input class="form-check-input dNone" type="checkbox" name="specialFare[]" id="studentFare"  value="studentFare">
																	Student Fare
																</label>
															</label>
														</div>
													</div>
												</div>
												<div class="p-3">
													<button type="button" class="greenTransparentBtn active w-100" id="applyTravelers">Apply</button>
												</div>
											</div>
										</div>
									</div>
									<div class="d-none" id="multiTripSection">
										<div class="row mb-3" >
											<div class="col-lg-3 px-2 position-relative">
												<div class="form-group fromBox pl-4 py-2 ">
													<label class="text-secondary mb-1">
														<i class="fa-solid fa-plane-departure" style="color: #6c757d;"></i> &nbsp;
														Drop City
													</label>
													<br>
													<input type="text" class=" form-control font700 left dropCity" name="dropCity[0]" value="" id="dropCity_0">
												</div>
											</div>

											<div class="col-lg-3 px-2">
												<div class="form-group px-3 py-2 depart">
													<label class="text-secondary mb-1">
														<i class="fa-solid fa-calendar-days" style="color: #6c757d;"></i> &nbsp;
														Depart
													</label>
													<br>
													<input type="text" class="form-control font700 DatedepartureClass w-100" id="dateDeparture_0" name="dateDeparture[0]"  value="">
												</div>
											</div>

										</div>
										<div class="addMultipleRow" >

										</div>
										<div class="col-lg-6 px-2 position-relative pt-2">
											<div class="px-3 py-3 depart">
												<button type="button" class="greenTransparentBtn mr-auto w-100" id='add_row'>Add Drop City</button>
											</div>
										</div>
									</div>

							</section>


							<div class="pt-4 text-right">
								<button type="submit" class="mr-auto greenTransparentBtn" id="search_flight">Search Flight</button>
								<button type="reset" class="mr-auto cancelBtn" id= "Cancel">Cancel</button>
							</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
</section>
<script>

    $(document).ready(function () {

		$("#one-way-trip").on('click', function (e) {

			$('#returnId').addClass('dNone');
			// $('#oneRoundTrip').removeClass('dNone');
			$('#multiTripSection').addClass('d-none');
			$('#returnDate').val('');
			$('#trip_type').val('one_way_trip');
			// $('.rmv').closest(".removeClassmultiple").remove();

			function processInputValues(inputName) {
                var titles = $('input[name^=' + inputName + ']').map(function (idx, elem) {
                    var value = $(elem).val();
                    var combinedValue = value + '(' + $(elem).attr("name") + ')';

                    return {
                        value: value,
                        combinedValue: combinedValue
                    };
                }).get();

                var valuesArray = titles.map(function (item) {
                    return item.value;
                });

                var combinedValuesArray = titles.map(function (item) {
                    return item.combinedValue;
                });

                for (var i = 1; i <= valuesArray.length - 1; i++) {
                    if (valuesArray[i] === '') {
                        var originalString = combinedValuesArray[i].split("[");
                        var searchString = originalString[1].replace("(" + inputName, "");
                        var replacementString = searchString.split("])");
                        var modifiedString = replacementString[0].replace(",", "");
                        $('.removeClassmultiple_' + modifiedString).remove();
                    }
                }
            }
            processInputValues('dateDeparture');

            processInputValues('dropCity');

        });
		$("#removeReadonly").on('click', function (e) {

			// $("#ReturnId").removeClass("intro");
			$('#returnId').removeClass('dNone');
			// $('#oneRoundTrip').removeClass('dNone');
			$('#multiTripSection').addClass('d-none');
			var tripType = $('#trip_type').val('roundTrip');
			// var abc = $('.DatedepartureClass').val();
			// alert(abc);

			// var titles = $('input[name^=dateDeparture]').map(function(idx, elem) {
			// 	// alert()
			// 	// var name = $(elem).attr("name");
			// 	// console.log($(elem).attr("name"));
			// 	return $(elem).val() +'('+$(elem).attr("name")+')';


			// }).get();

			function processInputValues(inputName) {
                var titles = $('input[name^=' + inputName + ']').map(function (idx, elem) {
                    var value = $(elem).val();
                    var combinedValue = value + '(' + $(elem).attr("name") + ')';

                    return {
                        value: value,
                        combinedValue: combinedValue
                    };
                }).get();

                var valuesArray = titles.map(function (item) {
                    return item.value;
                });

                var combinedValuesArray = titles.map(function (item) {
                    return item.combinedValue;
                });

                for (var i = 1; i <= valuesArray.length - 1; i++) {
                    if (valuesArray[i] === '') {
                        var originalString = combinedValuesArray[i].split("[");
                        var searchString = originalString[1].replace("(" + inputName, "");
                        var replacementString = searchString.split("])");
                        var modifiedString = replacementString[0].replace(",", "");
                        $('.removeClassmultiple_' + modifiedString).remove();
                    }
                }
            }
            processInputValues('dateDeparture');

            processInputValues('dropCity');

        });

		$("#multi-Trip").on('click', function (e) {
			$('#multiTripSection').removeClass('d-none');
			$('#returnId').addClass('dNone');
			$('#trip_type').val('multiTrip');
			$('#returnDate').val('');
        });
		$(function() {

            var availableAirport = {!! json_encode($airportCode) !!};

            // $( ".left" ).autocomplete({
            //    minLength:3,
            //    delay:500,
            //    source: availableTutorials
            // });
			// $(document).on("input", ".left", function() {
			// 	$(this).autocomplete({
			// 		minLength: 1,
			// 		delay: 500,
			// 		source: availableAirport
			// 	});
			// });

			$(document).on("input", ".left", function() {
				$(this).autocomplete({
					minLength: 1,
					delay: 500,
					source: availableAirport,
					select: function(event, ui) {
						console.log("Selected value: " + ui.item.value);
					}
				});


				$(this).rules("add", {
					flightValidName: true,
					uniqueLeftValue: true

				});
			});

			$.validator.addMethod("flightValidName", function(value, element) {

				return availableAirport.includes(value);
			}, "Please Enter a valid Location");

			// $.validator.addMethod("uniqueLeftValue", function(value, element) {
			// var isUnique = true;
			// 	if ($(this).val() === value) {
			// 		isUnique = false;
			// 		return false;
			// 	}
			// return isUnique;
			// }, "This value must be unique among all left fields.");

			

            $( "#right" ).autocomplete({
               minLength:3,
               delay:500,
               source: availableAirport,
			   select: function(event, ui) {
						console.log("Selected value: " + ui.item.value);
					}
            });

    	});
			var i = 1;
		$("#add_row").click(function(){
			$("#myform").delegate("#add_row", "click", function () {
                function checkValidity(className) {
                    var flags = true;

                    $("." + className).each(function () {
                        if (!$(this).valid()) {
                            flags = false;
                            return false;
                        }
                    });
                  
                }
                checkValidity("dropCity");
                checkValidity("DatedepartureClass");
            });
			
			if (validator.form()) {
			
				var row = '<div class="row mb-3  removeClassmultiple removeClassmultiple_'+i+'" >' +
				'<div class="col-lg-3 px-2 position-relative">' +
				'<div class="form-group fromBox pl-4 py-2">' +
				'<label class="text-secondary mb-1">' +
				'<i class="fa-solid fa-plane-departure" style="color: #6c757d;"></i> &nbsp;' +
				'Drop City' +
				'</label>' +
				'<br>' +
				'<input type="text" class="form-control font700 left dropCity" name="dropCity['+i+']" value="" id="dropCity_'+i+'">' +
				'</div>' +
				'</div>' +
				'<div class="col-lg-3 px-2">' +
				'<div class=" form-group px-3 py-2 depart">' +
				'<label class="text-secondary mb-1">' +
				'<i class="fa-solid fa-calendar-days" style="color: #6c757d;"></i> &nbsp;' +
				'Depart' +
				'</label>' +
				'<br>' +
				'<input type="text" class=" form-control font700 DatedepartureClass" id="" name="dateDeparture['+i+']"  value="" id="dateDeparture_'+i+'">' +
				'</div>' +
				'</div>' +
				'<div class="col-lg-1 px-2 position-relative text-center rmv cursor removeIconOuter">' +
				'<div class="px-3 py-4 depart">' +
				'<i class="fa fa-trash " aria-hidden="true"></i>'
				// '<button type="button" class="tripBtn mr-auto w-100 rmv">Remove Drop City</button>' +
				'</div>' +
				'</div>' +
				'</div>';

				$(".addMultipleRow").append(row);
				// initializeValidation();
				i++;
			}

		});
		// ['+designfieldcount+']


		$("body").on("click",".rmv",function(){
			$(this).closest(".removeClassmultiple").remove();
		});


        var travelclass = $('input[name="travelclass"]:checked').val();

        $("#applyTravelers").on("click", function () {
			adultsCount = parseInt($('#adultsCount').val()) || 0;
			childrenCount = parseInt($('#childrenCount').val()) || 0;
			infantsCount = parseInt($('#infantsCount').val()) || 0;

			var totalCount = adultsCount + childrenCount + infantsCount;
			$('#passenger').val(totalCount);

			var travelclass = $('input[name="travelclass"]:checked').val();
			var checkedSpecialFares = [];


			$("input[name='specialFare[]']:checked").each(function () {
				checkedSpecialFares.push($(this).attr("value")); //
			});
			var specialFare = checkedSpecialFares.join(", ");

			$("#addPassanger").hide();

		});

        // var flyingFromInput = document.getElementById("left");
        // var flyingToInput = document.getElementById("right");
        // flyingFromInput.addEventListener("input", function () {
        //     var inputValue = flyingFromInput.value.trim();
        //     if (!/^[a-zA-Z]+$/.test(inputValue)) {
        //         $('.Flyingleft').text('Enter valid characters (only alphabets allowed)');
        //         // displayErrorMessage("Enter valid characters (only alphabets allowed)");
        //     }
        // });


        // Adding a custom rule for letters only
        $.validator.addMethod(
            "lettersOnly",
            function (value, element) {

				// return this.optional(element) || /^[a-zA-Z(),]+$/.test(value);
				return this.optional(element) || /^[a-zA-Z,()\- ]+$/.test(value);
            },
            "Enter valid characters (only alphabets allowed)"
        );

		$.validator.addMethod("uniqueLeftValue", function(value, element) {
					var isUnique = true;
					$(".left").not(element).each(function() {
						if ($(this).val() === value) {
							isUnique = false;
							return false;
						}
					});
					return isUnique;
		}, "You have already used this location.");

        $.validator.addMethod(
            "validDateFormat",
            function (value, element) {
                return this.optional(element) || /^\d{1,2}-\d{1,2}-\d{4}$/.test(value);
            },
            "Enter a valid date in the format DD-MM-YYYY"
        );

        var validator = $(".flight-searching-form").validate({
			ignore: '.tab-pane:not(.active) :input',
			errorClass: "invalid-feedback animated fadeInDown",
			errorElement: "div",
			errorPlacement: function(e, a) {
				if (jQuery(a).closest(".form-group").find('.invalid-feedback').length > 0)
					jQuery(a).closest(".form-group").find('.invalid-feedback').remove();

				jQuery(a).closest(".form-group").append(e)

				var name = jQuery(a).attr('name');
				jQuery(e).attr('id', 'invalid-' + name);
			},
			highlight: function(e) {
				jQuery(e).closest(".form-group").removeClass("is-invalid").addClass("is-invalid")
			},
			success: function(e) {
				jQuery(e).closest(".form-group").removeClass("is-invalid"), jQuery(e).remove()
			},
			rules: {
				flyingfrom:{
					required:!0,
					lettersOnly: true,

				},
				flyingTo:{
					required:!0,
					lettersOnly: true,
					flightValidName:true,
					uniqueLeftValue:true,
				},
				departureDates:{
					required:!0,
					validDateFormat: true,
				},
				// dropCity:{
				// 	required:!0,
				//     lettersOnly: true,
				// },
				// returnDates: {
				// 	required: function(element) {
				// 		return $("#removeReadonly").hasClass("active");
				// 	},
				// 	validDateFormat: true,
				// },


			},
			messages: {
				flyingfrom:{
					required:"Please Enter the City",
					lettersOnly: "Enter valid characters (only alphabets allowed)",
				},
				flyingTo:{
					required:"Please Enter the City",
					lettersOnly: "Enter valid characters (only alphabets allowed)",
					flightValidName:"Please Enter a valid Location",
					uniqueLeftValue:"You have already used this location.",
				},
				departureDates: {
						required: "Please enter a date",
						dateISO: "Enter a valid date in the format DD-MM-YYYY",
				},
				returnDates: {
						required: "Please enter a date",
						dateISO: "Enter a valid date in the format DD-MM-YYYY",
				},
				// dropCity:{
				//     required:"Please Enter the City",
				//     lettersOnly: "Enter valid characters (only alphabets allowed)",
				// },

			}
		})


		$("#removeReadonly").click(function() {
			validator.settings.rules.returnDates = {
				required:!0,
				validDateFormat: true,
			};
			validator.settings.messages.returnDates = {
				required: "Please enter a return date",
				dateISO: "Enter a valid return date in the format DD-MM-YYYY",
			};

			$("#myform").delegate("#search_flight", "click", function () {
				$(".dropCity, .DatedepartureClass").each(function(){
					$(this).rules("remove");
				});
			});

            validator.resetForm();
    	});

		$("#multi-Trip").click(function() {
			// validator.resetForm();

            $(".dropCity").each(function(){
					$(this).rules("add", {
						required:true,
						lettersOnly: true,
						flightValidName:true,
						uniqueLeftValue:true,

						messages: {
						required: "Please Enter the City",
						lettersOnly: "Enter valid characters (only alphabets allowed)",
						flightValidName:"Please Enter a valid Location",
						uniqueLeftValue:"You have already used this location.",
					}
					});
			});

            $(".DatedepartureClass").each(function(){
                $(this).rules("add", {
                    required:true,
                    validDateFormat: true,
                    messages: {
                    required: "Please enter a date",
                    validDateFormat: "Enter a valid date in the format DD-MM-YYYY",

                    }
                });
            });
            $("#myform").delegate("#add_row", "click", function () {
			    $(".dropCity").each(function(){
					$(this).rules("add", {
						required:true,
						lettersOnly: true,
						flightValidName:true,
						uniqueLeftValue:true,
						messages: {
						required: "Please Enter the City",
						lettersOnly: "Enter valid characters (only alphabets allowed)",
						flightValidName:"Please Enter a valid Location",
						uniqueLeftValue:"You have already used this location.",
					}
					});
				});
                $(".DatedepartureClass").each(function(){
					$(this).rules("add", {
						required:true,
						validDateFormat: true,
						messages: {
						required: "Please enter a date",
						validDateFormat: "Enter a valid date in the format DD-MM-YYYY",

						}
					});
				});
            });
            $("#myform").delegate("#search_flight", "click", function () {
				$(".dropCity").each(function(){
					$(this).rules("add", {
						required:true,
						lettersOnly: true,
						flightValidName:true,
						uniqueLeftValue:true,
						messages: {
						required: "Please Enter the City",
						lettersOnly: "Enter valid characters (only alphabets allowed)",
						flightValidName:"Please Enter a valid Location",
						uniqueLeftValue:"You have already used this location.",
					}
					});
				});
				$(".DatedepartureClass").each(function(){
					$(this).rules("add", {
						required:true,
						validDateFormat: true,
						messages: {
						required: "Please enter a date",
						validDateFormat: "Enter a valid date in the format DD-MM-YYYY",

						}
					});
				});
			});


		});

		

		$("#one-way-trip").on('click', function (e) {

			validator.showErrors({
                "returnDates": "",
            });
			delete validator.settings.rules.returnDates;
            delete validator.settings.messages.returnDates;
			$("#myform").delegate("#search_flight", "click", function () {
				$(".dropCity, .DatedepartureClass").each(function(){
					$(this).rules("remove");
				});
			});
			validator.resetForm();
        });

		$("#add_row").click(function () {

			// $('.addMultipleRow input[name^="dropCity"]').rules('add', {
			// 	required: true,
			// 	lettersOnly: true,
			// 	messages: {
			// 		required: "Please Enter the City",
			// 		lettersOnly: "Enter valid characters (only alphabets allowed)",
			// 	},
			// });

			// $('.addMultipleRow input[name^="dateDeparture"]').rules('add', {
			// 	required: true,
			// 	validDateFormat: true,
			// 	messages: {
			// 		required: "Please enter a date",
			// 		dateISO: "Enter a valid date in the format DD-MM-YYYY",
			// 	},
			// });


		// validator = $(".flight-searching-form").validate(validator.settings);

		// // Trigger revalidation to update error messages immediately (optional)
			// $(".flight-searching-form").valid();
		});

        $(document).on('click', function (e) {
            if (!$(e.target).closest(".addPassangerClass").length) {
                $(".OneId").hide();
            }
        });

        $(".addPassangerClass").on('click', function (e) {
            e.stopPropagation();
        });
    });
</script>

@endsection