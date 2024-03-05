@extends('front.layouts.master')
@section('template_title')
    {{ isset($id) ? 'Sub Category' : 'Categories' }}
@endsection
@section('content')
    <section class="wrapper5 topOffers py-3 py-md-5">
        <div class="container">
            <div class="sectionTitleBlock d-flex mb-5">
                <h5 class="sectionTitle font35 font-weight-bold color11"> {{ isset($id) ? 'Sub Category' : 'Categories' }}</h5>
            </div>
            <div class="row">
                @forelse ($categories as $category)
                    <div class="col-md-6 mb-4">
                        <?php
                        if($category->subcategory_count>0){
                            $url = url('category/groceries/'.$category->id);
                        }else{
                            $url = url('category/groceries/product/'. $category->id);
                        }
                        ?>
                        <a class=" d-flex align-items-center" href="{{ $url }}">
                            <div class="categoryBlock bgWhite">
                                <div class="categoryImg mr-4">
                                    <img src="{{ $category->image }}" alt="" width="100">
                                </div>
                                <div class="ctaegoryDetail">
                                    <p class="font20 fontsemibold color20 mb-1">{{ $category->name }}</p>
                                    <p class="font16 color36">{{ $category->description }}</p>
                                </div>
                                <div class="categoryClickArrow">
                                    <a href="{{ $url }}"><i class="allinone categoryarrow"></i></a>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                <div class="d-flex mx-auto my-5">
                    <span class="font20">No categories found !</span>
                </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection