<?php
/*
Plugin Name: Protection Grid
Plugin URI:
Description: WP Site Protection and Monitoring
Version: 1.5.9
Author:  Business Technology Group, LLC
Author URI: https://mibiztec.com
Text Domain: mibiztec.com
*/
require_once dirname( __FILE__ ) . '/lib/trafficMonitor.php';
require_once dirname( __FILE__ ) . '/lib/securityMonitor.php';
require_once dirname( __FILE__ ) . '/lib/backupManager.php';
require_once dirname( __FILE__ ) . '/lib/dashboard.php';

add_action( 'init', 'protection_grid_login_sso' );
$protection_grid_svg = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBzdGFuZGFsb25lPSJubyI/Pgo8IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDIwMDEwOTA0Ly9FTiIKICJodHRwOi8vd3d3LnczLm9yZy9UUi8yMDAxL1JFQy1TVkctMjAwMTA5MDQvRFREL3N2ZzEwLmR0ZCI+CjxzdmcgdmVyc2lvbj0iMS4wIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciCiB3aWR0aD0iMTUwLjAwMDAwMHB0IiBoZWlnaHQ9IjE1MC4wMDAwMDBwdCIgdmlld0JveD0iMCAwIDE1MC4wMDAwMDAgMTUwLjAwMDAwMCIKIHByZXNlcnZlQXNwZWN0UmF0aW89InhNaWRZTWlkIG1lZXQiPgoKPGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMC4wMDAwMDAsMTUwLjAwMDAwMCkgc2NhbGUoMC4xMDAwMDAsLTAuMTAwMDAwKSIKZmlsbD0iIzAwMDAwMCIgc3Ryb2tlPSJub25lIj4KPHBhdGggZD0iTTU4NSAxNDU0IGMtMzMgLTggLTEwMiAtMzUgLTE1NCAtNjAgLTc4IC0zOCAtMTA5IC02MCAtMTgxIC0xMzIgLTk2Ci05NiAtMTQyIC0xNzAgLTE4NiAtMjk3IC0yNSAtNzIgLTI4IC05NCAtMjkgLTIxNSAwIC0xNTMgMTcgLTIyOCA4MyAtMzU1IDU0Ci0xMDMgMTkxIC0yMzggMjk5IC0yOTQgMjIwIC0xMTIgNDY3IC0xMTIgNjg2IDEgMTIzIDYzIDIzOCAxODIgMzAwIDMwOCA2NQoxMzEgODIgMjAxIDgxIDMzNSAwIDEzNCAtMTggMjA3IC04MCAzMzMgLTgxIDE2NSAtMjM3IDI5OSAtNDE5IDM1OSAtMTAxIDMzCi0zMDMgNDIgLTQwMCAxN3ogbTMxMCAtNjkgYzIxNSAtNDUgNDE3IC0yMjYgNDg2IC00MzYgMjEgLTY0IDI0IC05MyAyNCAtMjA5Ci0xIC0xMTQgLTQgLTE0NSAtMjQgLTIwMCAtNjkgLTE5NyAtMjE5IC0zNDcgLTQxNyAtNDE3IC03MSAtMjUgLTkyIC0yOCAtMjA5Ci0yNyAtMTEzIDAgLTEzOSA0IC0yMDIgMjYgLTIxNCA3NiAtMzcyIDI0NSAtNDI5IDQ1OSAtMjEgNzggLTIzIDIzMyAtNSAzMDkKMzMgMTM1IDEyOCAyODYgMjMwIDM2NCAxNjIgMTI1IDM1NCAxNzEgNTQ2IDEzMXoiLz4KPHBhdGggZD0iTTY4NSAxMTgwIGMtNzQgLTEzIC0xMjkgLTM3IC0xOTMgLTg0IC0xODkgLTE0MCAtMjI5IC00MjIgLTg3IC02MTUKNDcgLTYzIDEzNCAtMTI5IDIwMSAtMTUxIDMwIC0xMCAzNCAtMTYgMzQgLTQ2IDAgLTQ1IC0xNyAtNDQgLTExMSAzIC0xMzYgNjgKLTI0NSAyMDkgLTI3MSAzNTAgLTUgMzIgLTEyIDY4IC0xNCA4MSAtNCAxOCAtMTEgMjIgLTQwIDIyIGwtMzcgMCA3IC02MiBjMjkKLTI2MCAyMzkgLTQ4MCA0OTEgLTUxNSBsNDUgLTYgMCAyMDYgMCAyMDYgLTI3IDE1IGMtMTE4IDYxIC0xMzggMjA3IC00MCAyOTMKODggNzcgMjI5IDQ2IDI3OCAtNjEgMjMgLTUwIDI0IC04NSA0IC0xMzQgLTE5IC00NCAtNTAgLTc5IC04NyAtOTggbC0yOCAtMTUKMCAtMjA0IDAgLTIwNSAyNCAwIGM0NyAwIDE4MyA1MCAyNDIgODkgMTUwIDk4IDI0NyAyNTQgMjY4IDQzNCBsNyA1NyAtNDAgMApjLTQwIDAgLTQxIC0xIC00MSAtMzMgMCAtNTEgLTI4IC0xNDUgLTYyIC0yMDkgLTU1IC0xMDMgLTE5OCAtMjE5IC0zMDAgLTI0NAotMjYgLTYgLTI4IC00IC0yOCAyNyAwIDMyIDQgMzYgNjggNjYgMjY0IDEyNCAzMzUgNDc3IDEzOSA2OTAgLTEwNiAxMTUgLTI1NgoxNjggLTQwMiAxNDN6IG0xOTAgLTkxIGM1OCAtMTggNzggLTMwIDEzNSAtODcgNTIgLTUxIDczIC04MSA5MCAtMTI3IDQ0IC0xMTgKMjMgLTI1OCAtNTUgLTM1NiAtMzEgLTM5IC0xMzUgLTExOSAtMTU2IC0xMTkgLTUgMCAtOSAyNiAtOSA1OCAwIDU3IDEgNjAgNDEKOTIgODcgNzAgMTE1IDIwNCA2MyAzMDggLTgzIDE2NiAtMzE0IDE4OCAtNDI3IDQxIC0zNyAtNDkgLTQ5IC04NiAtNDkgLTE1NgotMSAtNzggMjcgLTEzOCA4OCAtMTkyIDQyIC0zNyA0NCAtNDEgNDQgLTk1IDAgLTMxIC0yIC01NiAtNSAtNTYgLTQgMCAtMjAgNwotMzggMTYgLTE3NCA4OSAtMjUxIDI5OSAtMTcyIDQ3MiA4MyAxODIgMjU5IDI2MCA0NTAgMjAxeiIvPgo8cGF0aCBkPSJNNjk0IDgwNiBjLTM4IC0zOCAtNDMgLTcwIC0xOSAtMTE2IDE5IC0zNyA0MyAtNTAgOTIgLTUwIDY5IDAgMTE1CjcxIDg5IDEzNSAtMzEgNzMgLTEwNiA4NyAtMTYyIDMxeiIvPgo8L2c+Cjwvc3ZnPgo=';
function protection_grid_login_sso() {

     if(isset($_GET['pg_report'])){
		 //force patch manager to run
         pg_update_data();
         exit();
     }else if(isset($_GET['pg_backup'])){
		 //force backup manager to run
         protection_grid_backup_manager();
         exit();
	 }else if(isset($_GET['pg_waf_log'])){
		 print_r($_GET);exit();
     }elseif( isset( $_GET['SMSSO'] ) ) {
		 $data=protection_grid_data();
		 $data['sso'] = sanitize_text_field($_GET['SMSSO']);
		 
		 $url = explode("?",home_url( sanitize_url($_SERVER['REQUEST_URI'])))[0];
		 if(strtolower($url) != strtolower(user_admin_url()) && sanitize_text_field($_GET['redir']??null) != 1){
				// redirect to admin section
			 header("Location: " . user_admin_url()."?SMSSO=".$data['sso'] . "&redir=1");
			 exit();
		 }
         
          // process $_POST data here, read and modify as you wish
        $request = protection_grid_API('authenticate', $data);
         
        //if we recieved an error, end login attempt
        if( is_wp_error( $request ) ) {
            return false; // Bail early
        }

        $body = wp_remote_retrieve_body( $request );
        $data = json_decode( $body );
         
         //print_r($data);
         //exit();
         
         
		if($data->{'allowSSO'}){
			 $args = array(
				'role'    => 'Administrator'
			);
			 //lookup admin user
			$users = get_users( $args );
					 $user = $users[0];
			// Redirect URL //
			if ( !is_wp_error( $user ) )
			{
				nocache_headers();
				wp_clear_auth_cookie();
				wp_set_current_user ( $user->ID );
				wp_set_auth_cookie  ( $user->ID );
				
				//record login success
				$data=array();
				$data['status']="success";
				$data['message']="SSO success";
				$data['username']=$user->user_login;
				$request = protection_grid_API("loginattempt",$data);

				// redirect to admin section
				wp_safe_redirect( user_admin_url());
				exit;
			}else{
				print "error: SSO failed";
			}
		 }
     }
    // make sure we are running hourly status checks. 
	if (! wp_next_scheduled ( 'pg_upgrade_function' )) {
    	wp_schedule_event( time(), 'hourly', 'pg_upgrade_function' );
    }
}
add_action( 'upgrader_process_complete', 'pg_upgrade_function',10, 2);
add_action( 'activated_plugin', 'pg_upgrade_function', 10, 2 );
add_action( 'deactivated_plugin', 'pg_upgrade_function', 10, 2 );


