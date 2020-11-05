<?php

namespace App\Services;

use stdClass;
use Exception;

use App\Models\Item;

use Illuminate\Support\Facades\Log;


class ItemService {


	public function find($keyword){
		try{
            $array = [
                'The howling wolf',
                'The Hunting spider',
                'The Flying raven'
            ];
            return $array;
		}catch(Exception $e){
			Log::critical('Cannot find: '. $e->getMessage());
			throw $e;
		}

	}

	public function findOne($id){
		try{
			$item = Item::find($id);
			return $item;
		}catch(Exception $e){
			Log::critical('Cannot find: '. $e->getMessage());
			throw $e;
			
		}

	}

	public function store($data){
		try{
			$item = new Item();
			$item->title = $data['title'];
			$item->description = $data['description'];
			$item->save();
		}catch(Exception $e){
			Log::critical('Cannot store data: '. $e->getMessage());
		}

	}


}