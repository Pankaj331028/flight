<?php
return [
	'RESPONSE' => array(
		"status" => 500,
		"message" => "Internal server error",
		"data" => "",
	),
	'MAIL_SENDER_ID' => 'apoorva.vervelogic@gmail.com',
	'CURRENCY' => '₹',
	"active" => "#1a2f20",
	"inactive" => "#907138",
	'CONFIRM' => [0 => 'No', 1 => 'Yes'],
	'STATUS' => ['AC' => 'Active', 'IN' => 'Inactive', 'DL' => 'Delete', 'RJ' => 'Rejected', 'AP' => 'Approved', 'PN' => 'Pending', 'CM' => 'Delivered', 'CL' => 'Cancelled', 'RFIN' => 'Cancel Request Pending', 'RFCM' => 'Cancelled', 'RFCL' => 'Cancel Request Rejected', 'RN' => 'Returned'],
	'SLIDER_TYPE' => ['banner' => 'Banner', 'slider' => 'Slider'],
	'PLAN_TYPE' => ['percent' => 'Percent', 'fixed' => 'Fixed'],
	'USER_TYPE' => ['user' => 'User', 'provider' => 'OmrBranch'],
	'PRODUCT_TYPE' => ['single' => 'Single', 'variable' => 'Variable'],
	'UID' => ['g_modules' => 'MOD', 'c_categories' => 'CAT', 'c_brands' => 'BRN', 'c_offers' => 'OFF', 'c_areas' => 'LOC', 'products' => 'PROD', 'g_users' => 'USR', 'c_delivery_slots' => 'SLOT', 'c_carts' => 'CART', 'user_address' => 'ADD', 'c_shipping_charges' => 'SHIP', 'orders' => 'ORD', 't_transactions' => 'TXN'],
	'OPERATIONS' => ['list' => 'List', 'add' => 'Add', 'edit' => 'Edit', 'view' => 'View', 'delete' => 'Delete', 'block' => 'Block', 'report' => 'Generate Reports', 'update_status' => 'Status Update'],
	'WEEK_DAY' => ['sun' => 'Sunday', 'mon' => 'Monday', 'tue' => 'Tuesday', 'wed' => 'Wednesday', 'thu' => 'Thursday', 'fri' => 'Friday', 'sat' => 'Saturday'],
	'SLOT_TYPE' => ['single' => 'Single Day Selection', 'multiple' => 'Multiple Days Schedule'],
	'PAYMENT_METHOD' => ['cod' => 'Cash On Delivery', 'debit_card' => 'Debit Card', 'credit_card' => 'Credit Card'],

	'debit_card' => [
		'amex' => [
			'5555555555550000',
			'5555555555551111',
		],
		'visa' => [
			'5555555555552222',
			'5555555555553333',
		],
		'master' => [
			'5555555555554444',
			'5555555555555555',
		],
		'discover' => [
			'5555555555556666',
			'5555555555557777',
		],
	],

	'credit_card' => [
		'amex' => [
			'5555555555550001',
			'5555555555551112',
		],
		'visa' => [
			'5555555555552223',
			'5555555555553334',
		],
		'master' => [
			'5555555555554445',
			'5555555555555556',
		],
		'discover' => [
			'5555555555556667',
			'5555555555557778',
		],
	],
	'room_types' => [
		'standard' => 0,
		'deluxe' => 100,
		'suite' => 200,
		'luxury' => 300,
		'studio' => 400,
	],
	'tax' => '18',
	'upi' => [
		'seleniumtraining@vbc', 'javatraining@vbc', 'apitraining@vbc',
	],
	'COUNTING' => ['1' => '1-One', '2' => '2-Two', '3' => '3-Three', '4' => '4-Four', '5' => '5-Five'],
	'gst' => [
		'registration_no' => '9043592058',
		'name' => 'Greens Tech OMR Branch',
		'address' => 'Thoraipakkam',
	],
];
