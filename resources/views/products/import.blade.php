@extends('layouts.app')
@section('title', 'Products')

@section('content')
	<div class="content-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Products</h3> </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('products')}}">Products</a></li>
                    <li class="breadcrumb-item active">Bulk Import</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('layouts.message')
                    <div class="card-body">
                        <h4 class="card-title">Import</h4>

                        <form class="form-material m-t-50 row form-valide" method="post" action="{{$url}}" enctype="multipart/form-data">

                            {{ csrf_field() }}
                            <div class="sampleSheet col-12">
                                <div class="dt-buttons">
                                    <a href="/download/Sample_Product_File.xlsx" class="btn dt-button py-2" download>Download Sample File</a>
                                </div>
                                <div class="variantRow">
                                    <h6>Points to Remember:</h6>
                                    <p>
                                        <ol>
                                            <li>Please download the sample excel sheet from the button above before importing the data.</li>
                                            <li>If any product has more than one variation, then add them in new row leaving all the product information except the variants blank.</li>
                                            <li>In product description, you can add any type of data: HTML or plain text.</li>
                                            <li>Please make sure you upload images in the specified folder while importing data to show the data properly in the mobile application.<br><center><b>{{Url::asset('uploads/products')}}</b></center></li>
                                            <li>If units of any variation are not added in the system previously, then they will be added at the time of import.</li>
                                            <li>Do not leave any blank row between two new products.</li>
                                        </ol>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 p-0">
                                <div class="form-group col-md-6 m-t-20 float-left">
                                    <label>File</label><sup class="text-reddit"> *</sup>

                                    <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                        <div class="form-control" data-trigger="fileinput"> <i class="glyphbanner glyphbanner-file fileinput-exists"></i> <span class="fileinput-filename"></span></div> <span class="input-group-addon btn btn-default btn-file"> <span class="fileinput-new">Select file(Allowed Extensions - .xlsx)</span> <span class="fileinput-exists">Change</span>
                                        <input type="file" name="product_import"> </span> <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 m-t-20">
                                <button type="submit" class="btn btn-success submitBtn m-r-10">Upload</button>
                                <a href="{{route('products')}}" class="btn btn-inverse waves-effect waves-light">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End PAge Content -->
    </div>

@endsection

@push('scripts')

    <script type="text/javascript">
    	$(function(){

    	});
    </script>
@endpush