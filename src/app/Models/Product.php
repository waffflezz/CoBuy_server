<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    const NONE_STATUS = 0;
    const BUY_STATUS = 1;
    const PLANNED_STATUS = 2;

    protected $fillable = [
        'name',
        'description',
        'status',
        'shopping_list_id',
        'image',
        'price',
        'user_id',
        'count'
    ];

    public function shoppingList(): BelongsTo
    {
        return $this->belongsTo(ShoppingList::class);
    }
}
