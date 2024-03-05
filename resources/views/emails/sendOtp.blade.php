
<div style="font-family: Helvetica,Arial,sans-serif;min-width:1000px;overflow:auto;line-height:2">
  <div style="margin:50px auto;width:70%;padding:20px 0">
    <div style="border-bottom:1px solid #eee">
      <a href="" style="font-size:1.4em;color: #00466a;text-decoration:none;font-weight:600">Your Brand</a>
    </div>
    <p style="font-size:1.1em">Hi,</p>
    <p></p>
    <h2 style="background: #00466a;margin: 0 auto;width: max-content;padding: 0 10px;color: #fff;border-radius: 4px;"> {{ $otp ?? '-' }}  </h2>
    <p style="font-size:0.9em;">Regards,<br /> omrbranch Team </p>
    <hr style="border:none;border-top:1px solid #eee" />
    <div style="float:right;padding:8px 0;color:#aaa;font-size:0.8em;line-height:1;font-weight:300">
      <p> omrbranch </p>

    </div>
  </div>
</div>






<!DOCTYPE html>
<html lang="en">
<head>
<title>omrbranch</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<body style="background-color:#fff; font-family:arial; font-size:14px;">
<center>
    <table width="100%"  cellspacing="0" cellpadding="20" style="margin-bottom: 40px;">
        <thead align="center">
            <tr>
                <th style="width: 50%;">
                    <img src="{{ asset('/front/images/logo.png') }}" width="20%" height="auto">
                </th>
            </tr>
        </thead>
    </table>
	<table width="800" cellspacing="0" style="border:1px solid #40b851; border-radius:5px; overflow:hidden">
		<tr>
			<td bgcolor="#40b851" style="padding:10px 20px;">
				<table style="width:100%;">
					<tr>
						<td width="40%" style="color:#fff;">
							omrbranch
						</td>
						<td style="color:#fff">
							&nbsp;
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td style="text-align:left; padding:10px 0px 20px 20px;" bgcolor="#f9f9f9">
				<div style="margin:20px auto 10px; font-size:16px;"><h4 style="text-transform: capitalize;"><b>Hello,</b></h4></div>
                <div style="margin:20px auto 10px;">
                    <p style="font-size: 16px;color: #5f5e5e;line-height: 1.5;margin-top: 10px;">Thank you for choosingomrbranch. Use the following OTP to complete your Sign Up procedures. OTP is valid for 5 minutes  <a href="{{ config('app.url') }}" style="color : #40b851;font-weight:bold;">omrbranch App </a> to continue browsing.</p>
                </div>
               <h2 style="background: #00466a;margin: 0 auto;width: max-content;padding: 0 10px;color: #fff;border-radius: 4px;"> {{ $otp ?? '-' }}  </h2>

			</td>
		</tr>
		<tr>
			<td style="text-align:left; padding:10px 0px 20px 20px; border-bottom:1px solid #e1e1e1;" bgcolor="#f9f9f9">
				<div style="font-size:14px;"><b>For Joining Automation course -</b></div>
				<div style="font-size:14px;">Please contact - Velmurugan<br><a href="tel:9944152058">99441 52058</a>
					<p>Thanks,<br>
					Velmurugan</p>
				</div>
			</td>
		</tr>
		<tr>
			<td style="text-align:center; padding:20px 10px 20px; font-size:15px; color:#666;">Copyright @ {{ date('Y') }}omrbranch. All rights reserved.</td>
		</tr>
	</table>
</center>
</body>
</html>