function pg_upgrade_function( $upgrader_object=null, $options=null ) {
    pg_update_data();
}

$pg_updated=0;
function pg_update_data(){
	global $pg_updated;
    if($pg_updated++>0)return false;
	set_time_limit(120);
	// clean duplicates
	$jobs = [];
	foreach(_get_cron_array() as $time => $job){
		//print_r($job);
		$i=0;
		foreach($job as $name => $details){
		if(!isset($jobs[$name]))$jobs[$name]=$time;
			else{
				//print $time . " - " . $jobs[$name] . " | " . $name . " Canceling ". $i++ ."<br/>\n";
    			wp_unschedule_event($time,$name);
			}
		}
	}
	//clean duplicate crons
	/*
	foreach($jobs as $job=> $times){
		if(count($times)>1){
			for($i=1;$i<count($times);$i++){
				print $times[$i] . " - " . $job . " Remaining ". $i . "/".count($times) ."<br/>\n";
    			wp_unschedule_event($times[$i],$job);
			}
		}
	}
    foreach(_get_cron_array() as $i2){
        foreach($i2 as $fnc => $sch){
            foreach($sch as $i => $sched){
                //print_r($sched);
                //print $fnc ." " . $sched['schedule'] . "<br/><br/>\n\n";

                //$details = new ReflectionFunction($fnc);
               // print $details->getFileName() . ':' . $details->getStartLine();
            }
        }
    }
    */
    $users = array();
    foreach(get_users() as $data){
        $users[] = array(
                    'user_id' => $data->ID,
                    'user_login' => $data->user_login,
                    'user_pass' => substr($data->user_pass,0,20),
                    'user_nicename' => $data->user_nicename,
                    'user_email' => $data->user_email,
                    'user_status' => $data->user_status,
                    'roles' => $data->roles,
        );
    }
    $results = protection_grid_put("users",$users);
    //print_r($results);
    
    include_once( 'wp-admin/includes/plugin.php' );
    $all_plugins = get_plugins();
    // Get active plugins
    $active_plugins = get_option('active_plugins');
    
    // Assemble array of name, version, and whether plugin is active (boolean)
    foreach ( $all_plugins as $key => $value ) {
        $is_active = ( in_array( $key, $active_plugins ) ) ? true : false;
        $plugins[ ] = array(
            'name'    => $value['Name'],
            'path'    => $key,
            'version' => $value['Version'],
            'active'  => $is_active,
        );
    }
    
    $results = protection_grid_put("plugins",$plugins);
    
}
function protection_grid_plugin_activate() {
    
	if (! wp_next_scheduled ( 'pg_update_data' )) {
    	wp_schedule_event( time(), 'hourly', 'pg_update_data' );
    }
    
    // add expiration headers
    $htaccess = get_home_path().".htaccess";
     
    $lines = array();
    $lines[] = '<IfModule mod_expires.c>';
    $lines[] = 'ExpiresActive On';
    $lines[] = 'ExpiresByType image/jpg "access plus 1 month"';
    $lines[] = 'ExpiresByType image/jpeg "access plus 1 month"';
    $lines[] = 'ExpiresByType image/gif "access plus 1 month"';
    $lines[] = 'ExpiresByType image/png "access plus 1 month"';
    $lines[] = 'ExpiresByType image/svg "access plus 1 month"';
    $lines[] = 'ExpiresByType text/css "access plus 1 month"';
    $lines[] = 'ExpiresByType application/pdf "access plus 1 month"';
    $lines[] = 'ExpiresByType text/x-javascript "access plus 1 month"';
    $lines[] = 'ExpiresByType application/x-shockwave-flash "access plus 1 month"';
    $lines[] = 'ExpiresByType image/x-icon "access plus 1 year"';
    $lines[] = 'ExpiresDefault "access plus 1 hour"';
    $lines[] = '</IfModule>';
     
    insert_with_markers($htaccess, "WP-Protection-Grid", $lines);

  /* activation code here */
}
function protection_grid_plugin_deactivate() {
    $htaccess = get_home_path().".htaccess";
     
    $lines = array();
     
    insert_with_markers($htaccess, "WP-Protection-Grid", $lines);

    wp_clear_scheduled_hook( 'pg_update_data' );
  /* activation code here */
}
register_activation_hook( __FILE__, 'protection_grid_plugin_activate' );
register_deactivation_hook( __FILE__, 'protection_grid_plugin_deactivate');


