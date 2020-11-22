<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $fillable = [
        'comment',
        'product_id',
        'user_id'

    ];
    protected $guarded = 'id';
    protected $hidden = ['product_id', 'user_id','id'];

public function product(){
    return $this->belongsTo(Product::class);
}
}
