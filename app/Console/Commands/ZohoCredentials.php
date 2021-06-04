<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ZohoCredential;

class ZohoCredentials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoho:credentials';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function cf_remote_request($url, $_args = array()) {
		// prepare array
		$array = array(
			//'status' => false,
			'message' => array(
				'101' => 'Invalid url',
				'102' => 'cURL Error #: ',
				'200' => 'cURL Successful #: ',
				'400' => '400 Bad Request',
			)
		);
		
		// initalize args
		$args = array(
			'method' 		=> 'POST',
			'timeout' 		=> 45,
			'redirection' 	=> 5,
			'httpversion' 	=> '1.0',
			'blocking' 		=> true,
			'ssl' => true,
			'headers' => array(),
			'body' => array(),
			'returntransfer' => true,
			'encoding' => '',
			'maxredirs' => 10,
			'format' => 'JSON'
		);
		
		if( empty($url) ) {
			$code = 101;
			$response = array('status' => $code, 'body' => $array['message'][$code]);
			return $response;
		}
		
		if( !empty($_args) && is_array($_args) )
			$args = array_merge($args, $_args);
		
		$fields = $args['body'];
		if( strtolower($args['method']) == 'post' && is_array($fields) )
			$fields = http_build_query( $fields );
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL 			=> $url,
			CURLOPT_RETURNTRANSFER 	=> $args['returntransfer'],
			CURLOPT_ENCODING 		=> $args['encoding'],
			CURLOPT_MAXREDIRS 		=> $args['maxredirs'],
			CURLOPT_HTTP_VERSION 	=> $args['httpversion'],// CURL_HTTP_VERSION_1_1,
			CURLOPT_USERAGENT 		=> isset($_SERVER['HTTP_USER_AGENT']) && !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
			//CURLOPT_HEADER 			=> true,
			CURLINFO_HEADER_OUT 	=> true,
			CURLOPT_TIMEOUT 		=> $args['timeout'],
			CURLOPT_CONNECTTIMEOUT 	=> $args['timeout'],
			CURLOPT_SSL_VERIFYPEER 	=> $args['ssl'] === true ? true : false,
			//CURLOPT_SSL_VERIFYHOST 	=> $args['ssl'] === true ? true : false,
            // CURLOPT_CAPATH     		=> APPPATH . 'certificates/ca-bundle.crt',
			CURLOPT_CUSTOMREQUEST 	=> $args['method'],
			CURLOPT_POSTFIELDS 		=> $fields,
			CURLOPT_HTTPHEADER 		=> $args['headers'],
		));
	
		$curl_response 	= curl_exec($curl);
		$err 			= curl_error($curl);
		$curl_info = array(
			'status' 		=> curl_getinfo($curl, CURLINFO_HTTP_CODE),
			'header' 		=> curl_getinfo($curl, CURLINFO_HEADER_OUT),
			'total_time' 	=> curl_getinfo($curl, CURLINFO_TOTAL_TIME)
		);
		
		curl_close($curl);
		
		
		if( $err ) {
			$response = array('message' => $err, 'body' => $err);
			
		} else {
			if( $curl_info['status'] == 200
			&& in_array($args['format'], array('ARRAY', 'OBJECT')) 
			&& !empty($curl_response) && is_string($curl_response) ) {
				$curl_response = json_decode( $curl_response, $args['format'] == 'ARRAY' ? true : false );
                $curl_response = ( json_last_error() == JSON_ERROR_NONE ) ? $curl_response : $curl_response;
			}
            else{
                $curl_response = json_decode($curl_response, TRUE);
            }
			
			$response = array(
				//'message' 	=> $array['message'][ $curl_info['status'] ],
				'body' 		=> $curl_response
			);
		}
		
		$response = array_merge($curl_info, $response);
		return $response;
	}

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::info("start new  ZohoCredentials Cron is working fine!");

        $zoho_credentials = ZohoCredential::findOrFail(1);
        $refresh_token = $zoho_credentials->refresh_token;
        $url = "https://accounts.zoho.com/oauth/v2/token?refresh_token=" . $refresh_token . "&client_id=1000.0VJP33J6LLOQ63896U88RWYIVJRSFD&client_secret=81212149f53ee4039b280b420835d64b8443c96a83&grant_type=refresh_token";
        $args = array('ssl' => false, 'format' => 'ARRAY');
        $response = $this->cf_remote_request($url, $args);
        if( $response['status'] == 200 ) {
			$body = $response['body'];
            $zoho_credentials->access_token = $body['access_token'];
            $zoho_credentials->save();
		}

    }
}
