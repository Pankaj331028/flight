<?php

namespace App\Http\Controllers;

use Image;
use App\Model\Product;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function cropImage($type)
    {
        // Thumbnails
        switch ($type) {
            case 'product':
                $documents = Product::get();
                $path = 'uploads/products/';
            break;
            case 'category':
                $documents = Category::get();
                $path = 'uploads/categories/';
            break;
        }
        foreach($documents as $document){
            $fullPath = public_path($path.$document->image);
            //small
            $thumbnail_name = 'small-'.$document->image;
            $img = Image::make($fullPath)->resize(150, 100, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save(public_path($path.'small/'.$thumbnail_name));

            // medium 
            $thumbnail_name = 'medium-'.$document->image;
            $img = Image::make($fullPath)->resize(300, 185, function ($constraint) {
                $constraint->aspectRatio();
            });

            $img->save(public_path($path.'medium/'.$thumbnail_name));
        }
        dd('completed');
    }
}