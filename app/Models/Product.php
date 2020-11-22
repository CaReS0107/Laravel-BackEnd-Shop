<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'description',
        'user_id',
    ];

    protected $guarded = 'id';
    protected $hidden = ['user_id', 'id', 'comment_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function categories(){
        return $this->belongsToMany(Category::class,'category_product','product_id','category_id');
    }
//    public function categories()
//    {
//        return $this->morphedByMany(Category::class, 'category_product');
//    }
}