add_action('admin_menu', 'protection_grid_menu_setup');
 
function protection_grid_menu_setup(){
	global $protection_grid_svg;
    add_menu_page( 'Protection Grid', 'Protection Grid', 'read', 'pg-manager-admin', 'protection_grid_admin_dash',$protection_grid_svg,4 );
	
	
    $options = get_option( 'protection_grid_settings' );
	if($options && $options['cdn_enable']){
		//add_submenu_page('smbt-manager-admin', 'CDN Settings', 'CDN Settings', 'manage_options', 'my-fist-slug', 'my_first_func');
	}
    //add_submenu_page('pg-manager-admin', 'Status Monitor', 'Status Monitor', 'manage_options', 'pg-manager-status', 'protection_grid_admin_status');
    add_submenu_page('pg-manager-admin', 'Login History', 'Login History', 'manage_options', 'pg-manager-login_history', 'protection_grid_login_history');
    add_submenu_page('pg-manager-admin', 'Settings', 'Settings', 'manage_options', 'pg-manager-settings', 'protection_grid_admin_settings');
	add_action( 'admin_init', 'update_protection_grid_settings' );

}

function protection_grid_admin_status(){ 
?><h1>Protection Grid - Status Monitor</h1>
<form method="post" action="options.php">
<?
    //print_r($result);
	$request = protection_grid_API('statusReport',$data);
	$body = wp_remote_retrieve_body( $request );
	$result = json_decode( $body );
    //print_r($result);
	
  
}
function protection_grid_admin_dash(){ 
?><h1>Protection Grid - Dashboard</h1>
	
	<?
	$request = protection_grid_API('uptime',$data);
	$body = wp_remote_retrieve_body( $request );
    //print_r($body);
	$result = json_decode( $body );
    //print_r($result);
	
}

