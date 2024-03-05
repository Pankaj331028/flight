@extends('front.layouts.master')
@section('template_title','Notifications')
@section('content')
    <div class="wrappernotification1 my-5"> 
        <div class="container">
            <div class="font35 font-weight-bold color11 text-center pageTitle">Notifications</div>
            @if($notifications->isNotEmpty())
            @foreach ($notifications as $key => $notification)
            <div class="font20 colorTheme font-weight-bold my-md-4 my-3">{{ $key }}</div>
                @foreach ($notification as $noty)
                <div class="notificationBlock">
                    <div class="row notificationBar bgf7 d-flex align-items-md-center py-4 px-4 position-relative mb-3">
                        <div class="col-md-2"> <img src="{{ $noty->image }}" alt=""></div>
                        <div class="col-md-8">
                            <div class="d-flex align-items-center notiHead justify-content-between">
                                <p class="font20 color20 font-weight-bold mb-2">{{ $noty->title }}</p>
                                <p class="font16 color36 mb-2">{{ $noty->time }}</p>
                            </div>
                            <p class="font18 color36 mb-0">{{ $noty->description }}</p>
                        </div>
                        <div class="closenoti">
                            <a href="javascript:;"><img src="{{ asset('/front/images/cross.png') }}"></a>
                        </div>
                    </div>
                </div>
                @endforeach
            @endforeach
            @else
                <h5 class="text-center mt-5">No new notification</h5>
            @endif
        </div>
    </div>
@endsection