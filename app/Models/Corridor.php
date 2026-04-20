<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Corridor extends Model
{
    protected $fillable = [
        'name',
        'description',
        'logo_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    // Relaciones (se agregarán en tarea 6.4)
    // public function currencyPairs() { ... }

    // Métodos de utilidad
    public function activate()
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    public function toggleStatus()
    {
        $this->update(['is_active' => !$this->is_active]);
    }
}
