<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'price', 'type'];
    protected $dates = ['deleted_at'];

    public function checkout()
    {
        return $this->hasMany(Checkout::class);
    }
}
