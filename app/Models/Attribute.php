<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Attribute extends Model
{
    protected $hidden = ['created_at', 'updated_at'];

    public const AVAILABLE_TYPES = [
        'text',
        'number',
        'date',
        'select'
    ];

    public const CACHE_KEY = 'attributes_list';
    public const CACHE_TIME = 3600;// seconds

    protected $table = 'attributes';

    protected $fillable = [
        'name',
        'type',
    ];

    // ACTUALL ENTITY
    private string $name;
    private string $type;

    static public function loadCache() : array {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TIME, function() {
            return Attribute::all()
                ->map(fn($item) => [
                    'id' => $item->getAttribute('id'),
                    'name' => $item->getAttribute('name'),
                    'type' => $item->getAttribute('type')]
                )// cache only limited data, avoid size
                ->keyBy('id')
                ->toArray();
        });
    }
}