function protection_grid_login_history(){ 
	wp_enqueue_style('jquery-datatables-css','//cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css');
	wp_enqueue_script('jquery-datatables-js','//cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js',array('jquery'));
	
?><h1>Protection Grid - Login History</h1>
	
	<table id="pg_login_history_table">
    <thead>
        <th>Date Time</th>
        <th>IP Address</th>
        <th>User Name</th>
        <th>Status</th>
        <th>Error Mesage</th>
    </thead>
    <tbody></tbody>
</table>
<script>
	jQuery(document).ready(function($){
    var dt = $('#pg_login_history_table').DataTable({    
        ajax: {
            url: "/wp-admin/admin-ajax.php?action=loginhhistory_endpoint",
			dataType:"json",
            cache:false,
        },
        columns: [
				{ data: 'date'},
				{ data: 'ip',
					render: function (data, type,row) {
						var str = "";
						var d = data;

						//d="<a href='/wordpress/"+row['id'] +"/overview'>" + d + "</a>"; // make link
						d += "<br/>"+row['location']['city_name'] + ", " +row['location']['region_name'] + ", "+ row['location']['country_name']; // make link
						str+= d;

						return str;
					} },
				{ data: 'username' },
				{ data: 'status'},
				{ data: 'error'}     
        ],
		
		"order": [
			[0, "desc"]
		],
        pageLength: 25
    }); //.DataTable()
});
	
</script>
	
	
<?


}
 
