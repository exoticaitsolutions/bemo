<?php

namespace App\Http\Controllers;
use App\Models\Cards;
use App\Models\Items;
use Illuminate\Http\Request;
use DB;

class CardController extends Controller
{
    public function cardList(){
        try{
            $cards = Cards::with('items')->get();
            return response()->json(array('status'=>  true, 'data' => $cards));

        }catch(Exception $e){
            return response()->json(array('status'=>  false, 'data' => []));
        }
    }

    public function addNewCard(Request $request){
        DB::beginTransaction();
        try{
            $data = $request->all();
            $cards = new Cards;
            $cards->title = $data['title'];
            $cards->save();
            DB::commit();
            return response()->json(array('status'=>  true, 'data' => $cards));
        }catch(Exception $e){
            DB::rollback();
            return response()->json(array('status'=>  false, 'data' => []));
        }
    }

    public function addNewItem(Request $request){
        DB::beginTransaction();
        try{
            $data = $request->all();
            $items = new Items;
            $items->cards_id = $data['listId'];
            $items->title = $data['title'];
            $items->save();
            DB::commit();
            return response()->json(array('status'=>  true, 'data' => $items));
        }catch(Exception $e){
            DB::rollback();
            return response()->json(array('status'=>  false, 'data' => []));
        }
    }

    public function deleteCard($id){
        DB::beginTransaction();
        try{
            $cards = Cards::find($id);
            // delete related   
            $cards->items()->delete();
            $cards->delete();
            DB::commit();
            return response()->json(array('status'=>  true, 'data' => []));
        }catch(Exception $e){
            DB::rollback();
            return response()->json(array('status'=>  false, 'data' => []));
        }
    }

    public function deleteItem($id){
        DB::beginTransaction();
        try{
           Items::whereId($id)->delete();
            DB::commit();
            return response()->json(array('status'=>  true, 'data' => []));
        }catch(Exception $e){
            DB::rollback();
            return response()->json(array('status'=>  false, 'data' => []));
        }
    }
}
