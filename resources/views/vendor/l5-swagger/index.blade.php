<!-- HTML for static distribution bundle build -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{config('l5-swagger.api.title')}}</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700|Source+Code+Pro:300,600|Titillium+Web:400,600,700" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ l5_swagger_asset('swagger-ui.css') }}">
    <link rel="icon" type="image/png" href="{{ l5_swagger_asset('favicon-32x32.png') }}" sizes="32x32" />
    <link rel="icon" type="image/png" href="{{ l5_swagger_asset('favicon-16x16.png') }}" sizes="16x16" />
    <style>
    html {
        box-sizing: border-box;
        overflow: -moz-scrollbars-vertical;
        overflow-y: scroll;
    }

    *,
    *:before,
    *:after {
        box-sizing: inherit;
    }

    body {
        margin: 0;
        background: #fafafa;
    }
    </style>
</head>

<body>
    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="position:absolute;width:0;height:0">
        <defs>
            <symbol viewBox="0 0 20 20" id="unlocked">
                <path d="M15.8 8H14V5.6C14 2.703 12.665 1 10 1 7.334 1 6 2.703 6 5.6V6h2v-.801C8 3.754 8.797 3 10 3c1.203 0 2 .754 2 2.199V8H4c-.553 0-1 .646-1 1.199V17c0 .549.428 1.139.951 1.307l1.197.387C5.672 18.861 6.55 19 7.1 19h5.8c.549 0 1.428-.139 1.951-.307l1.196-.387c.524-.167.953-.757.953-1.306V9.199C17 8.646 16.352 8 15.8 8z"></path>
            </symbol>
            <symbol viewBox="0 0 20 20" id="locked">
                <path d="M15.8 8H14V5.6C14 2.703 12.665 1 10 1 7.334 1 6 2.703 6 5.6V8H4c-.553 0-1 .646-1 1.199V17c0 .549.428 1.139.951 1.307l1.197.387C5.672 18.861 6.55 19 7.1 19h5.8c.549 0 1.428-.139 1.951-.307l1.196-.387c.524-.167.953-.757.953-1.306V9.199C17 8.646 16.352 8 15.8 8zM12 8H8V5.199C8 3.754 8.797 3 10 3c1.203 0 2 .754 2 2.199V8z" />
            </symbol>
            <symbol viewBox="0 0 20 20" id="close">
                <path d="M14.348 14.849c-.469.469-1.229.469-1.697 0L10 11.819l-2.651 3.029c-.469.469-1.229.469-1.697 0-.469-.469-.469-1.229 0-1.697l2.758-3.15-2.759-3.152c-.469-.469-.469-1.228 0-1.697.469-.469 1.228-.469 1.697 0L10 8.183l2.651-3.031c.469-.469 1.228-.469 1.697 0 .469.469.469 1.229 0 1.697l-2.758 3.152 2.758 3.15c.469.469.469 1.229 0 1.698z" />
            </symbol>
            <symbol viewBox="0 0 20 20" id="large-arrow">
                <path d="M13.25 10L6.109 2.58c-.268-.27-.268-.707 0-.979.268-.27.701-.27.969 0l7.83 7.908c.268.271.268.709 0 .979l-7.83 7.908c-.268.271-.701.27-.969 0-.268-.269-.268-.707 0-.979L13.25 10z" />
            </symbol>
            <symbol viewBox="0 0 20 20" id="large-arrow-down">
                <path d="M17.418 6.109c.272-.268.709-.268.979 0s.271.701 0 .969l-7.908 7.83c-.27.268-.707.268-.979 0l-7.908-7.83c-.27-.268-.27-.701 0-.969.271-.268.709-.268.979 0L10 13.25l7.418-7.141z" />
            </symbol>
            <symbol viewBox="0 0 24 24" id="jump-to">
                <path d="M19 7v4H5.83l3.58-3.59L8 6l-6 6 6 6 1.41-1.41L5.83 13H21V7z" />
            </symbol>
            <symbol viewBox="0 0 24 24" id="expand">
                <path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z" />
            </symbol>
        </defs>
    </svg>

    <div id="swagger-ui"></div>
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="{{ l5_swagger_asset('swagger-ui-bundle.js') }}"> </script>
    <script src="{{ l5_swagger_asset('swagger-ui-standalone-preset.js') }}"> </script>
    <script>
    <?php

