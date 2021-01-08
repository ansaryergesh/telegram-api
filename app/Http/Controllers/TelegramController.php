<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Telegrammes;
use DB;
use Carbon\Carbon;
class TelegramController extends Controller
{
    public function nonAnswered() {
        $nonAnswered = Telegrammes::where('status', 0)->get()->toJson(JSON_PRETTY_PRINT);;
        return response($nonAnswered, 200);
    }
    public function answered() {
        $nonAnswered = Telegrammes::where('status', 1)->get()->toJson(JSON_PRETTY_PRINT);;
        return response($nonAnswered, 200);
    }
    public function index(Request $request) {
        $message_id = $request->input('message_id');
        $iin = $request->input('iin');
        $name = $request->input('name');
        $vopros = $request->input('question');
        $result['success'] = false;

        do {
            if(!$message_id){
                $result['message'] = 'messageId required';
                break;
            }
            if(!$iin){
                $result['message'] = 'iin required';
                break;
            }
            if(!$name){
                $result['message'] = 'name required';
                break;
            }
            if(!$vopros){
                $result['message'] = 'vopros required';
                break;
            }

            DB::beginTransaction();
            $testing = Telegrammes::insertGetId([
                'message_id' =>$message_id,
                'iin'=>$iin,
                'name'=>$name,
                'question'=>$vopros,
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

    //Удалить данные выше недели
    public function delete() {
        $dates = \Carbon\Carbon::today()->subDays(7);
        $result = Telegrammes::where('created_at', '<', $dates)->get();
        if(count($result) !== 0) {
            $result->each->delete();
            return response()->json([
                'success' => true,
                'message' => 'messages more than week deleted'
            ], 200);
        }
        else {
            return response()->json([
                'success' => false,
                'message' => 'already deleted'
            ], 200);
        }
    }

    public function editTelega($id) {
      $message = Telegrammes::find($id);
      if($message) {
          try {
              DB::beginTransaction();
                $message->status=1;
                $message->updated_at=Carbon::now();
                $message->save();
              DB::commit();
              return response()->json([
                "success"=> 'true',
                "message" => "updated"
            ], 202);
          }catch(\Exception $e){
              DB::rollback();
              return response()->json([
                "success"=> 'false',
                "message" => "ошибка хз что"
            ], 403);
          }
      }else {
        return response()->json([
            "message" => "message info not found"
          ], 404);
      }
    }
}
 // if($message) {
        //   return response()->json([
        //     "message" => "updated"
        //   ], 202);
        // }else {
            // return response()->json([
            //   "message" => "testing info not found"
            // ], 404);
        // }