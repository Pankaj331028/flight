<?php

return [

	/*
		    |--------------------------------------------------------------------------
		    | Third Party Services
		    |--------------------------------------------------------------------------
		    |
		    | This file is for storing the credentials for third party services such
		    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
		    | default location for this type of information, allowing packages
		    | to have a conventional place to find your various credentials.
		    |
	*/

	'mailgun' => [
		'domain' => env('MAILGUN_DOMAIN'),
		'secret' => env('MAILGUN_SECRET'),
	],

	'ses' => [
		'key' => env('SES_KEY'),
		'secret' => env('SES_SECRET'),
		'region' => 'us-east-1',
	],

	'sparkpost' => [
		'secret' => env('SPARKPOST_SECRET'),
	],

	'stripe' => [
		'model' => App\Model\User::class,
		'key' => env('STRIPE_KEY'),
		'secret' => env('STRIPE_SECRET'),
	],
	'paytm' => [
		'merchant_key' => '',
		'industry_type' => '',
		'staging_mid' => '',
		'channel_id' => '',
		'app_name' => '',
	],

	'facebook' => [
		'client_id' 	=> '2883288261755948',
		'client_secret' => '4c8f9852024be3cc656b2c473d3eda87',
		// 'redirect' 	    => 'http://localhost:8091/social-login-callback/facebook',
		'redirect' => 'http://gogrocery.vlcare.com/grocery_store/social-login-callback/facebook',
	],

	'google' => [
		'client_id'     => "375667378859-hu04kikm8aivrrbh1515c03uq44dfipa.apps.googleusercontent.com",
		'client_secret' => "ENyOW3qB2g9XUZu14BrfQTtP",
		// 'redirect'		=> 'http://localhost:8091/social-login-callback/google',
		'redirect'      => "http://gogrocery.vlcare.com/grocery_store/social-login-callback/google"
	],
];