if (!isset($_SERVER['PHP_AUTH_USER'])) {
	header('WWW-Authenticate: Basic realm="Test Authentication System"');
	header('HTTP/1.0 401 Unauthorized');
	echo "You must enter a valid login ID and password to access this resource\n";
	exit;
} else {
	if (Auth::guard('front')->attempt(['email' => $_SERVER['PHP_AUTH_USER'], 'password' => $_SERVER['PHP_AUTH_PW'], 'status' => 'AC', 'admin_verify' => 1, 'is_verified' => 1]) && Auth::attempt(['email' => $_SERVER['PHP_AUTH_USER'], 'password' => $_SERVER['PHP_AUTH_PW'], 'status' => 'AC', 'admin_verify' => 1, 'is_verified' => 1])) {

		Session::put('user', Auth::user());
		Session::put('logtoken', Auth::user()->createToken('Thoraipakkam OMR Branch Login')->accessToken);

		$auth_user = \App\model\User::find(Auth::user()->id);
		$access = [];

		if ($auth_user->travel_access == 'yes') {
			$access[] = 'travel';
		}
		if ($auth_user->travel_with_api_access == 'yes') {
			$access[] = 'travel_api';
		}
		if ($auth_user->grocery_front_access == 'yes') {
			$access[] = 'grocery';
		}
		if ($auth_user->sample_api_access == 'yes') {
			$access[] = 'sample_api';
		}
		if ($auth_user->database_access == 'yes') {
			$access[] = 'database';
		}
		if ($auth_user->admin_panel_access == 'yes') {
			$access[] = 'admin_panel';
		}
		Session::put('access', $access);
		?>

    window.onload = function() {


        // echo "<p>Hello {$_SERVER['PHP_AUTH_USER']}.</p>";
        // echo "<p>You entered {$_SERVER['PHP_AUTH_PW']} as you password.</p>";

        // Build a system
        const ui = SwaggerUIBundle({
            dom_id: '#swagger-ui',

            url: "{!! $urlToDocs !!}",
            operationsSorter: {!!isset($operationsSorter) ? '"'.$operationsSorter. '"' : 'null'!!},
            configUrl: {!!isset($additionalConfigUrl) ? '"'.$additionalConfigUrl. '"' : 'null'!!},
            validatorUrl: {!!isset($validatorUrl) ? '"'.$validatorUrl. '"' : 'null'!!},
            oauth2RedirectUrl: "{{ route('l5-swagger.oauth2_callback') }}",

            // requestInterceptor: function() {
            //   this.headers['X-CSRF-TOKEN'] = '{{ csrf_token() }}';
            //   return this;
            // },

            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ],

            plugins: [
                SwaggerUIBundle.plugins.DownloadUrl
            ],

            layout: "StandaloneLayout"
        })

        window.ui = ui

        var intr = setInterval(function() {

            if ($('.download-url-wrapper').is(':visible')) {
                $('.download-url-wrapper').hide();
                clearInterval(intr);
            }

            $('.information-container.wrapper').append('<div style="text-align:center"><h1 data-testid="join-automation">For Joining Automation Course</h1><p style="font-size:20px"> Please Contact-<span style="font-weight:bold">Velmurugan <br>9944152058</span></p></div>');

        }, 1000);

        @if(Session::has('access') && !in_array('grocery', Session::get('access')))
        var inter = setInterval(function() {
            if ($('[data-tag="Account"]').closest('span').length >= 1) {
                $('[data-tag="Account"]').closest('span').remove();
                $('[data-tag="Cart"]').closest('span').remove();
                $('[data-tag="Order"]').closest('span').remove();
                $('[data-tag="Products"]').closest('span').remove();
                clearInterval(inter);
            }
        }, 1000);
        @endif
        @if(Session::has('access') && !in_array('travel_api', Session::get('access')))
        var inter1 = setInterval(function() {

            if ($('[data-tag="Booking Api"]').closest('span').length >= 1) {
                $('[data-tag="Booking Api"]').closest('span').remove();
                clearInterval(inter1);
            }
        }, 1000);
        @endif
        @if(Session::has('access') && !in_array('travel_api', Session::get('access')) && !in_array('grocery', Session::get('access')))
        var inter2 = setInterval(function() {

            if ($('[data-tag="Authentication"]').closest('span').length >= 1) {
                $('[data-tag="Authentication"]').closest('span').remove();
                clearInterval(inter2);

                setTimeout(function() {
                    alert('You do not have access to any of the API');
                }, 1000)
            }
        }, 1000);
        @endif

    }
    <?php
} else {
		?>
    alert('Invalid Credentials');
    <?php
header('WWW-Authenticate: Basic realm="Test Authentication System"');
		header('HTTP/1.0 401 Unauthorized');
		echo "You must enter a valid login ID and password to access this resource\n";
		exit;
	}
}
?>
    </script>
</body>

</html>