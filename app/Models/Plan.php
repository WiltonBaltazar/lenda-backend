<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug', 
        'price',
        'bg_color',
        'text_color',
        'description',
        'duration_days',
    ];

    protected $appends = ['formatted_price'];
    
    protected $casts = [
        'id' => 'integer',
        'price' => 'decimal:2'
    ];

    public function getFormattedPriceAttribute(): string
{
    return number_format($this->price, 2, '.', ',');
}



    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
    
    //Check if the plan is valid
    public function isValid(): bool
    {
        return $this->duration_days > 0 && $this->price > 0;    
    }
}
