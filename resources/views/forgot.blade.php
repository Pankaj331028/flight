
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
        <title>omrbranch - Forgot Password</title>
        <link rel="stylesheet" href="{{URL::asset('/css/font-awesome/css/font-awesome.min.css')}}">
        <link rel="stylesheet" href="{{URL::asset('/css/mdi/css/materialdesignicons.min.css')}}">
        <link rel="stylesheet" href="{{URL::asset('/css/toastr/toastr.min.css')}}">
        <link rel="stylesheet" href="{{URL::asset('/css/vendor.bundle.base.css')}}">
        <link rel="stylesheet" href="{{URL::asset('/css/vendor.bundle.addons.css')}}">
        <link rel="stylesheet" href="{{URL::asset('/css/styles.css')}}">
        <link rel="stylesheet" href="{{URL::asset('/css/style.css')}}">
        <link rel="stylesheet" href="{{URL::asset('/css/animate.css')}}">
        <link rel="stylesheet" href="{{URL::asset('/css/custom.css')}}">
        <link rel="shortcut icon" href="{{URL::asset('images/favicon.png')}}" />
    </head>
    <body class="fix-header fix-sidebar card-no-border">
        <div class="preloader">
            <div class="loader">
                <div class="loader__figure"></div>
                <p class="loader__label">omrbranch</p>
            </div>
        </div>
        <div class="container-scroller">
            <div class="container-fluid page-body-wrapper full-page-wrapper auth-page">
                <div class="content-wrapper d-flex align-items-center auth register-bg-1 theme-one">
                    <div class="row w-100">
                        <div class="col-lg-4 mx-auto">
                            <h2 class="text-center mb-4">Forgot Password</h2>
                            @include('layouts.message')
                            <div class="auto-form-wrapper">
                                <form class="form-valide" method="post" action="{{route('forgotPost')}}">
                                    {{csrf_field()}}
                                    <div class="form-group">
                                        <label class="label">Email <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Email" name="email">
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="mdi mdi-check-circle-outline"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-flat m-b-30">Send Mail</button>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="{{URL::asset('/plugins/jquery/jquery.min.js')}}"></script>
        <script src="{{URL::asset('/js/vendor.bundle.base.js')}}"></script>
        <script src="{{URL::asset('/js/vendor.bundle.addons.js')}}"></script>
        <script src="{{URL::asset('/js/misc.js')}}"></script>
        <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.js" type="text/javascript"></script>
        <script src="{{URL::asset('/js/jquery.validate.min.js')}}"></script>
        <script src="{{URL::asset('/js/jquery.validate-init.js')}}"></script>

        @stack('scripts')
        <script type="text/javascript">
            $('div.alert').delay(10000).slideUp(500);
            $(".preloader").fadeOut();

        </script>

    </body>
</html>
