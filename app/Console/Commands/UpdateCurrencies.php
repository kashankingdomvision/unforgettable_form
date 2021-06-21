<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\CurrencyConversions;

class UpdateCurrencies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:currencies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to update the currencies for conversion';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function curl_data($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$url");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        return $output = curl_exec($ch);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::info(" UpdateCurrencies Cron is working fine!");

   
        $from = CurrencyConversions::pluck('from');
        $to   = CurrencyConversions::pluck('to');
        
        for($i=0 ; $i<16; $i++){
          $url = "https://free.currencyconverterapi.com/api/v6/convert?q=$from[$i]_$to[$i]&compact=ultra&apiKey=9910709386be4f00aa5b";
          $output2 =  json_decode($this->curl_data($url));
          $key = "$from[$i]_$to[$i]";

          CurrencyConversions::where('from',"$from[$i]")->where('to',"$to[$i]")->update(['value' => floatval($output2->{$key}) ]); 
        }
    }
}
