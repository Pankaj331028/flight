<!-- Schedule Multi Modal  --> 
<div class="modal fade" id="scheduleMultipleDay" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
    <div class="modal_header border-bottom-0">
        <h5 class="modal-title text-center font24 font-weight-bold modal_title_rel" id="exampleModalLabel">
        Schedule
        </h5>
        <p class="text-center font18">Choose delivery slot for this order</p>
        <button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close"> 
        <img src="{{ asset('/front/images/close-icon.png') }}">
        </button>
    </div>
    <form class="modal_form" action="{{ url('api/setProductDateTime') }}" method="POST" id="multipleSchedule">
        <input type="hidden" class="item_id">
        <div class="modal-body">
            <form>
            <div class="row border-bottom pb-4">
                <div class="col-md-6 multipleDay">
                    <div class="form_group_input mb-md-0 mb-2">
                        <input type="text" readonly class="form-control w-100 colorb7 font16 height50 datepicker from" name="start_date" placeholder="From">
                        <i class="fa fa-angle-down" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form_group_input mb-md-0 mb-2">
                        <input type="text" readonly class="form-control w-100 colorb7 font16 height50 datepicker to" name="end_date" placeholder="To">
                        <i class="fa fa-angle-down" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                <div class="list_column mt-3">
                    <ul class="column_list_wrapper multipleTimeSlot"></ul>
                </div>
                </div>
            </div>
            </form>
        </div>
        <div class="modal-footer border-top-0 d-flex justify-content-center">
        <button class="multilpleItem font18 fontSemiBold colorWhite bgTheme radius50 borderNone px-5 py-2 hover1" data-id="" onclick="event.preventDefault();saveSchedule('multipleSchedule','multiple')">Save</button>
        </div>
    </form>
    </div>
</div>
</div>
<!-- Schedule Multi Modal End -->
<!-- Schedule Same Modal  --> 
<div class="modal fade" id="scheduleSingleDay" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
    <div class="modal_header border-bottom-0">
        <h5 class="modal-title text-center font24 font-weight-bold modal_title_rel" id="exampleModalLabel">
        Same Day
        </h5>
        <p class="text-center font18">Choose delivery slot for this order</p>
        <button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
        <img src="{{ asset('/front/images/close-icon.png') }}">
        </button>
    </div>
    <form class="modal_form" action="{{ url('api/setProductDateTime') }}" method="POST" id="singleSchedule">
        <input type="hidden" class="item_id">
        <div class="modal-body">
            <div class="row border-bottom py-3 m-0">
            <div class="singleSchedule d-flex w-100 grid_timing_wrapper flex-wrap justify-content-between"></div>
            </div>
            <div class="row">
                <div class="col-md-12">
                <div class="list_column mt-3">
                    <ul class="column_list_wrapper timeSlot"></ul>
                </div>
                </div>
            </div>
        </div>
        <div class="modal-footer border-top-0 d-flex justify-content-center">
        <button type="submit" class="singleItem font18 fontSemiBold colorWhite bgTheme radius50 borderNone px-5 py-2 hover1" data-id="" onclick="event.preventDefault();saveSchedule('singleSchedule','single')">Save</button>
        </div>
    </form>
    </div>
</div>
</div>
<!-- Schedule Same Modal End -->