<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CodeIgniter GoogleUrlApi Library.
 * Do whatever you want with this library.
 * It uses the tmhOauth library from themattharris. All credits to him.
 *
 * @author Demostenes Garcia -> www.demogar.com + www.twitter.com/demogar
 * @version 0.1 (2011-01-13)
 *
 */
class GoogleUrlApi {
	
	private $CI; // CodeIgnier super object (to handle CI functionality)

    private $apiURL;

	function __construct()
	{
		$this->CI =& get_instance();
        $this->apiURL ='https://www.googleapis.com/urlshortener/v1/url?key='.GOOGLE_API_KEY;
	}

    // Shorten a URL
    function shorten($url) {
        // Send information along
        $response = $this->send($url);
        // Return the result
        return isset($response['id']) ? $response['id'] : false;
    }

    // Send information to Google
    function send($url,$shorten = true) {
        // Create cURL
        $ch = curl_init();
        // If we're shortening a URL...
        if($shorten) {
            curl_setopt($ch,CURLOPT_URL,$this->apiURL);
            curl_setopt($ch,CURLOPT_POST,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode(array("longUrl"=>$url)));
            curl_setopt($ch,CURLOPT_HTTPHEADER,array("Content-Type: application/json"));
        }
        else {
            curl_setopt($ch,CURLOPT_URL,$this->apiURL.'&shortUrl='.$url);
        }
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // Execute the post
        $result = curl_exec($ch);
        // Close the connection
        curl_close($ch);
        // Return the result
        return json_decode($result,true);
    }
}
