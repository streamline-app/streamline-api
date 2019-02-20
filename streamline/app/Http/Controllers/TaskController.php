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
        $tasks = \App\Task::all();
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
        $tagIDs = $request -> get('tags');

        //TODO: Validation 
        $task = new \App\Task;
        $task -> ownerId = $request -> input('userID');
        $task -> title = $request -> get('title');
        $task -> body = $request -> get('body');
        $task -> workedDuration = 0;
        $task -> expDuration = 0;
        $task -> estimatedMin = $request -> input('estimatedMin');
        $task -> estimatedHour = $request -> input('estimatedHour');
        $task -> created_at = Carbon::now()->toDateTimeString();
        $task -> updated_at = Carbon::now()->toDateTimeString();
        $task -> lastWorkedAt = null;
        $task -> active = false;
        $task -> save();

        $tags = \App\Tag::find($tagIDs);
        $task->tags()->attach($tags);

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
     * Display all tasks that belong to the user with userID
     * 
     * @param Request $request 
     * @return \Illuminate\Http\Response
     */
    public function list($userID){

        $tasks = \App\User::find($userID)->tasks;

        return  response()->json($tasks);
    }

    public function listTags($id){
        $task = \App\Task::find($id);
        $tags = $task->tags;

        return response()->json($tags);
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
        $task -> estimatedMin = $request -> input('estimatedMin');
        $task -> estimatedHour = $request -> input('estimatedHour');
        $task -> expDuration = $request -> input('expDuration');
        $task -> save();
        return 200;
    }

    /**
     * 
     */
    public function removeTag($id, $tagID){
        $task = \App\Task::find($id);
        $task->tags()->detach($tagID);
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
