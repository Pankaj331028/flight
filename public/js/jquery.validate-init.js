var fixedLength = 0;
var APP_NAME = location.protocol + '//' + location.host
// var APP_NAME = '/'; 
jQuery.validator.addMethod("filesize_max", function(value, element, param) {
    var isOptional = this.optional(element),
        file;

    if (isOptional) {
        return isOptional;
    }

    if ($(element).attr("type") === "file") {

        if (element.files && element.files.length) {

            file = element.files[0];
            //console.log(file.size);      
            return (file.size && file.size <= 52428800);
        }
    }
    return false;
}, "File size is too large.");

$.validator.addMethod('dimension', function(value, element, param) {
    if (element.files.length == 0) {
        return true;
    }
    var file = element.files[0];
    var width = height = 0;
    var tmpImg = new Image();
    var result = '';
    tmpImg.src = window.URL.createObjectURL(file);
    tmpImg.onload = function() {
        width = tmpImg.naturalWidth,
            height = tmpImg.naturalHeight;

        result = (width <= param[0] && height <= param[1]);

        return result;
    }
}, function() {
    return 'Please upload an image with maximum 100 x 100 pixels dimension'
});

jQuery.validator.addMethod("fixedDigits", function(value, element, param) {
    var isOptional = this.optional(element);
    fixedLength = param;

    if (isOptional) {
        return isOptional;
    }

    return ($(element).val().length <= param);
}, function() {
    return "Value cannot exceed " + fixedLength + " characters."
});

jQuery.validator.addMethod("invalidUpi", function(value, element, param) {
    

    return ($.inArray(value, param) < 0);
}, function() {
    return "Invalid UPI ID"
});

jQuery.validator.addMethod("extension", function(value, element, param) {
    param = typeof param === "string" ? param.replace(/,/g, '|') : "png|jpe?g|gif";
    return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
}, "Please select image with a valid extension (.jpg, .jpeg, .png, .gif, .svg)");

jQuery.validator.addMethod("import_extension", function(value, element, param) {
    param = typeof param === "string" ? param.replace(/,/g, '|') : "xls|xlsx";
    return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
}, "Please select file with a valid extension (.xls, .xlsx)");

jQuery.validator.addMethod("docextension", function(value, element, param) {
    param = typeof param === "string" ? param.replace(/,/g, '|') : "png|jpe?g|gif";
    return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
}, "Please select file with a valid extension (.jpg, .jpeg, .png, .doc, .docx, .pdf)");

jQuery.validator.addMethod("decimalPlaces", function(value, element) {
    return this.optional(element) || /^\d+(\.\d{0,2})?$/i.test(value);
}, "Please enter a value with maximum two decimal places.");

jQuery.validator.addMethod("alphanumeric", function(value, element) {
    return this.optional(element) || /^[a-zA-Z0-9]+$/i.test(value);
}, "Please enter alphanumeric value.");

jQuery.validator.addMethod("alphanumericspace", function(value, element) {
    return this.optional(element) || /^[a-zA-Z0-9\s]+$/i.test(value);
}, "Please enter alphanumeric value.");

jQuery.validator.addMethod("exactlength", function(value, element, param) {
    return this.optional(element) || value.length == param;
}, $.validator.format("Please enter exactly {0} characters."));

jQuery.validator.addMethod("lettersonly", function(value, element) {
    return this.optional(element) || /^[a-zA-Z\s]+$/i.test(value);
}, "Name can have alphabets and space only.");

jQuery.validator.addMethod("contact_number", function(value, element) {
    return this.optional(element) || /^\+[0-9]+[0-9\-]+[0-9]+$/i.test(value);
}, "Incorrect contact number format");

jQuery.validator.addMethod("non_whitespace", function(value, element) {
    return this.optional(element) || /^(?!\s*$).+/i.test(value);
}, "Incorrect value");

jQuery.validator.addMethod("check_content", function(value, el, param) {
    var content = $(el).summernote('code');
    content = $(content).text().replace(/\s+/g, '');

    return (content !== "");
}, "Incorrect value");

jQuery.validator.addMethod("correctPassword", function(value, element) {
    return this.optional(element) || /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).{6,}$/i.test(value);
}, "Please fill minimum 6 character Password with uppercase, lowercase, special character and digit");

$.validator.addMethod("greaterThanDate", function(value, element, param) {
    var $otherElement = $(param);
    return new Date('1970-01-01T' + value + 'Z') > new Date('1970-01-01T' + $otherElement.val() + 'Z');
}, "End Time must be greater than start time");


jQuery.validator.addMethod("validate_email", function(value, element) {
    if (/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(value)) {
        return true;
    } else {
        return false;
    }
}, "Please enter a valid Email.");


