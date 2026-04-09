<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['product_id', 'warehouse_id', 'quantity'])]
class Inventory extends Model
{
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
    
    // Get movements where this warehouse was involved
    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'product_id', 'product_id')
                    ->where(function($query) {
                        $query->where('from_warehouse_id', $this->warehouse_id)
                              ->orWhere('to_warehouse_id', $this->warehouse_id);
                    });
    }
}
