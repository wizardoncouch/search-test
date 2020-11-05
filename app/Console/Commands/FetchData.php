<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;

use App\Services\ItemService;


use Exception;

class FetchData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:data';

    protected $itemService;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch data from public api and store it to the database.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->itemService = new ItemService();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->fetch();
        // return 0;
    }

    private function fetch(){
        try{
            $client = new Client();

            $response = $client->request('GET', 'https://bikewise.org/api/v2/incidents', [
                'query' => [
                    'page'=> 1,
                    'incident_type' => 'theft',
                    'proximity_square' => 100
                ]
            ]);
            if($response->getBody()){
                $data = json_decode($response->getBody(), true);
                $incidents = $data['incidents'];
                foreach ($incidents as $key => $row) {
                    $itemService->store($row);
                }
            }else{
                // echo 'something went wrong';
                throw new Exception('Something went wrong');
            }

        }catch(Exception $e){
            echo $e->getMessage();
            Log::error($e->getMessage());
            throw $e;
        }

    }

}
