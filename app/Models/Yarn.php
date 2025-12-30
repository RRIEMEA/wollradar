<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Yarn extends Model
    {
        protected $fillable = [
        'user_id',
        'project_id',
        'color_id',
        'material_id',
        'brand_id',
        'location_id',
        'name',
        'color_code',
        'batch_number',
        'needle_size',
        'quantity',
        'length_m',
        'weight_g',
        'notes',
        'photo_path'
    ];


    protected $casts = [
        'quantity' => 'decimal:2',
        'length_m' => 'decimal:2',
        'weight_g' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
