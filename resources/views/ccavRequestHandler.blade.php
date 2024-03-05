<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{URL::asset('/images/favicon.png')}}">
    <title>omrbranch - CCAvenue Payment</title>
    <link href="{{URL::asset('/public/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('/public/plugins/perfect-scrollbar/css/perfect-scrollbar.css')}}" rel="stylesheet">
    <!-- This page CSS -->
    <!-- chartist CSS -->
    <link href="{{URL::asset('/public/plugins/chartist-js/dist/chartist.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('/public/plugins/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.css')}}" rel="stylesheet">
    <!--c3 CSS -->
    <link href="{{URL::asset('/public/plugins/c3-master/c3.min.css')}}" rel="stylesheet">
    <!--Toaster Popup message CSS -->
    <!-- <link href="{{URL::asset('/plugins/toast-master/css/jquery.toast.css')}}" rel="stylesheet"> -->
    <!-- Custom CSS -->
    <link href="{{URL::asset('/public/css/style.css')}}" rel="stylesheet">
    <link href="{{URL::asset('/public/plugins/switchery/dist/switchery.min.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('/public/css/pages/dashboard2.css')}}" rel="stylesheet">
    <link href="{{URL::asset('/public/css/colors/default-dark.css')}}" id="theme" rel="stylesheet">
    <link href="{{URL::asset('/public/css/toastr/toastr.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('/public/plugins/summernote/dist/summernote.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('/public/css/pages/file-upload.css')}}" rel="stylesheet">
    <link href="{{URL::asset('/public/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css')}}"
        rel="stylesheet">
    <style type="text/css">
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }

        .displayOverlay {
            position: absolute;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 99;
            background: rgba(255, 255, 255, 0.8);
            font-size: 1.5em;
            text-align: center;
            padding-top: 100px;
        }

        .editableDiv {
            border: 1px solid #ced4da;
            overflow: auto;
            min-height: 100px;
            resize: both;
            width: 70%;
        }

        iframe {
            border: 1px solid lightgray;
        }
    </style>
</head>

<body class="fix-header fix-sidebar card-no-border text-center">
    <div class="preloader">
        <div class="loader">
            <div class="loader__figure"></div>
            <p class="loader__label">omrbranch</p>
        </div>
    </div>
    <div id="main-wrapper" class="d-flex flex-column align-items-center justify-content-center">


<center>
<form method="post" name="redirect" action="https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction">
@php
echo "<input type=hidden name=encRequest value=$encrypted_data>";
echo "<input type=hidden name=access_code value=$access_code>";
echo "<input type=hidden name=custom_code value=$custom_code>";
@endphp
</form>
</center>
<script language='javascript'>document.redirect.submit();</script>

</body>

</html>
