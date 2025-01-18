<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    /** @use HasFact
     * ory<\Database\Factories\OrderDetailFactory> */
    use HasFactory;
    protected $fillable = [
        'product_id',
        'quantity',
        'price'
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
