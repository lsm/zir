<?php

	class GeoCouch
	{
		var $conf = array(
			'host' => 'localhost',
			'port' => '5984',
			'db' => 'sf_geo',
			'geocoder' => array(
					'url' => 'http://maps.google.com/maps/geo?key=',
					'key' => 'ABQIAAAAVGtDOSlFqk8NmAuso8hCbhTtNrbKIcQUlnZjajMW7XYQgWF6shT0P7bVRIFD86VkxyxQyxJ37BM0Pg',
				),
		);
		
		var $address;
		var $geoJSONResponse;
		var $geoObj;
		
		function GeoCouch() {
			
		}
		
		function geoCode($address = null)
		{
			$this->address = $address;
			$url = $this->conf['geocoder']['url'].$this->conf['geocoder']['key'];
			$url .= '&q='.urlencode($address);
			
			$this->geoJSONResponse = $this->_geoCodeRequest($url);
			$this->geoObj = json_decode($this->geoJSONResponse);
			
			if(empty($this->geoObj->Status->code) || $this->geoObj->Status->code != 200) {
				return false;
			} else {
				return $this->geoJSONResponse;
			}	
		}
		
		function _geoCodeRequest($url) 
		{
			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL, $url);
			curl_setopt ($ch, CURLOPT_HEADER, 0);
			ob_start();
			curl_exec ($ch);
			curl_close ($ch);
			$string = ob_get_contents();
			ob_end_clean();
			return $string;
		}
		
		function locationName($str) {
			return trim(preg_replace('/[^a-z0-9]+/i', '-', $str), '_');
		}
		
		function save($address, $extra = array())
		{
			$existing = $this->get($address);
			
			if($this->geoCode($address)) 
			{
				if(!empty($existing->_rev)) {
					$this->geoObj->_rev = $existing->_rev;
				}
				
				foreach($extra as $field => $value) {
					$this->geoObj->$field = $value;
				}
				
				return $this->put($address, json_encode($this->geoObj));
			}
			else {
				return false;
			}
		}
		
		function get($name = null)
		{
			$s = $this->openSock();
			
			$url = '/'.$this->conf['db'].'/'.$this->locationName($name);
			$request = 'GET '.$url.' HTTP/1.0'. "\r\n";
			$request .= 'Host: localhost'. "\r\n\r\n";
			fwrite($s, $request);
			
			return $this->parseCouchResponse($s);		
		}
		
		function put($name = null, $json = null)
		{
			$s = $this->openSock();
			
			$url = '/'.$this->conf['db'].'/'.$this->locationName($name);
			$request = 'PUT '.$url.' HTTP/1.0'. "\r\n";
			$request .= 'Host: localhost'. "\r\n";
			$request .= 'Content-Length: '.strlen($json)."\r\n\r\n";
			$request .= $json."\r\n";
			fwrite($s, $request);
			
			return $this->parseCouchResponse($s);	
		}
		
		function openSock() 
		{
			$s = fsockopen($this->conf['host'], $this->conf['port'], $errno, $errstr);
			if(!$s) {
				return $errno.':'.$errstr;
			} else {
				return $s;
			}
		}
		
		function parseCouchResponse($s) 
		{
			$response = '';
			while(!feof($s)) {
				$response .= fgets($s);
			}
			fclose($s);
			
			list($headers, $body) = explode("\r\n\r\n", $response);
			return json_decode($body);
		}
	}
?>