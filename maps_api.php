<?php

/*
	ini_set('display_errors', '1');
	ini_set('display_startup_errors', '1');
	error_reporting(E_ALL);
*/

	class urlHandler
	{
		protected $resp = array();
		
		protected $latitude;
		
		protected $longitude;
		
		protected $address;
		
			
		public function get_urlresponse($url)
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			// JSON Reponse from URL		
			$resp_json = curl_exec ($ch);
			
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close ($ch);
			
			// decode the json response to array		
			$this->resp = json_decode($resp_json, true);
			
			return $httpCode;
		}	
		
		public function getAddress () 
		{
			return $this->address;
		}
		
		public function getLatitude () 
		{
			return $this->latitude;
		}
		
		public function getLongiture () 
		{
			return $this->longitude;
		}
		
	}



	class mapOsmApi extends urlHandler
	{
		
		const BASE_URL = 'https://nominatim.openstreetmap.org/?email=reewa_2000@yahoo.com&format=json&limit=1&addressdetails=1';
		
		private $baseUrl = "";	
		
		
		function __construct ()
		{        
			$this->baseUrl = self::BASE_URL ;
		}  
			
		public function get_geocode($address)
		{		
			try	
			{
				// url encode the address	
				$address = urlencode($address);
				
				// Form Query Param url
				$url = $this->baseUrl . "&q={$address}";
					
				// Get Response
				$status = $this->get_urlresponse($url);
								
				if($status == "200" )
				{	
					// Valid Response				
					$data_arr = array();			
					$data_arr['latitude'] =  isset($this->resp[0]['lat']) 	? $this->resp[0]['lat'] : '';
					$data_arr['longitude'] =  isset($this->resp[0]['lon']) 	? $this->resp[0]['lon'] : '';		

					if(!empty($data_arr) && !empty($data_arr['latitude']) && !empty($data_arr['longitude']))
					{
						$this->latitude = $data_arr['latitude'];
						$this->longitude = $data_arr['longitude'];
						$this->address = $address;
						
						return $data_arr;
					}
					else
					{
						return false;
					}
				}
				else
				{
					return false;
				}	
			}
			catch(Exception $e)
			{
				echo $e->getMessage();
			}
		}
	}
	
	
	class mapGeoCodeApi extends urlHandler
	{
		
		const BASE_URL = 'https://maps.google.com/maps/api/geocode/json?';
		
		private $apikey;		
		
		private $baseUrl = "";	
 
 
		function __construct ($key = '')
		{        
			$this->apikey = $key;
			$this->baseUrl = self::BASE_URL ."&key=" . $this->apikey; ;
		}  
			
		public function get_geocode($address)
		{		
			try	
			{
				// url encode the address	
				$address = urlencode($address);
				
				// Form Query Param url
				$url = $this->baseUrl . "&address={$address}";
					
				// Get Response
				$status = $this->get_urlresponse($url);
								
				if($this->resp['status']=='OK')
				{		
					$data_arr = array();			
					$data_arr['latitude'] = isset($this->resp['results'][0]['geometry']['location']['lat']) ? $this->resp['results'][0]['geometry']['location']['lat'] : '';
					$data_arr['longitude'] = isset($this->resp['results'][0]['geometry']['location']['lng']) ? $this->resp['results'][0]['geometry']['location']['lng'] : '';
								
					// verify if data is exist
					if(!empty($data_arr) && !empty($data_arr['latitude']) && !empty($data_arr['longitude']))
					{	 
						return $data_arr;
					}
					else
					{
						return false;
					}
				}				
				else
				{
					return false;
				}	
			}
			catch(Exception $e)
			{
				echo $e->getMessage();
			}
		}
	}




?>

