<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\AttributeValue;
use App\Models\Attribute;
use DB;

class Project extends Model
{
    protected $table = 'projects';
    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = [
        'name',
        'status',
    ];

    public const AVAILABLE_TYPES = [
        'pending',
        'in-progress',
        'on-hold',
        'completed',
    ];

    private int $id = 0;
    private string $name = '';
    private string $status = 'pending';

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'attribute_values', 'project_id', 'attribute_id')
            ->select(['attribute_values.value', 'attributes.type', 'attributes.name']);
    }

    public static function filterByAttributes(array $filters = [])
    {
        $projectObj = new Project();
        $attObj = new Attribute();
        $attValueObj = new AttributeValue();
        $attTable = $attObj->getTable();
        $attValTable = $attValueObj->getTable();
        $projectTable = $projectObj->getTable();

        $query = DB::table($projectTable . ' as p')
            ->join($attValTable . ' as av', 'p.id', '=', 'av.project_id')
            ->join($attTable . ' as a', 'a.id', '=', 'av.attribute_id')
            ->select([
                'p.id',
                'av.value',
                'a.id as attribute_id',
            ]);

        if (!empty($filters['attribute_name'])) {
            $query->where('a.name', $filters['attribute_name']);
        }

        if (!empty($filters['attribute_value'])) {
            $query->where('av.value', $filters['attribute_value']);
        }

        return $query->get();
    }
}
