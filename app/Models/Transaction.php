<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'order_id',
        'payment_method_id',
        'status',
        'external_reference'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

}
