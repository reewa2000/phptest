<?php
	$gmapkey="AIzaSyAzzNHQF8BrVyL1y1g--Bt6VDTQfycNE6k";
?>

<!doctype html>
<html lang="en">
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
		<title>Location Finder</title>
		<script src="includes/js/jquery.min.js"></script>		
		<script src="http://www.openlayers.org/api/OpenLayers.js"></script>
		
		
	</head>
	<body>
	<div class="container"> 
		<div class="row">
			<div class="col-md-12">	
				
				<form name='thisForm' id='thisForm' class="form-horizontal mt-5" method='POST' action=""> 
					<div class="card">				
						<div class="card-header">
							<h3 class="card-title">Location Finder </h3>													
						</div>	
						<div class="card-body gllpLatlonPicker">
												
							<div class="row p-2">
								<div class="col-2">
									<label class="col-form-label">City Name <span class="text-danger"> *</span></label>
								</div>
								<div class="col-6">
									<input type="text" class="form-control"  id="txt_address" name="txt_address" autofocus value=""/>	
								</div>
								<div class="col">
									<input class="btn btn-primary" id="btn_search" type="button" name="btn_search" value="Search" >
								</div>
							</div>
							
							<div class="row p-2">
								<div class="col-2">
									<label class="col-form-label">Latitude </label>
								</div>
								<div class="col-4">
									<input type="text" readonly name="latitude" id="latitude" class="form-control" value="">
								</div>
								<div class="col-2 ">
									<label class="col-form-label">Longitude </label>
								</div>
								<div class="col-4 text-left">
									<input type="text" readonly name="longitude" id="longitude" class="form-control" value="">
								</div>
							</div>
							
							<div class="row p-2">
								<div class="col-6">
									<label class="col-form-label"><b>Google Maps</b> </label>
								</div>
								<div class="col-6">
									<label class="col-form-label"><b>Open Street Map </b></label>
								</div>
							</div>
							<div class="row p-2">
								<div class="col-12">
									<span id="search_result"></span>
								</div>
								
							</div>
							
							<div class="row p-2">								
								<div class="col-6">	
									<div style="width:80%; height:400px" id="gmap"></div>
								</div>
								<div class="col-6">
									<div style="width:80%; height:400px" id="osm_map"></div>
								</div>
								
							</div>				
						</div>
						
					</div>
				</form>
			</div>
		</div>
		
    </div>
	</body>
    
	
	<script type="text/javascript">
		$(function() {					
			$('#btn_search').click(function()
			{
				//alert("Search");
				
				if( $('#txt_address').val() != '')
				{
					//alert("Has Value");
					var srch_add = $('#txt_address').val();
					
					$("#osm_map, #gmap").html("Loading...");
					
					$.ajax({
						type: "POST",
						url: "getMapData.php",
						dataType: 'json',						
						data: {"addr": srch_add,"srch_from":1},
						success: function (data) 
						{								
							if(data)
							{
								$('#latitude').val(data.latitude);
								$('#longitude').val(data.longitude);						  						  
								
								//console.log(data);
								//load openstreet map
								update_osm(data);
								//load Google map
								initMap(data);
								
							}
							else
							{
								$("#osm_map, #gmap").empty();	
								$('#latitude').val('');
								$('#longitude').val('');										
								alert("Invalid Location or Unable to Retreive Location Data");
							}
						}
					});
					
				}
				else					
				{
					alert("Please enter the City Name to search.."); 
					$('#txt_address').focus();
					return false;
				}
				
			});	
		});
	</script>
	
	
	<script type="text/javascript">		
		
		function initMap(data) 
		{
		
			var v_lat, v_lon =0;
			v_lat = parseFloat(data['latitude']);
			v_lon = parseFloat(data['longitude']);
						
			const location = { lat: v_lat, lng: v_lon };
		  
			const map = new google.maps.Map(document.getElementById("gmap"), {
					zoom: 10,
					center: location,
			});		  
			const marker = new google.maps.Marker({
					position: location,
					map: map,
			});
		}
				
			
		function update_osm(data) 
		{			
			$("#osm_map").empty();
			
			map = new OpenLayers.Map ("osm_map", {
				controls:[ 
				new OpenLayers.Control.Navigation(),
						   new OpenLayers.Control.PanZoomBar(),
						   new OpenLayers.Control.ScaleLine(),
						   new OpenLayers.Control.Permalink('permalink'),
						   new OpenLayers.Control.MousePosition(),                    
						   new OpenLayers.Control.Attribution()
						  ],
				projection: new OpenLayers.Projection("EPSG:900913"),
				displayProjection: new OpenLayers.Projection("EPSG:4326")
				} );
	 
			var mapnik = new OpenLayers.Layer.OSM("OpenStreetMap (Mapnik)");
			
			map.addLayer(mapnik);
	 
			var lonLat = new OpenLayers.LonLat( data['longitude'], data['latitude'] )
			  .transform(
					new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
					map.getProjectionObject() // to Spherical Mercator Projection
			  );				  
			map.setCenter (lonLat, 10);	 
		} 
		
	</script>
	
	<script src="https://maps.googleapis.com/maps/api/js?<?php echo $gmapkey; ?>" async> </script>
	
	<!--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAzzNHQF8BrVyL1y1g--Bt6VDTQfycNE6k" async> </script>-->
	

</html>