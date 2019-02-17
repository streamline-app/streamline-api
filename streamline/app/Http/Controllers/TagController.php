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
        $tags = DB::table('tags')->get();

        return view('tags.index', ['tags' => $tags]);
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
        $name = $request -> input('name');
        $description = $request -> input('description');
        $userID = $request -> input('userID');
        $color = $request -> input('color');

        DB::table('tags')->insert(
            [
                'name' => $name,
                'description' => $description,
                'tasks_completed' => 0,
                'average_time' => 0.0,
                'average_accuracy' => 0.0,
                'task_over_to_under' => 0.0,
                'color' => $color, //default color will be light grey
                'userID' => $userID,
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString()
            ]
        );
        
        return 201;
    }


    /**
     * Display all tags that belong to the user with userID
     * 
     * @param Request $request 
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request){
        $userID = (int)($request -> userID);
        $tags = DB::table('tags')->where('userID', '=', $userID)->get();
        return  response()->json($tags);
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
        //only three fields can be edited by the user, all others are done by app
        DB::table('tags')->where('id', '=', $id)->update(
            [
                'name' => $request -> input('name'),
                'description' => $request -> input('desc'),
                'color' => $request -> input('color'), //default color will be light grey
                'updated_at' => Carbon::now()->toDateTimeString()
            ]
        );

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
        //$id = (int)($request -> id); //get ID passed through request
        //DB::delete('delete from tags where id = ?', [$id]);
        
        $query_tag = DB::table('tags')->where('id', '=', $id);
        $query_tag->delete();

        return 200;
    }
}
