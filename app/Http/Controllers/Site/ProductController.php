<?php

namespace App\Http\Controllers\Site;

use App\Models\Attribute;
use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function productsBySlug($slug)
    {
        $data=[];
        $data['product'] = Product::where('slug',$slug) -> first();
        if (!$data['product']){
            return redirect()->back()->with(['error'=>'حدث خطأ ما برجاء المحاولة مرة اخرى']);
              }

        $product_id = $data['product'] -> id ;
        $product_categories_ids =  $data['product'] -> categories ->pluck('id');

       $data['product_attributes'] =  Attribute::whereHas('options' , function ($q) use($product_id){
            $q -> whereHas('product',function ($qq) use($product_id){
                $qq -> where('product_id',$product_id);
            });
        })->get();

         $data['related_products'] = Product::whereHas('categories',function ($cat) use($product_categories_ids){
           $cat-> whereIn('categories.id',$product_categories_ids);
       }) -> limit(20) -> latest() -> get();

        return view('front.products-details', $data);
    }

}
