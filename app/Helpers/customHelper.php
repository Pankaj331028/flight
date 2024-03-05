<?php

// Function to generate OTP
function generateNumericOTP($n) {
	$generator = "1357902468";
	$result = "";

	for ($i = 1; $i <= $n; $i++) {
		$result .= substr($generator, (rand() % (strlen($generator))), 1);
	}

	return $result;
}

function sendSMS($mobile, $msg) {
	$ch = curl_init('https://http.myvfirst.com/smpp/sendsms?to=' . $mobile . '&from=VBCLUB&text=' . rawurlencode($msg) . '&dlr-mask=19&dlr-url');

	$header = [
		'Authorization: Bearer eyJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL2FwaS5teXZhbHVlZmlyc3QuY29tL3BzbXMiLCJzdWIiOiJ2YmNsdWIiLCJleHAiOjE5Njg0NTA2MTF9.zaskaugO30olHB5kEu4tjfD_23kO2G4ulwfogx72VRA',
	];
	curl_setopt($ch, CURLOPT_POST, true);
	//curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	return $response;
}