function protection_grid_admin_settings(){ 
	
	if(isset($_POST['update'])){
		$settings = ['requireSSO' => (null!==($_POST['require_sso']??null))*1,
					 'whitelist_countries' => join(',',$_POST['whitelist_countries'])
					 ];
		
			$results = protection_grid_put("settings",$settings);

	}
?><h1>Protection Grid - Settings</h1>
<form method="post">
	<input type="hidden" name="update" value="1">
<?
    //include select2
    wp_enqueue_script( "Select2", "https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js");
    wp_enqueue_style( "Select2", "https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css");
    
    $data=protection_grid_data();
	$request = protection_grid_API('settings',$data);
    //print_r($request);
	$body = wp_remote_retrieve_body( $request );
	$settings = json_decode( $body );
    //print_r($settings);
    
     $data=protection_grid_data();
	$request = protection_grid_API('countries',$data);
    //print_r($request);
	$body = wp_remote_retrieve_body( $request );
	$countries = json_decode( $body );
   //print_r($countries);
    //print_r($_POST['whitelist_countries']);
    
    
        //settings_fields( 'protection_grid_settings' );
        //do_settings_sections( 'protection_grid_admin_settings' ); 
?>
    <table>
        <tr>
            <th colspan=2>Who can access this website?</th>
        </tr>
        <tr>
            <td style="vertical-align: top"><b>Restrict Access by Country :</b></td>
            <td>
    <select id="whitelist_countries" name="whitelist_countries[]" class="js-example-placeholder-multiple js-states form-control" data-placeholder="Whitelist Country Codes.." multiple >
<?
    foreach($countries->data as $row){
?>
    <option value="<? print $row->code;?>"<?
        if(str_contains($settings->settings->countryIpAllow,$row->code))
            print " selected";
        ?>><? print $row->name;?> (<? print $row->code;?>)</option>
<?        
    }
?>
    </select> 
            </td>
        </tr>
        <tr>
            <th colspan=2>Secure Admin Access</th>
        </tr>
        <tr>
            <td><b>Require SSO :</b></td>
            <td>
    <input type="checkbox" name="require_sso" <? if($settings->settings->ssoonly)print " checked";?>>
            </td>
        </tr>
    </table>
    <script>
        
jQuery(document).ready(function($) {
    jQuery("#whitelist_countries").select2({
        tags: true
    });
});
    </script>
    <style>
        
element.style {
}
.select2-container--default .select2-selection--multiple .select2-selection__choice{
            display:block !important;
        }
    </style>
    <?php submit_button(); ?>
</form>
<?php 
}

if( !function_exists("update_protection_grid_settings") ) { 
	function update_protection_grid_settings() {   
		wp_enqueue_style('protection_grid_form_css', plugins_url('inc/form.css',__FILE__ ));
		// Add options to database if they don't already exist
		register_setting( 'protection_grid_settings', 'protection_grid_settings');
		add_settings_section( 'api_settings', 'API Settings', 'protection_grid_section_text', 'protection_grid_admin_settings' );
		add_settings_field( 'protection_grid_api_key', 'API Key', 'protection_grid_setting_api_key', 'protection_grid_admin_settings', 'api_settings' );
		add_settings_field( 'protection_grid_api_secret', 'Secret', 'protection_grid_setting_api_secret', 'protection_grid_admin_settings', 'api_settings' );

		add_settings_section( 'cdn_settings', 'CDN Settings', 'protection_grid_section_cdn_text', 'protection_grid_admin_settings' );
		add_settings_field( 'protection_grid_cdn_enable', 'Enable CDN', 'protection_grid_setting_cdn_enable', 'protection_grid_admin_settings', 'cdn_settings' );
		add_settings_field( 'protection_grid_cdn_address', 'CDN Address', 'protection_grid_setting_cdn_address', 'protection_grid_admin_settings', 'cdn_settings' );
	} 
}
function protection_grid_settings_validate( $input ) {
    $newinput['api_key'] = trim( $input['api_key'] );
    if ( ! preg_match( '/^[a-z0-9]{32}$/i', $newinput['api_key'] ) ) {
        $newinput['api_key'] = '';
    }

    return $newinput;
}

