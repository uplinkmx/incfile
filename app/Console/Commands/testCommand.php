<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class testCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:incfile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Incfile';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $faker = \Faker\Factory::create();
        try {
            $url = 'https://atomic.incfile.com/fakepost';
            $data = ['name'=> $faker->name, 'email' => $faker->email, 'phone'=>$faker->phoneNumber];
            $response = $this->requestPost($url,$data);
             // if have an server error loggin the error and retry again the request
            if($response->serverError()){
                $this->serverError($response, $url, $data);
            }
            //the url has been reached but send an status not like 200 and try again
            if($response->status() != 200){
                Log::error('server error: unable to get the adequate response we got ' . $response->status());        
                $this->requestPost($url,$data);
            }
            //let save the response if we have it 
            Log::info('this is the response: ' . $response->json() );
            return 0;
        } catch (\Exception $e){
            echo "an error has been ocurred".PHP_EOL;
            echo "//start of error ->".PHP_EOL;;
            echo $e->getMessage().PHP_EOL;;
            echo " <-- end of error//".PHP_EOL;;
            Log::error('laravel error ' . $e->getMessage());
            return 0;
        }

    }
    
    public function requestPost($url, $data)
    {
        return Http::post($url,$data);
    }

    public function serverError($response, $url,$data)
    {
        Log::error('server error ' . $response->serverError());
        return $this->requestPost($url,$data);
    }
}