@extends('layouts.app')
@section('title', 'Attribute - ' . ucfirst($attribute->title))

@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                {{-- <h3 class="text-primary">{{ucfirst($type)}} Branch</h3> --}}
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('attributes')}}">Attribute</a></li>
                    <li class="breadcrumb-item active">View Attribute</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            @include('layouts.message')
            <div class="col-lg-4 col-xlg-3 col-md-5">
                <div class="card">
                    <div class="card-body">
                        @include('layouts.message')
                            <center><h4 class="card-title m-t-10 m-b-0">{{ ucfirst($attribute->name) }}</h4></center>
                        </center>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-xlg-9 col-md-7">
                <div class="card">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs profile-tab" role="tablist">
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#profile" role="tab">Details</a> </li>
                        
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="profile" role="tabpanel">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 col-xs-6 b-r"> <strong>Name</strong>
                                        <br>
                                        <p class="text-success">{{ucfirst($attribute->name)}}</p>
                                    </div>
                                    <div class="col-md-3 col-xs-6 b-r"> <strong>Type</strong>
                                        <br>
                                        <p class="text-success">{{ucfirst($attribute->type)}}</p>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-3 col-xs-6 b-r">
                                        <h5 class="p-t-20 db">Created On</h5><small class="text-success db">{{date('Y, M d', strtotime($attribute->created_at))}}</small>
                                    </div>
                                    <div class="col-md-3 col-xs-6 b-r">
                                        <h5 class="p-t-20 db">Status</h5><small class="text-success db">{{ucfirst(config('constants.STATUS.'.$attribute->status))}}</small>
                                    </div>
                                    @if(isset($AttributeVariation) && !empty($AttributeVariation))
                                        @foreach($AttributeVariation as $k=> $val)
                                            <div class="col-md-3 col-xs-6 b-r">
                                                <h5 class="p-t-20 db">Option #{{$k+1}}</h5><small class="text-success db">{{$val->value}}</small>
                                            </div>
                                         @endforeach
                                    @endif
                                </div>            
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End PAge Content -->
    </div>
@endsection


