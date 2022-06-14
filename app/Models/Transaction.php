<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'product_id','quantity','total','status','payment_url'
    ];

    //relasi product
    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    //relasi user
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

     // mengubah field yang ada di db dan dikeluarkan sesuai keinginan (Accessors)
     public function getCreatedAttribute($value){
        return Carbon::parse($value)->timestamp; //epoch
    }

    public function getUpdatedAttribute($value){
        return Carbon::parse($value)->timestamp; //epoch
    }
}
