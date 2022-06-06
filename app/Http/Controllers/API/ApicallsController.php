<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\Apicalls as ApicallsResource;
use Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Helper\ApiHelper;
use App\Apicalls;
use App\ApicallsOutput;

class ApicallsController extends BaseController
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
        $apicalls = Apicalls::all();
        return response([ 'apicalls' => ApicallsResource::collection($apicalls), 'message' => 'Retrieved successfully'], 200);
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
            'format' => 'required|max:255'
        ]);

        if($validator->fails()){
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

        $apicalls = Apicalls::create($data);

        return response([ 'apicalls' => new ApicallsResource($apicalls), 'message' => 'Created successfully'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Apicalls  $apicalls
     * @return \Illuminate\Http\Response
     */
    public function show(Apicalls $apicalls)
    {
        return response([ 'apicalls' => new ApicallsResource($apicalls), 'message' => 'Retrieved successfully'], 200);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Apicalls  $apicalls
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Apicalls $apicalls)
    {
        $apicalls->update($request->all());

        return response([ 'apicalls' => new ApicallsResource($apicalls), 'message' => 'Retrieved successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Apicalls  $apicalls
     * @return \Illuminate\Http\Response
     */
    public function destroy(Apicalls $apicalls)
    {
        $apicalls->delete();

        return response(['message' => 'Deleted']);
    }


    /**
     * Make an mPokket API call
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function mapboxcall (Request $request) {
        /*
            Specimen Mapbox API
            https://api.mapbox.com/geocoding/v5/mapbox.places/Washington.json?limit=2&access_token=sk.eyJ1IjoiYWpheWdhcmdhIiwiYSI6ImNram1rM21wNjNtcjUzMXNjenE3bm9vYXUifQ.4oQ--6S-yF55fnXdUjs2Zw
        */

        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required|max:255',
            'format' => 'required|max:255'
        ]);

        $names = explode(',', $request->name);
        if($validator->fails() || count($names) > 3){
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

        $mapbox_api_url = $_ENV['MAPBOX_API_URL'];
        $mapbox_api_key = $_ENV['MAPBOX_API_KEY'];
        $params         = array(
            "limit"         => 2,
            "access_token"  => $mapbox_api_key,
        );
        $file_params = array();

        try {
            // Make Mapbox calls for each city to get the geometry data
            $geometryData        = array();
            $apicalls            = Apicalls::create($data);
            foreach($names as $name) {
                $nameFound           = ApicallsOutput::whereIn('name', (array)$name)->get();
                if (count($nameFound) > 0) {
                    array_push($geometryData, $nameFound);
                } else {
                    $completeURL    = $mapbox_api_url . trim($name) . '.json';
                    $result         = ApiHelper::make_api_call('GET', $completeURL, $params, array(), $file_params);
                    $result         = json_decode($result, true);
                    $len            = isset($result) ? count($result) : 0;
                    if ($len > 0) {
                        foreach($result['features'] as $coordinate){
                            $dataSet[] = [
                                'name'          => trim($name),
                                'latitude'      => $coordinate['geometry']['coordinates'][1],
                                'longitude'     => $coordinate['geometry']['coordinates'][0],
                                'created_at'    => now(),
                                'updated_at'    => now()
                            ];
                            $apicallsoutput  = ApicallsOutput::insert($dataSet); //DB::table('apicallsoutput')->insert($dataSet);
                            array_push($geometryData, $dataSet);
                            $dataSet         = array();
                        }
                    }
                }
            }
            return response([ 'apicallsoutput' => $geometryData, 'message' => 'Retrieved successfully'], 200);
        } catch (Throwable $e) {
            $this->logMessage(LogLevel::ERROR,"make_api_call:CURL Exception for $completeURL ::: Error code:" . $e->getCode() . "* Error Message:" . $e->getMessage() . "***");
            return false;
        }
    }


}
