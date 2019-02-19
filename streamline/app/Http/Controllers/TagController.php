<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tags = \App\Tag::all();

        return $tags;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $tag = new \App\Tag;
        $tag -> name = $request -> input('name');
        $tag -> description = $request -> input('description');
        $tag -> tasks_completed = 0;
        $tag -> average_time = 0;
        $tag -> average_accuracy = 0;
        $tag -> task_over_to_under = 0;
        $tag -> userID =  $request -> input('userID');
        $tag -> color = $request -> input('color');
        $tag -> created_at = Carbon::now()->toDateTimeString();
        $tag -> updated_at = Carbon::now()->toDateTimeString();
        $tag ->save();
  
        return 201;
    }


    /**
     * Display all tags that belong to the user with userID
     * 
     * @param Request $request 
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request){

        $tags = \App\User::find($request -> userID)->tags;

        return $tags;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
    }

    /**
     * edit the specified resource in storage based on user input
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $tag = \App\Tag::find($id);
        $tag -> name = $request -> input('name');
        $tag -> description =  $request -> input('desc');
        $tag -> color =  $request -> input('color');
        $tag -> updated_at = Carbon::now()->toDateTimeString();
        $tag ->save();

        return 201;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {       
        $tag = \App\Tag::find($id);
        $tag -> delete();

        return 200;
    }
}
