<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Person;
use Validator;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->get('limit') != null ? $request->get('limit') : 10;
            $persons = Person::with('position')->paginate($limit);
        } catch(\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }

        return response()->json($persons, 200);
    }

    /**
     * Show the table for listing all positions.
     *
     * @return \Illuminate\Http\Response
     */
    public function showTable()
    {
        return view('persons.index');
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
        try {
            $rules = [
                'name' => 'required|unique:people|alpha|max:100',
                'email' => 'required|email',
                'identification' => 'unique:people|size:6|nullable',
                'position_id' => 'exists:positions,id|nullable'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) 
                return response()->json(['success' => false, 'errors' => $validator->errors()], 400);

            $person = new Person();
            $person->name = $request['name'];
            $person->email = $request['email'];
            $person->identification = $request['identification'];
            $person->position_id = $request['position_id'];
            $person->save();

            return response()->json(['success' => true, 'person' => $person], 200);
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
            $person = Person::findOrFail($id);
        } catch(\Exception $e) {
            return response()->json(['success' => false, 'errors' => $e->getMessage()], 500);
        }

        return response()->json($person, 200);
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
            $person = Person::findorFail($id);

            if($request->has('name') && $person->name != $request['name']) {
                $rules['name'] = 'required|unique:people|alpha|max:100';
                $person->name = $request['name'];
            }

            if($request->has('email') && $person->email != $request['email']) {
                $rules['email'] = 'required|email';
                $person->email = $request['email'];
            }

            if($request->has('identification') && $person->identification != $request['identification']) {
                $rules['identification'] = 'unique:people|size:6|nullable';
                $person->identification = $request['identification'];
            }

            if($request->has('position_id') && $person->position_id != $request['position_id']) {
                $rules['position_id'] = 'exists:positions,id|nullable';
                $person->position_id = $request['position_id'];
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) 
                return response()->json(['success' => false, 'errors' => $validator->errors()], 400);

            $person->save();

            return response()->json(['success' => true, 'person' => $person], 200);
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
            $person = Person::findOrFail($id);
            $person->delete();
            return response()->json(['success' => true, 'person' => $person], 200);
        } catch(\Exception $e) {
            return response()->json(['success' => false, 'errors' => $e->getMessage()], 500);
        }
    }
}
