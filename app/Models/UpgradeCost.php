<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpgradeCost extends Model
{
    protected $fillable = [
        'upgrade_id',
        'resource_type_id',
        'quantity_required'
    ];

    public function upgrade()
    {
        return $this->belongsTo(Upgrade::class);
    }

    public function resourceType()
    {
        return $this->belongsTo(ResourceType::class);
    }
}