var form_validation = function() {
    // alert('enter');
    var e = function() {
        var form_validate = jQuery(".form-valide").validate({
            ignore: [".note-editor *", "password"],
            errorClass: "invalid-feedback animated fadeInDown",
            errorElement: "div",
            errorPlacement: function(e, a) {
                if (jQuery(a).closest(".form-group").find('.invalid-feedback').length <= 0)
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

                "customer_care_number": {
                    required: !0,
                    contact_number: !0
                },
                "email": {
                    required: !0,
                    validate_email: !0
                },
                "password": {
                    required: !0,
                },
                "pass": {
                    required: !0,
                    minlength: 6,
                    // correctPassword: !0
                },

                "day": {
                    required: !0,
                },

                "month": {
                    required: !0,
                },

                "year": {
                    required: !0,
                },

                "hobbies[]": {
                    required: !0
                },


                "confirm-pass": {
                    required: !0,
                    equalTo: "#pass",
                    // correctPassword: !0
                },
                "mobile_number": {
                    required: !0,
                    rangelength: [8, 15],
                },
                "address": {
                    required: !0,
                },
                "country": {
                    required: !0,
                },
                "state": {
                    required: !0,
                },
                "city": {
                    required: !0,
                },
                "slider_image": {
                    extension: "jpeg|png|jpg|gif|svg",
                    required: !0
                    // filesize_max: !0
                },
                "cat_image": {
                    extension: "jpeg|png|jpg|gif|svg",
                    required: !0
                    // filesize_max: !0
                },
                "cat_banner": {
                    extension: "jpeg|png|jpg|gif|svg",
                    required: !0
                    // filesize_max: !0
                },
                "description": {
                    required: !0,
                },
                "role": {
                    required: !0,
                },
                "name": {
                    required: !0,
                    lettersonly: !0,
                    maxlength: 100,
                    minlength: 3,
                    non_whitespace: !0
                },
                "first_name": {
                    required: !0,
                    lettersonly: !0,
                    maxlength: 100,
                    minlength: 3,
                    non_whitespace: !0
                },
                "last_name": {
                    required: !0,
                    lettersonly: !0,
                    maxlength: 100,
                    minlength: 3,
                    non_whitespace: !0
                },
                "role_name": {
                    required: !0,
                    maxlength: 100,
                    lettersonly: !0,
                    minlength: 3,
                    remote: APP_NAME + "/admin/roles/checkRole",
                    non_whitespace: !0
                },
                "user_name": {
                    required: !0,
                },
                "module[]": {
                    required: !0,
                },
                "phonecode": {
                    required: !0,
                },
                "staff_email": {
                    required: !0,
                    validate_email: !0,
                    remote: APP_NAME + "/admin/staffs/checkStaff",
                    non_whitespace: !0
                },
                "driver_email": {
                    required: !0,
                    validate_email: !0,
                    remote: APP_NAME + "/admin/drivers/checkDriver",
                    non_whitespace: !0
                },
                "driver_phone": {
                    required: !0,
                    rangelength: [8, 15],
                    non_whitespace: !0,
                    remote: APP_NAME + "/admin/drivers/checkDriver",
                    // remote: {
                    //     url: APP_NAME + "/admin/drivers/checkPhone",
                    //     type: "get",
                    //     data: {
                    //         phone_code: function() {
                    //             return $("#phonecode").val();
                    //         },
                    //     }
                    // },
                },
                "subject": {
                    required: !0,
                    alphanumericspace: !0,
                    maxlength: 200,
                    remote: APP_NAME + "/admin/subjects/checkSubject",
                    non_whitespace: !0
                },
                "brand_name": {
                    required: !0,
                    maxlength: 100,
                    alphanumericspace: !0,
                    minlength: 3,
                    remote: APP_NAME + "/admin/brands/checkBrand",
                    non_whitespace: !0
                },
                "country_unit": {
                    required: !0
                },
                "user_type": {
                    required: !0
                },
                "offer_image": {
                    extension: "jpeg|png|jpg|gif|svg",
                    required: !0
                    // filesize_max: !0
                },
                "offer_type": {
                    required: !0
                },
                "discount_value": {
                    required: !0,
                    maxlength: 20,
                    max: 100
                },
                "start_date": {
                    required: !0
                },
                "end_date": {
                    required: !0
                },
                "categories[]": {
                    required: !0
                },
                "coupon_categories[]": {
                    required: !0
                },
                "area": {
                    required: !0,
                    alphanumericspace: !0
                },
                "unit_name": {
                    required: !0,
                    remote: APP_NAME + "/admin/units/checkUnit",
                    maxlength: 50,
                    alphanumericspace: !0,
                    non_whitespace: !0
                },
                "unit_fullname": {
                    required: !0,
                    maxlength: 100,
                    alphanumericspace: !0,
                    non_whitespace: !0
                },
                "tax_name": {
                    required: !0,
                    maxlength: 50,
                    non_whitespace: !0
                },
                "tax_value": {
                    required: !0,
                    maxlength: 10
                },
                "product_image": {
                    required: !0,
                    extension: "jpeg|png|jpg|gif|svg",
                },
                "category_id": {
                    required: !0,
                },
                "product_name": {
                    required: !0,
                    maxlength: 100,
                    remote: {
                        url: APP_NAME + "/admin/products/checkProduct",
                        type: "post",
                        data: {
                            category: function() {
                                return $("#category_id").val();
                            },
                        }
                    },
                    non_whitespace: !0
                },
                "from_time": {
                    required: !0,
                    remote: {
                        url: APP_NAME + "/admin/delivery_slots/checkDeliverySlot",
                        type: "post",
                        data: {
                            to_time: function() {
                                return $("#to_time").val();
                            },
                            type: function() {
                                return $("#slot_type").val();
                            },
                            day: function() {
                                return $("#slot_day").val();
                            }
                        }
                    }
                },
                "to_time": {
                    required: !0
                },
                "slot_typenew": {
                    required: !0
                },
                "prod_description": {
                    required: !0,
                    non_whitespace: !0
                },
                "min_amount": {
                    required: !0,
                    min: 1
                },
                "coupon_code": {
                    required: !0,
                    maxlength: 10,
                    alphanumeric: !0,
                    remote: APP_NAME + "/admin/couponCodes/checkCouponCode"
                },
                "coupon_title": {
                    required: !0,
                    maxlength: 200
                },
                "max_use_total": {
                    required: !0
                },
                "max_use": {
                    required: !0
                },
                "min_price": {
                    required: !0,
                    remote: {
                        url: APP_NAME + "/admin/shippingCharges/checkShippingCharge",
                        type: "post",
                        data: {
                            max_price: function() {
                                return $("#max_price").val();
                            },
                        }
                    }
                },
                "max_price": {
                    required: !0,
                },
                "shipping_charge": {
                    required: !0,
                },
                "noti_title": {
                    required: !0,
                    maxlength: 200,
                    non_whitespace: !0
                },
                "noti_description": {
                    required: !0,
                    non_whitespace: !0
                },
                "cms_content": {
                    required: !0,
                },
                /*"noti_image": {
                    dimension: [100, 100]
                },*/
                "product_import": {
                    required: !0,
                    import_extension: "xlsx",
                },
                "product_update": {
                    required: !0,
                    import_extension: "xlsx",
                },
                "attribute_id[]": {
                    required: !0,
                },
                "no_rooms": {
                    required: !0
                },
                "check_in": {
                    required: !0
                },
                "check_out": {
                    required: !0
                },
                "no_adults": {
                    required: !0
                }
            },
            messages: {
                "role_name": {
                    required: "Please enter a role name",
                    minlength: "role name must consist of at least 3 characters",
                    remote: "This role name is already taken."
                },
                "email": {
                    required: "Please provide email address",
                    validate_email: "Please enter a valid email address",
                },
                "password": {
                    required: "Please provide a password",
                },
                "pass": {
                    required: "Please provide a password",
                    minlength: "Your password must be at least 6 characters long"
                },
                "confirm-pass": {
                    required: "Please provide a password",
                    minlength: "Your password must be at least 6 characters long",
                    equalTo: "Please enter the same password as above"
                },
                "mobile_number": {
                    required: "Please provide contact number",
                },
                "address": {
                    required: "Please provide address",
                },
                "role": {
                    required: "Please select user role",
                },
                "name": {
                    required: "Please provide name",
                    minlength: "Name must consist of at least 3 characters"
                },
                "first_name": {
                    required: "Please provide firstname",
                    minlength: "FirstName must consist of at least 3 characters"
                },
                "last_name": {
                    required: "Please provide lastname",
                    minlength: "LastName must consist of at least 3 characters"
                },
                "module[]": {
                    required: "Please grant atleast one module access to this role."
                },
                "phonecode": {
                    required: "Please select phonecode."
                },
                "staff_email": {
                    required: "Please provide email address",
                    validate_email: "Please enter a valid email address",
                    remote: "This email address is already taken."
                },
                "driver_email": {
                    required: "Please provide email address",
                    validate_email: "Please enter a valid email address",
                    remote: "This email address is already taken."
                },
                "driver_phone": {
                    required: "Please provide contact number",
                    remote: "This contact number  is already taken."
                },
                "cat_name": {
                    required: "Please provide category name",
                    remote: "This category already exists."
                },
                "brand_name": {
                    required: "Please provide brand name",
                    maxlength: "Name cannot exceed 100 characters",
                    remote: "This brand is already added."
                },
                "country_unit": {
                    required: "Please select unit"
                },
                "cat_image": {
                    required: "Please provide category image"
                },
                "slider_image": {
                    required: "Please provide slide image"
                },
                "cat_banner": {
                    required: "Please provide category banner"
                },
                "description": {
                    required: "Please provide description"
                },
                "user_type": {
                    required: "Please select user type"
                },
                "offer_image": {
                    required: "Please select offer image"
                },
                "offer_type": {
                    required: "Please select offer type"
                },
                "discount_value": {
                    required: "Please provide discount value",
                    maxlength: "Discoun value cannot be more than 10 digits"
                },
                "start_date": {
                    required: "Please provide offer start date"
                },
                "end_date": {
                    required: "Please provide offer end date"
                },
                "categories[]": {
                    required: "Please select categories to apply offer"
                },
                "coupon_categories[]": {
                    required: "Please select categories to apply coupon code"
                },
                "state": {
                    required: "Please select state"
                },
                "city": {
                    required: "Please select city"
                },
                "area": {
                    required: "Area field is required",
                    remote: "This area is already added in this city"
                },
                "unit_name": {
                    required: "Please provide unit abbreviation",
                    remote: "This unit is already added",
                },
                "unit_fullname": {
                    required: "Please provide unit fullname"
                },
                "tax_name": {
                    required: "Please provide tax name"
                },
                "tax_value": {
                    required: "Please provide tax value",
                    remote: "This tax is already added with this value"
                },
                "product_name": {
                    required: "Please provide product name",
                    remote: "This product is already added in this category"
                },
                "prod_description": {
                    required: "Please provide product description"
                },
                "product_image": {
                    required: "Please select product image"
                },
                "category_id": {
                    required: "Please select category"
                },
                "min_amount": {
                    required: "Please add minimum price"
                },
                "from_time": {
                    required: "Please provide start time",
                    remote: "Slots are already added within this range"
                },
                "to_time": {
                    required: "Please provide end time",
                },
                "slot_typenew": {
                    required: "Please select slot type",
                },
                "slot_day": {
                    required: "Please select day for scheduling a slot",
                },
                "max_order": {
                    required: "Please set the max order limit for this slot",
                },
                "coupon_code": {
                    required: "Please provide code",
                    required: "This code already exists",
                },
                "coupon_title": {
                    required: "Please enter the coupon code title",
                },
                "max_use": {
                    required: "Please set the max no. of usage per user",
                },
                "max_use_total": {
                    required: "Please set the max no. of usage in total",
                },
                "min_price": {
                    required: "Please provide minimum price",
                    remote: "Charges are already added within this range"
                },
                "max_price": {
                    required: "Please provide maximum price",
                },
                "shipping_charge": {
                    required: "Please add shipping charge",
                },
                "noti_title": {
                    required: 'Please provide notification title',
                    maxlength: 'Title cannot exceed 200 characters'
                },
                "noti_description": {
                    required: 'Please provide description',
                },
                "cms_content": {
                    required: 'Please provide page content',
                },
                "product_import": {
                    required: "Please provide file to import",
                },
                "product_update": {
                    required: "Please provide file to import",
                },
                "customer_care_number": {
                    required: "Please provide customer care number.",
                },
                "referrer_amount": {
                    required: "Please provide referrer amount.",
                },
                "android_version_user": {
                    required: "Please provide android version.",
                },
                "android_url_user": {
                    required: "Please provide android app url.",
                },
                "ios_version_user": {
                    required: "Please provide ios version.",
                },
                "ios_url_user": {
                    required: "Please provide ios app url.",
                },
                "app_update_msg": {
                    required: "Please provide app update message.",
                },
                "attribute_id": {
                    required: "Please select atleast one attribute.",
                },
                "no_rooms": {
                    required: "Please select no. of rooms"
                },
                "check_in": {
                    required: "Please select Check-in date"
                },
                "check_out": {
                    required: "Please select Check-out date"
                },
                "no_adults": {
                    required: "Please select no. of adults"
                }
            }
        })
    }
    return {
        init: function() {
            e(), jQuery(".js-select2").on("change", function() {
                jQuery(this).valid()
            })
        }
    }
}();
jQuery(function() {
    form_validation.init()
});