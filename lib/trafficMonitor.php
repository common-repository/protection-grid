<?
/*
Plugin Name: Protection Grid
Module Name: Traffic Monito
Description: Track traffic and block unallowed access
Version: 1.3.11
Author:  Business Technology Group, LLC
Author URI: https://mibiztec.com
Text Domain: mibiztec.com
*/
$protection_grid_session = null;
$protection_grid_trafficRecorded = false;
 
add_action( 'init', 'protection_grid_traffic_monitor' );
function protection_grid_traffic_monitor() {
	global $protection_grid_trafficRecorded;
	if($protection_grid_trafficRecorded)return $GLOBALS['protection_grid_session'];
	
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	
	$data = protection_grid_data();
	$str="?update=1";
	foreach($data as $d => $v){
		if($v!=""){
			$str.="&$d=".urlencode($v);
		}
	}
    $request = wp_remote_get("https://api.mibiztec.com/v1.0/wordpress/traffic$str",
        array($data,
            'headers' => array(
                'referer' => home_url()
            )
        ) 
    );
	if( is_wp_error( $request ) ) {
		return false; // Bail early
	}

	$body = wp_remote_retrieve_body( $request );
	$data = json_decode( $body );
	//print_r($request); print  $body;
	$GLOBALS['protection_grid_session']= $data->{'session_token'};
	setcookie('btg_sid', $data->{'session_token'}, time()+31556926);
	$protection_grid_trafficRecorded = true;
	
	//check for allowed country
	if(isset($data->{'settings'}->{'countryIpAllow'}) && $data->{'settings'}->{'countryIpAllow'} !="" && $data->{'settings'}->{'countryIpAllow'} !="all"){
		if(!in_array($data->{'country'},explode(",",$data->{'settings'}->{'countryIpAllow'}))){
			
			// traffic blocked by country, kick site
			print "Traffic Blocked by Country: ". $data->{'country'} . " not on Allowed List";
			die();
		}
	}
	return $data->{'session_token'};
}