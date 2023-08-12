<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as ResizeImage;

class ProductController extends Controller
{

    public function all()
    {
        return response()->json(Product::all());
    }

    public function findById($id)
    {
        return response()->json(
            Product::where('id', $id)->get()
        );
    }

    public function findByCategory($category_id)
    {
        return response()->json(
            Product::where('category_id', $category_id)->get()
        );
    }

    public function findImgById($id, $percentage = 100)
    {
        $width = 6.7 * $percentage;
        $height = 9.3 * $percentage;
        header("Content-type: image/png");
        return \Image::make(public_path("/products/$id/img.png"))->resize($width, $height)->response('png');
    }

    public function best()
    {
        return response()->json(
            Product::orderBy('sales', 'desc')->take(6)->get()
        );
    }


}
