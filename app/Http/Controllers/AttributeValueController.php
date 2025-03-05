<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AttributeValueController extends Controller
{
    /**
     * @api {post} /attributes/store Save
     * @apiVersion 0.1.0
     * @apiName Save
     * @apiGroup Attributes
     *
     * @apiDescription This API is used to create attribute values.
     *
     * @apiHeader {String} Authorization Bearer Token
     *
     * 
     * @apiParam {Number} attribute_id ID of the attribute
     * @apiParam {Number} project_id project id, in which the attribute is stored
     * @apiParam {String} value value of the attribute regarding project

     * @apiSuccess {Boolean} success true in case of success
     *
     * @apiError {Boolean} success false in case of error
     * @apiError {Object} error list of required parameters
     * @apiError {Object} error.attribute_id attribute id is required, should be integer and should be one of attributes
     * @apiError {Object} error.project_id project id is required and should be integer
     * @apiError {Object} error.value value is required

     * @apiParamExample Parameter Payload:
     * {"attribute_id":"","project_id":"","value":""}
     */
    public function store(Request $request, AttributeValue $attributeValueEntity)
    {
        $attributeList = Attribute::loadCache();
        $attribute_ids = array_column($attributeList, 'id');
        
        $validator = Validator::make($request->all(),[
            'attribute_id' => ['required','integer', Rule::in($attribute_ids)],
            'project_id' => 'required|integer',
            'value' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->getMessages();
            return response(['success' => false, 'error' => $errors], 400);
        }

        $attributeValueEntity->fill($request->all());
        // $saved = $attributeValueEntity->save();
        $saved = AttributeValue::upsert(
            $attributeValueEntity->toArray(),
            ['project_id', 'attribute_id'],
            ['value']
        );
        if ($saved) {
            return response(['success' => true], 200);
        }
        return response(['success' => false], 400);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AttributeValue  $attributeValue
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AttributeValue $attributeValue)
    {
        //
    }
}
