
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Tell the browser to be responsive to screen width -->
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="csrf-token" content="{{csrf_token()}}">
        <title>omrbranch - @yield('title')</title>
        <link rel="stylesheet" href="{{URL::asset('/plugins/bootstrap/css/bootstrap.min.css')}}">
        <link rel="stylesheet" href="{{URL::asset('/css/font-awesome/css/font-awesome.min.css')}}">
        <link rel="stylesheet" href="{{URL::asset('/css/mdi/css/materialdesignicons.min.css')}}">
        <link rel="stylesheet" href="{{URL::asset('/css/toastr/toastr.min.css')}}">
        <link rel="stylesheet" href="{{URL::asset('/css/vendor.bundle.base.css')}}">
        <link rel="stylesheet" href="{{URL::asset('/css/vendor.bundle.addons.css')}}">
        <link rel="stylesheet" href="{{URL::asset('/css/styles.css')}}">
        <link rel="stylesheet" href="{{URL::asset('/css/style.css')}}">
        <link rel="stylesheet" href="{{URL::asset('/css/animate.css')}}">
        <link rel="stylesheet" href="{{URL::asset('/css/custom.css')}}">
        <link rel="stylesheet" href="{{URL::asset('/css/pages/file-upload.css')}}">
        <link href="{{URL::asset('/plugins/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.css')}}" rel="stylesheet">
        <link href="{{URL::asset('/plugins/bootstrap-switch/bootstrap-switch.min.css')}}" rel="stylesheet">
        <link href="{{URL::asset('/css/pages/bootstrap-switch.css')}}" rel="stylesheet">
        <link href="{{URL::asset('/plugins/switchery/dist/switchery.min.css')}}" rel="stylesheet" />
        <link href="{{URL::asset('/plugins/summernote/dist/summernote.css')}}" rel="stylesheet" />
        <link href="{{URL::asset('/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css')}}" rel="stylesheet">
        <link href="{{URL::asset('/plugins/nestable/nestable.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{URL::asset('/plugins/perfect-scrollbar/css/perfect-scrollbar.css')}}" rel="stylesheet">
        <link href="{{URL::asset('/plugins/bootstrap-select/bootstrap-select.min.css')}}" rel="stylesheet">
        <link rel="shortcut icon" href="{{URL::asset('images/favicon.png')}}" />
        <script src="{{URL::asset('/plugins/jquery/jquery.min.js')}}"></script>
        <script type="text/javascript">
            var APP_NAME ='/omrbranch';
        </script>
    </head>
    <body class="fix-header fix-sidebar card-no-border">
        <div class="preloader">
            <div class="loader">
                <div class="loader__figure"></div>
                <p class="loader__label">omrbranch</p>
            </div>
        </div>
        <div class="container-scroller">
            <div class="container-fluid page-body-wrapper">
                @include('layouts.header')
                @include('layouts.sidebar')
                <div class="main-panel">
                    @yield('content')
                    @include('layouts.footer')
                </div>
            </div>
        </div>
        <script src="{{URL::asset('/js/vendor.bundle.base.js')}}"></script>
        <script src="{{URL::asset('/js/vendor.bundle.addons.js')}}"></script>
        <script src="{{URL::asset('/js/off-canvas.js')}}"></script>
        <script src="{{URL::asset('/js/misc.js')}}"></script>
        <script src="{{URL::asset('/js/jasny-bootstrap.js')}}"></script>
        <script src="{{URL::asset('/plugins/dropzone-master/dist/dropzone.js')}}"></script>
        {{-- <script src="{{URL::asset('/plugins/switchery/dist/switchery.min.js')}}"></script> --}}
        <script src="{{URL::asset('/plugins/bootstrap-switch/bootstrap-switch.min.js')}}"></script>
        <script src="{{URL::asset('/plugins/styleswitcher/jQuery.style.switcher.js')}}"></script>
        <script src="{{URL::asset('/plugins/summernote/dist/summernote.min.js')}}"></script>
        <script src="{{URL::asset('/js/jquery.validate.js')}}" type="text/javascript"></script>
        <script src="{{URL::asset('/plugins/nestable/jquery.nestable.js')}}"></script>
        <script src="{{URL::asset('/js/jquery.validate.min.js')}}"></script>
        <script src="{{URL::asset('/js/jquery.validate-init.js')}}"></script>
        <script src="{{URL::asset('/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js')}}"></script>
        <script src="{{URL::asset('/plugins/bootstrap-select/bootstrap-select.min.js')}}" type="text/javascript"></script>


        @stack('scripts')
        <script type="text/javascript">
            $('div.alert').delay(10000).slideUp(500);

            $(".bt-switch input[type='checkbox']:visible").bootstrapSwitch();
            $(".tab-pane .bt-switch input[type='checkbox']").bootstrapSwitch();
            $(".preloader").fadeOut();

            $('body').tooltip({selector: '[data-toggle="tooltip"]'});
            $('body').tooltip({selector: '[data-toggle="popover"]'});

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#from_date').bootstrapMaterialDatePicker({ weekStart: 0, time: false }).on('change', function(e, date){
                $('#end_date').bootstrapMaterialDatePicker('setMinDate', date);
                $('#end_date').val('');
            });

            $('#end_date').bootstrapMaterialDatePicker({ weekStart: 0, time: false });

            $('#start_date').bootstrapMaterialDatePicker({ weekStart: 0, time: false, minDate: new Date() }).on('change', function(e, date){
                $('#end_date').bootstrapMaterialDatePicker('setMinDate', date);
                $('#end_date').val('');
            });
            $('#end_date').bootstrapMaterialDatePicker({ weekStart: 0, time: false });

            $('#paymentmodefrom_date').bootstrapMaterialDatePicker({ weekStart: 0, time: false }).on('change', function(e, date){
                $('#paymentmodeend_date').bootstrapMaterialDatePicker('setMinDate', date);
                $('#paymentmodeend_date').val('');
            });
            $('#paymentmodeend_date').bootstrapMaterialDatePicker({ weekStart: 0, time: false });

            $('#cancelmodefrom_date').bootstrapMaterialDatePicker({ weekStart: 0, time: false }).on('change', function(e, date){
                $('#cancelmodeend_date').bootstrapMaterialDatePicker('setMinDate', date);
                $('#cancelmodeend_date').val('');
            });
            $('#cancelmodeend_date').bootstrapMaterialDatePicker({ weekStart: 0, time: false });

            $('#barmodefrom_date').bootstrapMaterialDatePicker({ weekStart: 0, time: false }).on('change', function(e, date){
                $('#barmodeend_date').bootstrapMaterialDatePicker('setMinDate', date);
                $('#barmodeend_date').val('');
            });
            $('#barmodeend_date').bootstrapMaterialDatePicker({ weekStart: 0, time: false });

            $('#stateFilter').change(function(){
                var state = $(this).val();

                if(state!='' && state!='all'){
                    $.ajax({
                        url: "{{route('getCity')}}",
                        type: "POST",
                        data: {state: state},
                        success: function(data){
                            var cities = JSON.parse(data);
                            var options = "<option value='all' selected>All</option>";
                            for (var i = 0; i < cities.length; i++) {
                                options += "<option value='" + cities[i] + "'>" + cities[i] + "</option>";
                            }
                            $('#cityFilter').html(options);
                            $('#cityFilterCol').attr('style','margin: 15px 0;display:flex !important');
                        }
                    })
                }
                else{
                    var options = "<option value='all' selected>Select State first</option>";
                    $('#cityFilter').html(options);
                    $('#cityFilterCol').attr('style','margin: 15px 0;display:none !important');
                }
            });

            $('#countryFilter').change(function(){
                var country = $(this).val();

                if(country!='' && country!='all'){

                    $.ajax({
                        url: "{{route('getState')}}",
                        type: "POST",
                        data: {country: country},
                        success: function(data){
                            var states = JSON.parse(data);
                            var options = "<option value='all' selected>All</option>";
                            for (var i = 0; i < states.length; i++) {
                                options += "<option value='" + states[i] + "'>" + states[i] + "</option>";
                            }
                            $('#stateFilter').html(options);
                            $('#cityFilter').html('<option value="all">Select State first</option>');
                            $('#stateFilterCol').attr('style','margin: 15px 0;display:flex !important');
                            $('#cityFilterCol').attr('style','margin: 15px 0;display:none !important');
                        }
                    })
                }
                else{
                    var options = "<option value='all' selected>Select Country first</option>";
                    $('#stateFilter').html(options);
                    $('#stateFilterCol').attr('style','margin: 15px 0;display:none !important');
                    $('#cityFilterCol').attr('style','margin: 15px 0;display:none !important');
                }
            });

            $('#applyFilter').click(function(){
                var country = $('#countryFilter').val();
                var state = $('#stateFilter').val();
                var city = $('#cityFilter').val();
                $.ajax({
                    url: "{{route('setCitySession')}}",
                    type: "POST",
                    data: {city: city,country:country,state:state},
                    success: function(data){
                        window.location.reload();
                    }
                })

            });

            $('#resetFilter').click(function(){
                var country = $('#countryFilter').val();
                var state = $('#stateFilter').val();
                var city = $('#cityFilter').val();
                $.ajax({
                    url: "{{route('resetCitySession')}}",
                    type: "POST",
                    data: {city: city,country:country,state:state},
                    success: function(data){
                        window.location.reload();
                    }
                })

            });

            $('#dismissFilter').click(function(){
                $.ajax({
                    url: "{{route('getState')}}",
                    type: "POST",
                    data: {country: "{{session()->get('globalCountry')}}"},
                    success: function(data){
                        var states = JSON.parse(data);
                        var options = "<option value='all'>All</option>";
                        for (var i = 0; i < states.length; i++) {
                            options += "<option value='" + states[i] + "'>" + states[i] + "</option>";
                        }
                        $('#stateFilter').html(options);
                        $('#cityFilter').html('<option value="all">Select State first</option>');
                        $('#stateFilter').val("{{session()->get('globalState')}}");
                    }
                });
                $.ajax({
                    url: "{{route('getCity')}}",
                    type: "POST",
                    data: {state: "{{session()->get('globalState')}}"},
                    success: function(data){
                        var cities = JSON.parse(data);
                        var options = "<option value='all'>All</option>";
                        for (var i = 0; i < cities.length; i++) {
                            options += "<option value='" + cities[i] + "'>" + cities[i] + "</option>";
                        }
                        $('#cityFilter').html(options);
                        $('#cityFilter').val("{{session()->get('globalCity')}}");
                    }
                });
                $('#countryFilter').val("{{session()->get('globalCountry')}}");
                @if(session()->get('globalState') == 'all')
                    $('#stateFilterCol').attr('style','margin: 15px 0;display:none !important');
                    $('#cityFilterCol').attr('style','margin: 15px 0;display:none !important');
                @endif
                @if(session()->get('globalCity') == 'all')
                    $('#cityFilterCol').attr('style','margin: 15px 0;display:none !important');
                @endif

            });

            $('#typeFilter').change(function(){
                if($(this).val() != 'discount'){
                    $('#basePlanCol').hide();
                    $('#basePlanCol').find('select[name=plan]').prop('selectedIndex',0);
                }
                else{
                    $('#basePlanCol').show();
                }
            })

            $('form').submit(function(e){
                if($(this).valid()){
                    $('button[type="submit"]').prop('disabled',true);
                }else{
                    $('button[type="submit"]').prop('disabled',false);
                }
            });
        </script>
    </body>
</html>
