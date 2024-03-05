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
                    <img src="{{ asset('/front/images/logo.png') }}" width="40%" height="auto">
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
			<td style="text-align:left; padding:10px 0px 20px 20px; " bgcolor="#f9f9f9">
				<div style="margin:20px auto 10px; font-size:18px;"><h4>New Account</h4></div>
				<div style="margin:20px auto 10px; font-size:28px;">Hello {{$first_name.' '.$last_name}}, </div>
				<div style="margin:20px auto 10px; font-size:15px;">omrbranch Admin has created a driver account using your email id: {{$email}}. Please install {{ucfirst($role)}} app and login with the following credentials:<br><br><strong>Email-ID: </strong>{{$email}}<br><strong>Password: </strong>{{$password}}</div>
				<div style="margin:20px auto 10px; font-size:15px;">You can change your password anytime from app.</div>
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
			<td style="text-align:center; padding:20px 10px 20px; font-size:15px; color:#666;">Copyright @ {{ date('Y') }} omrbranch. All rights reserved.</td>
		</tr>
	</table>
</center>
</body>
</html>