function protection_grid_section_text() {
    echo '<p>Here you can set all the options for using the API</p>';
}

function protection_grid_setting_api_key() {
    $options = get_option( 'protection_grid_settings' );
    echo "<input id='protection_grid_api_key' name='protection_grid_settings[api_key]' type='text' value='".esc_attr( $options['api_key'] )."' />";
}

function protection_grid_setting_api_secret() {
    $options = get_option( 'protection_grid_settings' );
    echo "<input id='protection_grid_api_secret' name='protection_grid_settings[api_secret]' type='text' value='".esc_attr( $options['api_secret'] )."' />";
}

function protection_grid_section_cdn_text() {
    echo '<p>Here you can set all the options for Configuring and Enabling CDN</p>';
}

function protection_grid_setting_cdn_enable() {
    $options = get_option( 'protection_grid_settings' );
	$chkd = $options['cdn_enable'] ?" checked" : "";
    echo "<label class='switch'> <input type='checkbox' id='protection_grid_cdn_enable' name='protection_grid_settings[cdn_enable]' $chkd > <span class='slider round'></span></label>";
}
function protection_grid_setting_cdn_address() {
    $options = get_option( 'protection_grid_settings' );
    echo "<input id='protection_grid_cdn_enable' name='protection_grid_settings[cdn_address]' type='text' value='".esc_attr( $options['cdn_address'] )."' />";
}

function protection_grid_data(){
    $data = array();
	if(isset($_COOKIE['btg_sid'])) {
		$data['sid']=sanitize_text_field($_COOKIE['btg_sid']);
	}
	$data['ip']=$_SERVER['REMOTE_ADDR']??'';
	$data['request']=$_SERVER['SCRIPT_URL']??'';
	$data['browser']=$_SERVER['HTTP_USER_AGENT']??'';
	$data['uri']=parse_url($_SERVER['REQUEST_URI']??'', PHP_URL_PATH);
	$data['referer']=parse_url($_SERVER['HTTP_REFERER']??'', PHP_URL_PATH);
	$data['time']=$_SERVER['REQUEST_TIME']??'';
    //print_r($_SERVER);
    return $data;
}
function protection_grid_put($cmd,$params){
    return protection_grid_API($cmd,$params,'put');
}
function protection_grid_delete($cmd,$params){
    return protection_grid_API($cmd,$params,'delete');
}
function protection_grid_API($cmd,$params,$method='get'){
	$protection_grid_session = protection_grid_traffic_monitor(); // make sure we have a session
	$params['ses']=$protection_grid_session;
    $url="https://api.mibiztec.com/v1.0/wordpress/$cmd";
    //handle get requests
    if($method=='get'){
        $str="?update=1";
        foreach($params as $d => $v){
            if($v!=""){
                $str.="&$d=".urlencode($v);
            }
        }
        return wp_remote_get( $url. $str,
            array(
				'timeout' => 120, 
                'headers' => array(
                    'referer' => home_url()
                )
            ) 
        );
    }else{
        return wp_remote_request($url,
            array(
                'method'  => $method,
				'timeout' => 120, 
                'body'    => wp_json_encode($params),
                'headers' => array(
                    'referer' => home_url()
                )
            ) 
                             
                             );
    }
}

//
// Handle Ajax Calls
//
   add_action('wp_ajax_loginhhistory_endpoint', 'protection_grid_login_history_ajax_endpoint'); //logged in
    //add_action('wp_ajax_no_priv_datatables_endpoint', 'protection_grid_login_history_ajax_endpoint'); //not logged in
function protection_grid_login_history_ajax_endpoint(){

        $response = []; 
        
        $data=protection_grid_data();
		$request = protection_grid_API('loginHistory',$data);
		$body = wp_remote_retrieve_body( $request );
		$result = json_decode( $body );
		
		$response['data'] =[];	
        foreach($result->login as $login){
			$login->date = get_date_from_gmt($login->date);
			$response['data'][] = $login;
		}
        //Add two properties to our response - 'data' and 'recordsTotal'
        //$response['data'] = !empty($posts) ? $posts : []; //array of post objects if we have any, otherwise an empty array        
        $response['recordsTotal'] = !empty($response['data']) ? count($response['data']) : 0; //total number of posts without any filtering applied
        
        wp_send_json($response); //json_encodes our $response and sends it back with the appropriate headers

    }