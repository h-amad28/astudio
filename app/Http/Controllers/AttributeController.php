<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AttributeController extends Controller
{
    /**
     * @api {get} /attributes list
     * @apiVersion 0.1.0
     * @apiName list
     * @apiGroup Attributes
     *
     * @apiDescription This API is used to fetch the list of attribute.
     *
     * @apiHeader {String} Authorization Bearer Token
     *
     * @apiSuccess {Object[]} attribute list of attributes
     * @apiSuccess {Number} attribute.id ID of the attribute
     * @apiSuccess {String} attribute.name Name of the attribute
     * @apiSuccess {String} attribute.type Type of the attribute
     *
     * @apiSuccessExample {json} Success-Response:
     *  [{"id":1,"name":"destinations","type":"text"}]
     */
    public function index()
    {
        $attributesList = Attribute::loadCache();
        return response()->json(array_values($attributesList), 200);
    }

    
    /**
     * @api {post} /attributes/store Save
     * @apiVersion 0.1.0
     * @apiName Save
     * @apiGroup Attributes
     *
     * @apiDescription This API is used to create attribute.
     *
     * @apiHeader {String} Authorization Bearer Token
     * 
     * @apiParam {String} name Name of the attribute
     * @apiParam {String} type Status of the project <ol><li>text</li><li>date</li><li>number</li><li>select</li></ol>
     *

     * @apiSuccess {Boolean} success true in case of success
     *

     * @apiParamExample Parameter Payload:
     * {"name":"Attribute label", "type":"type for attribute"}
     */
    public function store(Request $request, Attribute $attributeEntity)
    {
        $attributeEntity->fill($request->all());
        $saved = $attributeEntity->save();
        if ($saved) {
            cache()->forget(Attribute::CACHE_KEY);
            return response(['success' => true], 200);
        }
        return response(['success' => false], 400);
    }
    
    /**
     * @api {put} /attributes/:id Update
     * @apiVersion 0.1.0
     * @apiName Update
     * @apiGroup Attributes
     *
     * @apiDescription This API is used to update attribute.
     *
     * @apiHeader {String} Authorization Bearer Token
     * 
     * @apiParam {Number} id id, primary key, of the attribute
     * @apiParam {String} name Name of the attribute
     * @apiParam {String} type Status of the project <ol><li>text</li><li>date</li><li>number</li><li>select</li></ol>

     * @apiSuccess {Boolean} success true in case of success
     * @apiSuccess {Object} attribute attribute entity will return
     * @apiSuccess {Number} attribute.id id of the attribute
     * @apiSuccess {String} attribute.name name of the attribute
     * @apiSuccess {String} attribute.type type of the attribute
     *
     * @apiError {Boolean} success false in case of error
     * @apiError {Object} error list of required parameters
     * @apiError {Object} error.attribute_id attribute id is required, should be integer and should be one of attributes
     * @apiError {Object} error.type type is required
     *

     * @apiParamExample Parameter Payload:
     * {"name":"Attribute label", "type":"type for attribute"}
     */
    public function update(Request $request, $attributeId)
    {
        $request->merge(['id' => $attributeId]);
        $attributeList = Attribute::loadCache();
        $attribute_ids = array_column($attributeList, 'id');

        $validator = Validator::make($request->all(),[
            'id' => ['required','integer', Rule::in($attribute_ids)],
            'type' => ['required', Rule::in(Attribute::AVAILABLE_TYPES)],
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->getMessages();
            return response(['success' => false, 'error' => $errors], 400);
        }

        $attributeObj = Attribute::find($request->get('id'));
        if (!empty($attributeObj->id)) {
            $attributeObj->type = $request->get('type');
            $attributeObj->name = $request->get('name');
            $attributeObj->save();
            return response(['success' => true, 'attribute' => $attributeObj], 200);
        }
        return response(['success' => false], 400);
    }
}
