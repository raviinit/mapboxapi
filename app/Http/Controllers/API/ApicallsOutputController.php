<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\ApicallsOutput as ApicallsOutputResource;
use Validator;
use App\Http\Helper\ApiHelper;
use App\ApicallsOutput;

class ApicallsOutputController extends BaseController
{
    public function __construct()
    {
        // --
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $apicallsoutput = ApicallsOutput::all();
        return response([ 'apicallsoutput' => ApicallsOutputResource::collection($apicallsoutput), 'message' => 'Retrieved successfully'], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required|max:255',
            'latitude' => 'required|max:255',
            'longitude' => 'required|max:255'
        ]);

        if($validator->fails()){
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

        $apicallsoutput = ApicallsOutput::create($data);

        return response([ 'apicallsoutput' => new ApicallsOutputResource($apicallsoutput), 'message' => 'Created successfully'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ApicallsOutput  $apicalls
     * @return \Illuminate\Http\Response
     */
    public function show(ApicallsOutput $apicalls)
    {
        return response([ 'apicallsoutput' => new ApicallsOutputResource($apicallsoutput), 'message' => 'Retrieved successfully'], 200);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ApicallsOutput  $apicallsoutput
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ApicallsOutput $apicallsoutput)
    {
        $apicallsoutput->update($request->all());

        return response([ 'apicallsoutput' => new ApicallsOutputResource($apicallsoutput), 'message' => 'Retrieved successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ApicallsOutput  $apicallsoutput
     * @return \Illuminate\Http\Response
     */
    public function destroy(ApicallsOutput $apicallsoutput)
    {
        $apicallsoutput->delete();

        return response(['message' => 'Deleted']);
    }
}
