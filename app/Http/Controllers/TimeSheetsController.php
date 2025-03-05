<?php

namespace App\Http\Controllers;

use App\Models\TimeSheets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TimeSheetsController extends Controller
{
    /**
     * @api {post} /time-sheet List
     * @apiVersion 0.1.0
     * @apiName List
     * @apiGroup TimeSheet
     *
     * @apiDescription This API is used to load user timesheet.
     * 
     * @apiHeader {String} Authorization Bearer Token
     * 
     * @apiParam {String} [task_name]
     * @apiParam {String} [date] 
     * @apiParam {Number} [project_id]
     * 
     * @apiSuccess {Object[]} timeSheet
     * @apiSuccess {Number} timeSheet.id
     * @apiSuccess {String} timeSheet.task_name
     * @apiSuccess {Number} timeSheet.project_id
     * @apiSuccess {Number} timeSheet.user_id
     * @apiSuccess {Number} timeSheet.minutes
     * @apiSuccess {String} timeSheet.hours
     * 
     * @apiParamExample {String} Parameter Payload:
     *  ?task_name=&date=&project_id=
     * @apiSuccessExample {json} Success-Response:
     *  [{"id":1,"task_name":"testingTask","date":"2025-01-01","minutes":120,"user_id":2,"project_id":1,"hours":"2.00"}]
     *
     */
    public function index(Request $request)
    {
        $timeSheet = TimeSheets::where('user_id', auth()->user()->id);

        if ($projectId = $request->get('project_id')) {
            $timeSheet->where('project_id', $projectId);
        }

        if ($task_name = $request->get('task_name')) {
            $timeSheet->where('task_name', $task_name);
        }

        if ($date = $request->get('date')) {
            $timeSheet->where('date', $date);
        }
        $result = $timeSheet->get();
        return response()->json($result, count($result) ? 200 : 204);
    }

    /**
     * @api {post} /time-sheet/store Save
     * @apiVersion 0.1.0
     * @apiName Save
     * @apiGroup TimeSheet
     *
     * @apiDescription This API is used to create timesheet.
     * 
     * @apiHeader {String} Authorization Bearer Token
     * 
     * @apiParam {String} task_name Name of the task
     * @apiParam {String} date 
     * @apiParam {Number} minutes 
     * @apiParam {Number} project_id 
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
     * {"task_name":"testingTask", "date":"2025-01-01", "minutes":"100", "project_id"}
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'project_id' => ['required','integer'],
            'minutes' => ['required','integer'],
            'date' => ['required','date'],
            'task_name' => ['required','string'],
        ]);
        if ($validator->fails()) {
            return response(['success' => false, 'error' => $validator->errors()], 422);
        }

        $timeSheetObj = new TimeSheets();
        $timeSheetObj->fill($request->all());
        $timeSheetObj->user_id = auth()->user()->id;
        $timeData = $timeSheetObj->toArray();
        unset($timeData['hours']);
        $saved = TimeSheets::upsert(
            $timeData,
            ['project_id', 'user_id'],
            ['minutes', 'updated_at']
        );
        if ($saved) {
            return response()->json(['success' => true], 200);
        }
        return response()->json(['success' => false], 400);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TimeSheets  $timeSheets
     * @return \Illuminate\Http\Response
     */
    public function show(TimeSheets $timeSheets)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TimeSheets  $timeSheets
     * @return \Illuminate\Http\Response
     */
    public function edit(TimeSheets $timeSheets)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TimeSheets  $timeSheets
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TimeSheets $timeSheets)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TimeSheets  $timeSheets
     * @return \Illuminate\Http\Response
     */
    public function destroy(TimeSheets $timeSheets)
    {
        //
    }
}
