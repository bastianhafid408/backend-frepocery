<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory, softDeletes;

    protected $fillable = [
        'name', 'description','price','rate','type','picturePath'
    ];
    // mengubah field yang ada di db dan dikeluarkan sesuai keinginan (Accessors)
    public function getCreatedAttribute($value){
        return Carbon::parse($value)->timestamp; //epoch
    }

    public function getUpdatedAttribute($value){
        return Carbon::parse($value)->timestamp; //epoch
    }

    //laravel Accessors tidak terbaca, sehingga harus mengkonversi picture path terlehih dahulu
    public function toArray()
    {
        $toArray = parent::toArray();
        $toArray['picturePath'] = $this -> picturePath;
        return $toArray;
    }
    
    //ambil data picturePath lewat url
    public function getPicturePathAttribute()
    {
        return url('').Storage::url($this->attributes['picturePath']);
    }


}
