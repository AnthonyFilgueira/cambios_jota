<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class Module extends Model
{
    protected $fillable = [
        'name', 'slug', 'icon', 'description', 'order', 'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function permissions()
    {
        return Permission::where('module_id', $this->id)->orderBy('name')->get();
    }
}
