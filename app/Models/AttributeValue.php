<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Attribute;

class AttributeValue extends Model
{
    protected $table = 'attribute_values';

    private int $attribute_id;
    private int $project_id;
    private string $value;

    protected $fillable = [
        'attribute_id',
        'project_id',
        'value'
    ];

    public static function mapAttributeValues(array $data = []) : array {
        if (empty($data)) {
            return [];
        }

        $attributesData = Attribute::loadCache();
        foreach ($data as $key => &$value) {
            $attribute = $attributesData[$value['attribute_id']];
            switch ($attribute['type']) {
                case 'text':
                case 'select':
                    settype($value['value'], 'string');
                    break;
                
                    case 'number':
                    settype($value['value'], 'int');
                    break;
                
                case 'date':
                    $dateTime = \DateTime::createFromFormat('Y-m-d', $value['value']);
                    $value['value'] = $dateTime ? $dateTime->format('Y-m-d') : "";
                    break;
            }
        }
        return $data;
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'attribute_values', 'project_id', 'attribute_id');
    }
}
