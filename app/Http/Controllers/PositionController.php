<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Position;
use Validator;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $positions = Position::all();
        } catch(\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }

        return response()->json($positions, 200);
    }

    /**
     * Show the table for listing all positions.
     *
     * @return \Illuminate\Http\Response
     */
    public function showTable()
    {
        return view('positions.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('positions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $rules = [
                'name' => 'required|unique:positions'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) 
                return response()->json(['success' => false, 'errors' => $validator->errors()], 400);

            $position = new Position();
            $position->name = $request['name'];
            $position->save();

            return response()->json(['success' => true, 'position' => $position], 200);
        } catch(\Exception $e) {
            return response()->json(['success' => false, 'errors' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $position = Position::findOrFail($id);
        } catch(\Exception $e) {
            return response()->json(['success' => false, 'errors' => $e->getMessage()], 500);
        }

        return response()->json($position, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        try {
            $rules = [];
            $position = Position::findOrFail($id);

            if($request->has('name') && $position->name != $request['name']) {
                $rules['name'] = 'required|unique:positions';
                $position->name = $request['name'];
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) 
                return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
            
            $position->save();

            return response()->json(['success' => true, 'position' => $position], 200);
        } catch(\Exception $e) {
            return response()->json(['success' => false, 'errors' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $position = Position::findOrFail($id);
            $position->persons()->delete();
            $position->delete();
            return response()->json(['success' => true, 'position' => $position], 200);
        } catch(\Exception $e) {
            return response()->json(['success' => false, 'errors' => $e->getMessage()], 500);
        }
    }
}
