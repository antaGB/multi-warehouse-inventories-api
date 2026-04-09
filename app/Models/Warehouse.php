<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'location_address'])]
class Warehouse extends Model
{
    use HasUuids;

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }
    
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'inventories')
                    ->withPivot('id', 'quantity')
                    ->as('inventory');
    }
    
    public function stockMovementsFrom(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'from_warehouse_id');
    }
    
    public function stockMovementsTo(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'to_warehouse_id');
    }
    
    public function allStockMovements()
    {
        return StockMovement::where('from_warehouse_id', $this->id)
                           ->orWhere('to_warehouse_id', $this->id);
    }
}
