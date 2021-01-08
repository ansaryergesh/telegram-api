<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Telegrammes;
use DB;
use Carbon\Carbon;
class TelegramController extends Controller
{
    public function nonAnswered() {
        $nonAnswered = DB::table('telegrammes')->where('status', 0)->get();
        return response()->json($nonAnswered);
    }
    public function answered() {
        $answered = DB::table('telegrammes')->where('status', 1)->get();
        return response()->json($answered);
    }
    public function index(Request $request) {
        $message_id = $request->input('message_id');
        $iin = $request->input('iin');
        $name = $request->input('name');
        $question = $request->input('question');
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
            if(!$question){
                $result['message'] = 'question required';
                break;
            }

            DB::beginTransaction();
            $testing = DB::table('telegrammes')->insert(
                array(
                  'message_id' =>$message_id,
                  'iin'=>$iin,
                  'name'=>$name,
                  'question'=>$question,
                  'created_at' => Carbon::now(),
                )
            );
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
        $result = DB::table('telegrammes')->where('created_at','<', $dates)->get();
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
      $message = DB::table('telegrammes')->where('id', $id)->first();
      if(isset($message)) {
          try {
              DB::beginTransaction();
              DB::table('telegrammes')->where('id',$id)->update(['status'=>1, 'updated_at'=>Carbon::now()]);
              DB::commit();
              return response()->json([
                "success"=> 'true',
                "message" => "updated"
            ], 200);
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