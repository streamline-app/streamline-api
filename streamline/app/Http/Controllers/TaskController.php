<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks=\App\Task::all();
        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //TODO: Validation 
        $task = new \App\Task;
        $task -> ownerId = $request -> input('userID');
        $task -> title = $request -> get('title');
        $task -> body = $request -> get('body');
        $task -> workedDuration = 0;
        $task -> expDuration = $request -> input('expDuration');
        $task -> estimatedMin = $request -> input('estimatedMin');
        $task -> estimatedHour = $request -> input('estimatedHour');
        $task -> created_at = Carbon::now()->toDateTimeString();
        $task -> updated_at = Carbon::now()->toDateTimeString();
        $task -> lastWorkedAt = null;
        $task -> isFinished = false;
        $task -> save();

        $responseText = 'Successfully Created Object with ID: %s';
        $responseFull = sprintf($responseText, $task->id);
        return response($responseFull, 201)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = \App\Task::find($id);

        if ($task == null) {
            return response('', 404);
        } else {
            return $task;
        }
    }

    /**
     * Display all tasks that belong to the user with userID
     * 
     * @param Request $request 
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request){
        $userID = (int)($request -> userID);
        $tasks = DB::table('tasks')->where('ownerId', '=', $userID)->get(['id', 'title', 'body', 'estimatedMin', 'estimatedHour']);
        return  response()->json($tasks);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $task = \App\Task::find($id);

        if ($task == null) {
            return response('', 404);
        } else if ($task -> isFinished) {
            return response('Task already finished.', 409); 
        }

        $task -> title = $request -> get('title');
        $task -> body = $request -> get('body');
        $task -> workedDuration = $request -> input('workedDuration');
        $task -> estimatedMin = $request -> input('estimatedMin');
        $task -> estimatedHour = $request -> input('estimatedHour');
        $task -> expDuration = $request -> input('expDuration');
        $task -> save();
        return response('', 204);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task = \App\Task::find($id);
        if ($task == null) {
            return response('', 404);
        }

        $task -> delete();
        return response('', 204);
    }


    /**
     * Start the specified task based on ID
     */
    public function start($id) {
        $task = \App\Task::find($id);

        if ($task == null) {
            return response('', 404);
        }

        if ($task -> isFinished) {
            return response('Task already finished.', 409);
        } else if ($task -> lastWorkedAt != null) {
            return response('Task already started.', 409);
        } else {
            $task -> lastWorkedAt = Carbon::now()->toDateTimeString();
            $task -> save();
            return response('', 204);
        }
    }

    /**
     * Stop the specified task based on ID
     */
    public function stop($id) {

        $task = \App\Task::find($id);

        if ($task == null) {
            return response('', 404);
        }

        if ($task -> isFinished) {
            return response('Task already finished.', 409);
        } else if ($task -> lastWorkedAt == null) {
            return response('Task has not been started.', 409);
        } else {
            $totalDuration = Carbon::now()->diffInSeconds(Carbon::parse($task -> lastWorkedAt));
            $task -> workedDuration += $totalDuration;
            $task -> lastWorkedAt = null;
            $task -> save();
            return response('', 204);
        }

    }

    /**
     * Complete the specified task based on ID
     */
    public function finish($id) {
        $task = \App\Task::find($id);

        if ($task == null) {
            return response('', 404);
        } else if ($task -> lastWorkedAt != null) {
            return response('Task currently started, please stop before finishing.', 409);
        } else if ($task -> isFinished) {
            return response('Task already finished.', 409);
        }

        //TODO: Implement Analytics Hook
        $task -> isFinished = true;
        $task -> save();
        return response('', 204);
    }
}
