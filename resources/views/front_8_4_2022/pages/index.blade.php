@extends('front.layouts.master')
@section('template_title')
{{ $pageTitle }}
@endsection
@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/resources/demos/style.css">
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
@endpush
@php
use App\Model\Ticket;
use Illuminate\Support\Facades\Session as FacadesSession;
$auth_user = \App\model\User::find(FacadesSession::get('user')->id);
if(isset($auth_user->id)){
$data = Ticket::where('status', 0)->where('user_id', $auth_user->id)->orderBy('created_at', 'desc')->get();
$data_close = Ticket::where('status', 1)->where('user_id', $auth_user->id)->orderBy('created_at', 'desc')->get();
}else{
$data = [];
$data_close = [];
}
@endphp
<style>
   .iframe_div {
   display: none;
   }
   iframe {
   width: 100%;
   height: 600px;
   }
   li.active.tab {
   border: 1px solid;
   }
   li.tab a{
   padding: 12px;
   display: block;
   }
   li.tab {
   border: 1px solid;
   }
   li.tab.active a,a.show.active{
   color: #fff;
   background: #28a745;
   }
   /* a.mr-4.show.active {
   color: yellowgreen;
   } */
</style>
@section('content')
<section class="wrapperabout1 my-5">
   <div class="container">
      <div class="font35 font-weight-bold color11 text-center pageTitle">{{ $title }}</div>
      <img src="#" alt="No Image" width="100" height="100">
   </div>
</section>
@if($cms->slug =='faq' || $cms->slug == 'delivery-faq')
<div class="container">
   <div id="hwefaqaccordion" class="mt-5">
      @foreach($faqs as $faq)
      <div class="border-bottom pb-3 mb-3">
         <div class="panelhead">
            <a role="button" data-toggle="collapse" data-parent="#hwefaqaccordion" href="#panel-{{ $faq['id'] }}" class="font-weight-bold color20 font18 collapsed"><i class="trigger"></i>{!! $faq->question !!}</a>
         </div>
         <div id="panel-{{ $faq['id'] }}" class="collapse colorTriple5" data-parent="#hwefaqaccordion">
            <div class="pl-5 ml-2 mb-3">
               <div class="mb-3 font16 color36">{!! $faq->answer !!}</div>
            </div>
         </div>
      </div>
      @endforeach
   </div>
</div>
@else
<div class="container">
    @if(Request::path() == 'pages/customer-care')
   <div class="container">
      <div class="row">
         <div class="col-md-12">
            <div class="text-center">
               <h3> My Ticket </h3>
            </div>
            <div class="row">
               <div class="ml-auto mr-3 ui-droppable" id="droppable">
                  <button type="button" class="btn btn-primary"> Move To Close   </button>
               </div>
               <div class="mr-2" id="droppable_bin">
                  <button type="button" class="btn btn-success"> Move To Bin  </button>
               </div>
            </div>
            <div class="tabbable-panel">
               <div class="tabbable-line">
                  <ul class="nav nav-tabs mb-3">
                     <li class="tab">
                        <a href="#tab_default_1" data-toggle="tab" class="pr-4 active show"> <strong> Open Ticket </strong> </a>
                     </li>
                     <li class="tab">
                        <a href="#tab_default_2" data-toggle="tab"> <strong> Closed Ticket  </strong> </a>
                     </li>
                  </ul>
                  <div class="tab-content">
                     <div class="tab-pane active" id="tab_default_1">
                        <table class="table table-hover">
                           <thead>
                              <tr>
                                 <th scope="col"># </th>
                                 <th scope="col"> Order Id </th>
                                 <th scope="col"> Subject </th>
                              </tr>
                           </thead>
                           <tbody>
                              @if(isset($data) && count($data) > 0)
                              @foreach($data as $key => $item)
                              <tr class="draggable" id="{{$item->id}}">
                                 <th scope="row"> {{$key+1}} </th>
                                 <td>   {{$item->order_no ?? '-'}}   </td>
                                 <td> {{$item->subject ?? '-'}} </td>
                              </tr>
                              @endforeach
                              @else
                              <td>
                                 <h6> No Open Ticket </h6>
                              </td>
                              @endif
                           </tbody>
                        </table>
                     </div>
                     <div class="tab-pane" id="tab_default_2">
                        <table class="table table-hover">
                           <thead>
                              <tr>
                                 <th scope="col"># </th>
                                 <th scope="col"> Order Id </th>
                                 <th scope="col"> Subject </th>
                              </tr>
                           </thead>
                           <tbody>
                              @if(isset($data_close) && count($data_close) > 0)
                              @foreach($data_close as $key => $item_close)
                              <tr  class="draggable" id="{{$item_close->id}}">
                                 <th scope="row"> {{$key+1}} </th>
                                 <td>   {{$item_close->order_no ?? '-'}}   </td>
                                 <td> {{$item_close->subject ?? '-'}} </td>
                              </tr>
                              @endforeach
                              @else
                              <td>
                                 <h6> No Close Ticket </h6>
                              </td>
                              @endif
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <hr>
   <div class="text-center">
      <button type="button" class="btn btn-success generate_ticket_btn mb-4"> Generate Ticket </button>
   </div>
   <!-- <hr> -->
   <div class="iframe_div">
      <iframe src="{{URL::asset('ticketGenerate')}}" title="">
      </iframe>
   </div>
   @endif
   {!!$cms->content!!}
