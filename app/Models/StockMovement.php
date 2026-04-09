<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'produc_id', 'from_warehouse_id', 'to_warehouse_id', 'quantity', 'movement_type', 'reference_number', 'note'])]
class StockMovement extends Model
{
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    public function fromWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }
    
    public function toWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
