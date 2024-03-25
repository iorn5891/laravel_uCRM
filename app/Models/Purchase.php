<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Models\Item;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'status'
    ];

    
    public function customer()
    {
        // 1対1の関係
        return $this->belongsTo(Customer::class);
    }
    
    public function items()
    {
        // 1対多の関係
        return $this->belongsToMany(Item::class)
        ->withPivot('quantity'); //中間テーブル情報
    }
}