</div>


@endif
<script>
   $('document').ready(function() {
       $('.generate_ticket_btn').click(function() {
           $('.iframe_div').removeClass('iframe_div');
       });
   });

   $(function() {
       $(".draggable").draggable();
       $("#droppable").droppable({
           drop: function(event, ui) {
               var id = ui.draggable.attr("id");
               ui.draggable.hide();
               $.ajax({
                   headers: {
                       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                   },

                   type: 'post',
                   url: "{{route('closeTicket')}}",
                   data: {
                       id: id
                   },
                   dataType: "json",
                   success: function(res) {
                       if (res.status == true) {
                           toastr.success(res.message, "Status", {
                               timeOut: 5000,
                               "closeButton": true,
                               "debug": false,
                               "newestOnTop": true,
                               "positionClass": "toast-top-right",
                               "preventDuplicates": true,
                               "tapToDismiss": true
                           });

                           setTimeout(function() {
                               location.reload();
                           }, 2000);


                       } else {
                           toastr.error(res.message, "Status", {
                               timeOut: 5000,
                               "closeButton": true,
                               "debug": false,
                               "newestOnTop": true,
                               "positionClass": "toast-top-right",
                               "preventDuplicates": true,
                               "tapToDismiss": true
                           });
                       }
                   }
               });
           }
       });


       $("#droppable_bin").droppable({
           drop: function(event, ui) {
               var id = ui.draggable.attr("id");
               ui.draggable.hide();
               $.ajax({
                   headers: {
                       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                   },

                   type: 'post',
                   url: "{{route('moveBinTicket')}}",
                   data: {
                       id: id
                   },
                   dataType: "json",
                   success: function(res) {
                       if (res.status == true) {
                           toastr.success(res.message, "Status", {
                               timeOut: 5000,
                               "closeButton": true,
                               "debug": false,
                               "newestOnTop": true,
                               "positionClass": "toast-top-right",
                               "preventDuplicates": true,
                               "tapToDismiss": true
                           });

                           setTimeout(function() {
                               location.reload();
                           }, 2000);


                       } else {
                           toastr.error(res.message, "Status", {
                               timeOut: 5000,
                               "closeButton": true,
                               "debug": false,
                               "newestOnTop": true,
                               "positionClass": "toast-top-right",
                               "preventDuplicates": true,
                               "tapToDismiss": true
                           });
                       }
                   }
               });
           }
       });
   });

</script>
@endsection
