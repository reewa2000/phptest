<?php
/*
	ini_set('display_errors', '1');
	ini_set('display_startup_errors', '1');
	error_reporting(E_ALL);
*/
	
	require_once("maps_api.php");
	

	if(isset($_REQUEST['addr']) && $_REQUEST['addr'] !='')
	{	
		$srch_from = isset($_REQUEST['srch_from']) ? $_REQUEST['srch_from'] : '1';
		$map = '';
		
		if($srch_from ==1)
		{
			// Use OSM API to get co-ordinates
			$map = new mapOsmApi(); 
		}
		else
		{
			// Use Google API to get co-ordinates
			$gmapAPI_KEY = "AIzaSyAzzNHQF8BrVyL1y1g--Bt6VDTQfycNE6k";			
			$map = new mapGeoCodeApi($gmapAPI_KEY);
		}		
		echo  json_encode( $map->get_geocode($_REQUEST['addr']) ) ;
	}
	else
		echo false;
		
?>