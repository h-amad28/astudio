<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProjectUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    /**
     * @api {get} /project list
     * @apiVersion 0.1.0
     * @apiName list
     * @apiGroup Project
     *
     * @apiDescription This API is used to load projects.
     * 
     * @apiHeader {String} Authorization Bearer Token
     *
     * @apiParam {String} [attribute_name] filter projects using this name
     * @apiParam {String} [attribute_value] filter projects, using this value
     * 

     * @apiSuccess {Object[]} projects list of prjects
     * @apiSuccess {Number} projects.id id of the project
     * @apiSuccess {String} projects.name name of the project
     * @apiSuccess {String} projects.status status of the project
     * @apiSuccess {Object[]} projects.attributes list of attributes attached to project
     * @apiSuccess {String} projects.attributes.name attribute's name
     * @apiSuccess {String} projects.attributes.type attribute's type
     * @apiSuccess {String} projects.attributes.value attribute value's stored against project
     * 
     * @apiSuccessExample {json} Success-Response:
     *  [{"id":1,"name":"project1","status":"pending","attributes":[{"value":"Technologies","type":"text","name":"destinations"},{"value":"second","type":"text","name":"destination"}]}]
     *
     */
    public function index(Request $request)
    {
        $projectIds = [];
        if (!empty($request->all())) {
            $projectList = Project::filterByAttributes($request->all());
            $projectIds = $projectList->pluck('id')->toArray();
            if (empty($projectIds)) {
                return response()->json([], 204);
            }
        }
        $projectList = Project::with('attributes');
        if (!empty($projectIds)) {
            $projectList->whereIn('id', $projectIds);
        }
        $projectList = $projectList->get();

        $projectList->each(function($project){
            $project->attributes->each(function($attribute){
                $attribute->makeHidden('pivot');
            });
        });
        return response()->json($projectList, 200);
    }

    /**
     * @api {post} /project/store Save
     * @apiVersion 0.1.0
     * @apiName Save
     * @apiGroup Project
     *
     * @apiDescription This API is used to create project.
     * 
     * @apiHeader {String} Authorization Bearer Token
     * 
     * @apiParam {String} name Name of the project
     * @apiParam {String} status Status of the project <ol><li>pending</li><li>in-progress</li><li>completed</li><li>on-hold</li></ol>
     * @apiParam {Object[]} attributes Array of attibute objects
     * @apiParam {Number} attributes.id id of attribute
     * @apiParam {String} attributes.value value of attribute
     * 
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
     * {"name":"Project Name", "status":"project status", "attributes":[{"id":1,"value":"test"}]}
     */
    public function store(Request $request, Project $project)
    {
        $attributeList = Attribute::loadCache();
        $attribute_ids = array_column($attributeList, 'id');

        $validator = Validator::make($request->all(),[
            'name' => ['required','max:150'],
            'status' => ['required', Rule::in(Project::AVAILABLE_TYPES)],
            'attributes' => ['required', 'array'],
            'attributes.*.id' => ['required', 'integer', Rule::in($attribute_ids)],
            'attributes.*.value' => ['required', 'max:255'],
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->getMessages();
            return response(['success' => false, 'error' => $errors], 400);
        }

        $project->fill($request->all());
        $saved = $project->save();
        if ($saved) {
            $attributes = [];
            foreach ($request->get('attributes') as $attribute) {
                array_push($attributes, [
                    'project_id' => $project->id,
                    'attribute_id' => $attribute['id'],
                    'value' => $attribute['value'],
                ]);
            }
            if (!empty($attributes)) {
                $attributes = AttributeValue::mapAttributeValues($attributes);
                // Due to unique
                AttributeValue::upsert(
                    $attributes,
                    ['project_id', 'attribute_id'],
                    ['value']
                );
            }
        }
        return response(['success' => true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $Project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $Project)
    {
        //
    }

    /**
     * @api {put} /project/user Register User
     * @apiVersion 0.1.0
     * @apiName Register User
     * @apiGroup Project
     *
     * @apiDescription This API is used to associate user to project.
     * 
     * @apiHeader {String} Authorization Bearer Token
     * 
     * @apiParam {Number} project_id project id to which user is going to associated
     * @apiParam {Number} user_id user registering for project
     * 
     * @apiSuccess {Boolean} success true in case of success
     *
     * @apiParamExample Parameter Payload:
     * {"project_id":"1", "user_id":"1"}
     */
    public function assignUser(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'project_id' => ['required','integer'],
            'user_id' => ['required','integer'],
        ]);
        if ($validator->fails()) {
            return response(['success' => false, 'error' => $validator->errors()], 422);
        }

        $saved = false;
        try {
            $projectUser = new ProjectUsers();
            $projectUser->fill($request->all());

            $saved = ProjectUsers::upsert(
                $projectUser->toArray(),
                ['project_id', 'user_id'],
                ['updated_at']
            );
        } catch (\Exception $e) {
            \Log::info('Assigning Project to user');
            \Log::info($e->getMessage());
        }
        
        if ($saved) {
            return response(['success' => true], 200);
        }
        return response(['success' => false], 204);
    }
}
