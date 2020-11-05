<?php

namespace App\Http\Controllers;

use App\Services\ItemService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Exception;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, ItemService $itemService)
    {
        //
        try{
            return response()->json($itemService->find($request->keyword));
        }catch(Exception $e){
            Log::error('Something went wrong: '. $e->getMessage());
            return response()->json($e->getMessage(), 500);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, ItemService $itemService)
    {
        //
        try{
            $item = $itemService->findOne($id);
            if($item){
                return response()->json($item);
            }else{
                return response()->json('Item not found.', 404);
            }
        }catch(Exception $e){
            Log::error('Something went wrong: '. $e->getMessage());
            return response()->json($e->getMessage(), 500);
        }
    }


}
