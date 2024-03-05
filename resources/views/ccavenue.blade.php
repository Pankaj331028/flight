<html>

<head>
    <script>
        window.onload = function() {
		var d = new Date().getTime();
		document.getElementById("tid").value = d;
	};
</script>
</head>

<body>
    <form method="POST" name="customerData" action="{{URL::to('/ccavRequestHandler')}}">
        {{csrf_field()}}
        <input type="hidden" name="tid" id="tid" readonly />
        <input type="hidden" name="merchant_id" value="{{$settings['ccavenue_merchant_id']}}" />
        <input type="hidden" name="order_id" value="{{$order_id}}" />
        <input type="hidden" name="currency" value="INR" />
        <input type="hidden" name="amount" value="{{$amount}}" />
        <input type="hidden" name="redirect_url" value="{{URL::to('/payment-success')}}" />
        <input type="hidden" name="cancel_url" value="{{URL::to('/payment-failure')}}" />
        <input type="hidden" name="language" value="EN" />
        <input type="hidden" name="user_id" value="{{$user->id}}">
        <input type="hidden" name="billing_name" value="{{$user->first_name." ".$user->last_name}}">
        <input type="hidden" name="billing_tel" value="{{$user->mobile_number}}">
        <input type="hidden" name="billing_email" value="{{$user->email}}">
        <input type="hidden" name="custom_code" value="{{$transaction->transaction_code}}">
        </table>
    </form>
</body>
<!-- <script language="javascript" type="text/javascript" src="json.js"></script>-->
<!-- <script src="jquery-1.7.2.min.js"></script>-->
<script language="javascript" type="text/javascript" src="{{URL::asset('/public/js/json.js')}}"></script>
<script src="{{URL::asset('/public/js/jquery.min.js')}}"></script>
<script type="text/javascript">
$(function() {
    document.customerData.submit();
});
</script>

</html>