<!--- Social share modal -->
<div class="modal fade" id="socialShare" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal_header border-bottom-0">
          <h5 class="modal-title text-center font20 font-weight-bold modal_title_rel" id="exampleModalLabel">
          <span>Share link</span>
          </h5>
          <button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
            <img src="{{ asset('/front/images/close-icon.png') }}">
          </button>
        </div>
          <div class="modal-body">
            <div class="d-flex row my-5 ml-2" style="justify-content:space-evenly">
                    <div class="col-md-2 social-buttons">
                      <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($url) }}"
                         target="_blank">
                         <i class="fa fa-facebook-official fa-2x"></i>
                      </a>
                    </div>
                    <div class="col-md-2 social-buttons">
                      <a href="https://plus.google.com/share?url={{ urlencode($url) }}"
                      target="_blank">
                      <i class="fa fa-google-plus-square fa-2x"></i>
                    </a>
                    </div>
                    <div class="col-md-2 social-buttons">
                      <a href="https://twitter.com/intent/tweet?url={{ urlencode($url) }}"
                         target="_blank">
                          <i class="fa fa-twitter-square fa-2x"></i>
                      </a>
                    </div>
                    <div class="col-md-2 social-buttons">
                      @if(Request::segment(1)=='product')
                        <a href="mailto:?subject=omrbranch&body={{ ($product) ? $product->name. ' '.$product->image : '' }}"> <i class="fa fa-envelope fa-2x"></i>
                        </a>
                      @elseif(Request::segment(1)!='product' && $auth_user)
                      <a href="mailto:?subject=omrbranch&body=Register on GoGrocery with {{ urlencode($auth_user->referral_code) }} and earn Rs.{{ \App\Library\Notify::getBusRuleRef('referrer_amount') }}. Download on Android {{ \App\Library\Notify::getBusRuleRef('ios_url_user')}} and Download on iOS {{ \App\Library\Notify::getBusRuleRef('ios_url_user') }}">
                         <i class="fa fa-envelope fa-2x"></i>
                      </a>
                      @endif
                    </div>
              </button>
          </div>
          </div>
      </div>
    </div>
</div>
