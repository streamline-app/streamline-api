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
        return $tasks;
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
        $task -> ownerId = 0;
        $task -> title = $request -> get('title');
        $task -> body = $request -> get('body');
        $task -> workedDuration = $request -> input('workedDuration');
        $task -> expDuration = $request -> input('expDuration');
        $task -> created_at = Carbon::now()->toDateTimeString();
        $task -> updated_at = Carbon::now()->toDateTimeString();
        $task -> lastWorkedAt = null;
        $task -> active = false;
        $task -> save();
        return 201;
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
        return $task;
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
        $task -> title = $request -> get('title');
        $task -> body = $request -> get('body');
        $task -> workedDuration = $request -> input('workedDuration');
        $task -> expDuration = $request -> input('expDuration');
        $task -> save();
        return 200;
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
        $task -> delete();
        return 200;
    }
}
