@extends('layouts.app')
@section('title', 'Send Notification')


@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                {{-- <h3 class="text-primary">{{ucfirst($type)}} Offer</h3> --}}
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('notifications')}}">Notifications</a></li>
                    <li class="breadcrumb-item active">{{ucfirst($type)}} Notification</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->

            <div class="row">
                <div class="col-lg-12">
                    @if(session("success"))
                        @alert(["type" => "alert-success"])
                            {{ session("success") }}
                        @endalert
                    @elseif(session("danger"))
                        @alert(["type" => "alert-danger"])
                            {{ session("danger") }}
                        @endalert
                    @endif

                    <div class="card">
                        <div class="card-body">

                            @if($type == 'add')
                                <h4>Add Notification Details</h4>
                            @elseif($type == 'edit')
                                <h4>Edit Notification Details</h4>
                            @endif
                            <hr>
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form  class="form-material m-t-40 row form-valide" method="post" action="{{$url}}" enctype="multipart/form-data">
                                {{csrf_field()}}
                                <div class="form-group col-md-6 m-t-20">
                                    <label>Image (If any)</label>
                                    <input type="hidden" name="image_exists" id="image_exists" value="1">

                                    <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                        <div class="form-control" data-trigger="fileinput"> <i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div> <span class="input-group-addon btn btn-default btn-file"> <span class="fileinput-new">Select file(Allowed Extensions -  .jpg, .jpeg, .png, .gif, .svg)</span> <span class="fileinput-exists">Change</span>
                                        <input type="file" name="noti_image"> </span> <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                    </div>
                                </div>
                                <div class="col-md-12 px-0">
                                    <div class="form-group col-md-12 m-t-20">
                                        <label class="control-label">Notification Title</label><sup class="text-reddit"> *</sup>
                                        <input type="text" id="noti_title" name="noti_title" class="form-control" value="{{old('noti_title')}}">
                                    </div>
                                </div>
                                <div class="col-md-12 px-0">
                                    <div class="form-group col-md-12 m-t-20">
                                        <label class="control-label">Description</label><sup class="text-reddit"> *</sup>
                                            <textarea name="noti_description" class="form-control" rows="5">{{old('noti_title')}}</textarea>
                                    </div>
                                </div>
                                <div class="col-12 m-t-20">
                                    <button type="submit" class="btn btn-success waves-effect waves-light m-r-10">Send</button>
                                    <a href="{{route('notifications')}}" class="btn btn-inverse waves-effect waves-light">Cancel</a>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        <!-- Row -->
        </form>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(function(){
            $('input[name=noti_image]').change(function() {
                $(this).valid();
            });
        })
    </script>
@endpush