<!-- Reschedule/ cancel Modal  --> 
<div class="modal fade" id="editReschedule" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal_header border-bottom-0">
          
          <button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
            <img src="{{ asset('/front/images/close-icon.png') }}">
          </button>
        </div>
        <form class="modal_form" id="reschedule">
          <input type="hidden" name="date" class="date">
          <input type="hidden" name="item_id" class="item_id">
          <input type="hidden" name="order_id" class="order_id">
          <input type="hidden" name="user_id" value="{{ $auth_user->id }}">
          <div class="modal-body">
             <div class="custom-control custom-radio modal_input">
                  <input type="radio" id="customRadioRe2" name="set" value="date" class="custom-control-input">
                  <label class="custom-control-label font14 label_weight_text color20" for="customRadioRe2">
                      Reschedule</label>
              </div>
              <p class="font18 fontSemiBold pl-4">Select a new delivery date to reshedule</p>
              <div>
                   <input type="text" name="new_date" class="defaultDatepicker re-date form-control colorb7 font16 height50" placeholder="Select Date">
              </div>
              <div class="custom-control custom-radio modal_input">
                  <input type="radio" id="customRadioRe1" name="set" value="cancel" class="custom-control-input">
                  <label class="custom-control-label font14 label_weight_text color20" for="customRadioRe1">Cancel</label>
              </div>
              <p class="font18 fontSemiBold pl-4">Don’t want this product for today</p>
          </div>
          <div class="modal-footer border-top-0 d-flex justify-content-center">
            <button type="button" class="updateSchedule font25 fontSemiBold colorWhite bgTheme radius50 borderNone px-5 py-2 hover1">Apply</button>
          </div>
        </form>
      </div>
    </div>
</div>
<!-- Reschedule/ cancel Modal   -->