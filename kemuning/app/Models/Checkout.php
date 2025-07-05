<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Checkout extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['customer_id', 'track_id', 'item_id', 'qty', 'note', 'discount'];
    protected $dates = ['deleted_at'];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
