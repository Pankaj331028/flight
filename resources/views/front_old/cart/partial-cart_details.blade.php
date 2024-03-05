<div class="container borderTop1 mt-sm-5 mt-3 pt-sm-5 pt-3 px-md-0 cart_details">
    <div class="font20 fontSemiBold color20 mb-4">Price Details</div>
    <div class="row">
        <div class="col-md-12">
            <div class="pirceDetails">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="font16 color20">Total Amount</span>
                    <span class="font16 color20"><span>{{ $currency }}</span><span class="total">{{ $total_amount }}</span></span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="font16 color20">Shipping Charge</span>
                    <span class="font16 color20"><span>{{ $currency }}</span><span class="ship_fee">{{ $shipping_fee }}</span></span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="font16 color20">Coupon Discount</span>
                    <span class="font16 color20">-<span>{{ $currency }}</span><span class="discount">{{ $coupon_discount }}</span></span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="font16 color20">omrbranch Credits</span>
                    <span class="font16 color20">-<span>{{ $currency }}</span><span class="goCredits">{{ $credits_used }}</span></span>
                </div>
                <div
                    class="d-flex justify-content-between align-items-center mb-2 borderTop2 borderBottom2 py-2">
                    <span class="font20 colorTheme fontSemiBold">Grand Total</span>
                    <span class="font20 colorTheme fontSemiBold"><span>{{ $currency }}</span><span class="grandTotal">{{ $grand_total }}</span></span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <div
                        class="totalSaving d-flex align-items-center border1 rounded justify-content-between px-4 py-2 mt-4">
                        <span class="font20 fontSemiBold colorTheme">Total Savings</span>
                        <span class="font20 fontSemiBold colorTheme"><span>{{ $currency }}</span>{{ $savings }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
