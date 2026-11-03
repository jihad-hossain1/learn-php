<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use App\Models\Code;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'description',
        'slug',
        'cat_id'
    ];

    protected $hidden = ['code', 'cat_id'];


    protected $appends = ['category'];

    public function code()
    {
        return $this->belongsTo(Code::class, 'cat_id');
    }

    protected function category(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->code->name
        );
    }
}
