<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    use HasFactory;


    public function products(){
        $this->belongsToMany(Product::class, 'category-product', 'category_id','product_id');
    }
//    public function products()
//    {
//        return $this->morphedByMany(Product::class, 'category_product');
//    }
}
