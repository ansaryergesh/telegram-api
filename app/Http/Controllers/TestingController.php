<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Testing;
use DB;
use Carbon\Carbon;
class TestingController extends Controller
{
    public function index(Request $request) {
        $cpa_source = $request->input('cpa_source');
        $clickID = $request->input('clickID');
        $nomerZayavki = $request->input('nomerZayavki');
        $result['success'] = false;

        do {
            if(!$cpa_source){
                $result['message'] = 'cpa source required';
                break;
            }
            if(!$clickID){
                $result['message'] = 'clickID required';
                break;
            }
            if(!$nomerZayavki){
                $result['message'] = 'nomerZayavki required';
                break;
            }

            DB::beginTransaction();
            $testing = Testing::insertGetId([
                'cpa_source' =>$cpa_source,
                'clickID'=>$clickID,
                'nomerZayavki'=>$nomerZayavki,
                'created_at' => Carbon::now(),
            ]);
            if (!$testing){
                DB::rollback();
                $result['message'] = 'Что то произошло не так попробуйте позже';
                break;
            }
            DB::commit();
            $result['success'] = true;
            $result['message'] = 'Успешно добавлен';
        }while(false);

        return response()->json($result);
    }

    public function delete ($id) {
        $testing = Testing::find($id);
        if($testing) {
            $testing->delete(); 
            return response()->json([
                "message" => "records deleted"
            ], 202);
        }
       else {
          return response()->json([
            "message" => "testing info not found"
          ], 404);
        }
    }
}
