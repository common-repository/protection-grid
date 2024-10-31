<?
/*
Plugin Name: Protection Grid
Module Name: Security Monitor
Description: Add security messures to WP infrustructure
Version: 1.5.3
Author:  Business Technology Group, LLC
Author URI: https://mibiztec.com
Text Domain: mibiztec.com
*/

if (!defined('ABSPATH')) {
    exit;
}
//initialize the inline-WAF
add_action( 'init', 'protection_grid_htaccess' );
// disable xmlrpc by default
add_filter('xmlrpc_enabled', '__return_false');
add_action('validate_password_reset', 'custom_validate_password_reset', 10, 3);
add_action('password_reset', 'protection_grid_log_password_reset', 10, 2);
add_action('after_password_reset', 'protection_grid_log_password_reset', 10, 2);
add_action( 'profile_update', 'protection_grid_log_password_reset', 10, 2 );


if (!function_exists('getallheaders')) {
    function getallheaders() {
		$allHeaders = array();
		foreach($_SERVER as $name => $value) {
			if($name != 'HTTP_MOD_REWRITE' && (substr($name, 0, 5) == 'HTTP_' || $name == 'CONTENT_LENGTH' || $name == 'CONTENT_TYPE')) {
				$name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', str_replace('HTTP_', '', $name)))));
				$allHeaders[$name] = $value;
			}
		}
    return $allHeaders;
    }
}
//protection_grid_waf();


function protection_grid_log_password_reset($user, $new_pass) {
	//print_r($user);
	$u_data = get_user_by( 'id', $user );
	// user email. $user return full user info
	$email = $user->data->user_email;
	// new entered password (plain text)
	
	$password = $new_pass;
	
	$data['status']="update";
	if($user->ID != $u_data->ID)$data['userid']=$u_data->ID;
	if($user->user_login != $u_data->user_login)$data['username']=$u_data->user_login;
	if($user->user_nicename != $u_data->user_nicename)$data['user_nicename']=$u_data->user_nicename;
	if($user->user_email != $u_data->user_email)$data['user_email']=$u_data->user_email;
	if($user->user_pass != $u_data->user_pass)$data['pass']=substr($u_data->user_pass,0,15);
	// log password reset
	/*print_r($data);
	print "<br/>";
	print_r( $u_data );
	print "<br/>";
	print_r($new_pass);
	*/
	//exit();
	$request = protection_grid_API("passwordreset",$data);
	//print_r($request);exit();
	// Do whatever you want...
}
function protection_grid_htaccess(){
    // add expiration headers
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/misc.php';
    $htaccess = get_home_path().".htaccess";
     
    $lines = array();
	$lines[] = '<IfModule mod_rewrite.c>';
	$lines[] = 'RewriteEngine On';
    $lines[] = 'RewriteCond %{REQUEST_URI} (\.\.\/?\\?) [NC]
RewriteRule .* /?pg_waf_log=10&pg_waf_file=%{PATH_INFO}&pg_waf_type=directory_traversal [QSA,L,R=302,NE]
';
	$lines[] = '</IfModule>';
    insert_with_markers($htaccess, "WP-Protection-Grid-WAF", $lines);
	
}    
function protection_grid_waf() {

$malicious_patterns = [
    // Cross-site scripting (XSS)
    '/<script\b[^>]*>([\s\S]*?)<\/script>/i' => 'Cross-site scripting (XSS)',
    '/<img\b[^>]*src=["\']javascript:([^"\']+)["\']/i' => 'Cross-site scripting (XSS)',
    '/<iframe\b[^>]*src=["\']javascript:([^"\']+)["\']/i' => 'Cross-site scripting (XSS)',
    '/<body\b[^>]*onload=["\']([^"\']+)["\']/i' => 'Cross-site scripting (XSS)',
    '/<a\b[^>]*href=["\']javascript:([^"\']+)["\']/i' => 'Cross-site scripting (XSS)',
    '/<input\b[^>]*onfocus=["\']([^"\']+)["\']/i' => 'Cross-site scripting (XSS)',
    '/<div\b[^>]*onmouseover=["\']([^"\']+)["\']/i' => 'Cross-site scripting (XSS)',
    '/<button\b[^>]*onclick=["\']([^"\']+)["\']/i' => 'Cross-site scripting (XSS)',
    '/<form\b[^>]*action=["\']javascript:([^"\']+)["\']/i' => 'Cross-site scripting (XSS)',
    '/<link\b[^>]*rel=["\']stylesheet["\'][^>]*href=["\']javascript:([^"\']+)["\']/i' => 'Cross-site scripting (XSS)',
    '/<meta\b[^>]*http-equiv=["\']refresh["\'][^>]*content=["\'][^"\']+;url=javascript:([^"\']+)["\']/i' => 'Cross-site scripting (XSS)',
    '/<style\b[^>]*>@import ["\']javascript:([^"\']+)["\']/i' => 'Cross-site scripting (XSS)',
    '/<object\b[^>]*data=["\']javascript:([^"\']+)["\']/i' => 'Cross-site scripting (XSS)',
    '/<embed\b[^>]*src=["\']javascript:([^"\']+)["\']/i' => 'Cross-site scripting (XSS)',
    '/<applet\b[^>]*code=["\']javascript:([^"\']+)["\']/i' => 'Cross-site scripting (XSS)',
    '/<base\b[^>]*href=["\']javascript:([^"\']+)["\']/i' => 'Cross-site scripting (XSS)',
    '/<textarea\b[^>]*onfocus=["\']([^"\']+)["\']/i' => 'Cross-site scripting (XSS)',
    '/<svg\b[^>]*onload=["\']([^"\']+)["\']/i' => 'Cross-site scripting (XSS)',
    '/<marquee\b[^>]*onstart=["\']([^"\']+)["\']/i' => 'Cross-site scripting (XSS)',
    '/<bgsound\b[^>]*src=["\']javascript:([^"\']+)["\']/i' => 'Cross-site scripting (XSS)',

    // Other existing patterns (previously listed)
    '/\.\.\/\.\.\//' => 'Directory traversal',
    '/base64_decode/i' => 'Base64 encoded input',
    '/eval\s*\(/i' => 'Eval function usage',
    '/system\s*\(/i' => 'System function usage',
    '/shell_exec/i' => 'Shell execution',
  //  '/http(s)?:\/\/.*\/.*(\.php|\.txt|\.html|\.js)/i' => 'Remote File Inclusion (RFI)',
    '/(\.\.\/)+/i' => 'Local File Inclusion (LFI)',
    '/;.*(wget|curl|nc|perl|python|ruby|sh|bash)\s/i' => 'Command Injection',
    '/\|\|.*(wget|curl|nc|perl|python|ruby|sh|bash)\s/i' => 'Command Injection',
    '/&&.*(wget|curl|nc|perl|python|ruby|sh|bash)\s/i' => 'Command Injection',
    '/action=.*(delete|update|edit|add)/i' => 'Cross-Site Request Forgery (CSRF)',
    '/content-type:.*(image|pdf|text|plain)/i' => 'File Upload Vulnerabilities',
    '/wp-config\.php/i' => 'Accessing wp-config.php',
    '/wp-admin\/admin-ajax\.php/i' => 'Exploits targeting admin-ajax.php',
    '/(?!\/wp-admin)author=\d+/i' => 'User Enumeration',
    '/\.env/i' => 'Accessing .env files',
    '/xmlrpc\.php/i' => 'XML-RPC attacks',
    // SQL Injection patterns
    '/\bselect\b.*\bfrom\b/i' => 'SQL Injection',
    '/\bunion\b.*\bselect\b/i' => 'SQL Injection',
    '/\binsert\b.*\binto\b/i' => 'SQL Injection',
    '/\bupdate\b.*\bset\b/i' => 'SQL Injection',
    '/\bdelete\b.*\bfrom\b/i' => 'SQL Injection',
    '/\bdrop\b.*\btable\b/i' => 'SQL Injection',
    '/\balter\b.*\btable\b/i' => 'SQL Injection',
	
    '/\btruncate\b.*\btable\b/i' => 'SQL Injection',
    '/\bcreate\b.*\btable\b/i' => 'SQL Injection',
    '/\bshow\b.*\btables\b/i' => 'SQL Injection',
    '/\bdescribe\b.*\btable\b/i' => 'SQL Injection',
    '/\bgrant\b.*\ball\b/i' => 'SQL Injection',
    '/\brevoke\b.*\ball\b/i' => 'SQL Injection',
    '/\bexec\b.*\bsp_executesql\b/i' => 'SQL Injection',
    '/\bload_file\b/i' => 'SQL Injection',
    '/\boutfile\b/i' => 'SQL Injection',
    '/\binto\b.*\boutfile\b/i' => 'SQL Injection',
    '/\bselect\b.*\bnull\b/i' => 'SQL Injection',
    '/\bselect\b.*\b1,1\b/i' => 'SQL Injection',
    '/\bor\b.*\b1=1\b/i' => 'SQL Injection',
    // Header manipulation patterns
    '/.*(Proxy|proxy)-Connection:.*keep-alive/i' => 'Header Manipulation',
    '/.*X-Forwarded-For:.*[0-9]{1,3}(\.[0-9]{1,3}){3}/i' => 'Header Manipulation',
    '/.*X-Forwarded-Host:.*example\.com/i' => 'Header Manipulation',
    '/.*X-Forwarded-Proto:.*https/i' => 'Header Manipulation',
    '/.*X-HTTP-Method-Override:.*DELETE/i' => 'Header Manipulation',
    '/.*Referer:.*evil\.com/i' => 'Header Manipulation',
    '/.*User-Agent:.*sqlmap/i' => 'Header Manipulation',
    '/.*User-Agent:.*acunetix/i' => 'Header Manipulation',
    '/.*User-Agent:.*nikto/i' => 'Header Manipulation',
    '/.*User-Agent:.*w3af/i' => 'Header Manipulation',
    '/.*User-Agent:.*sqlninja/i' => 'Header Manipulation',
    '/.*Authorization:.*Basic\s[a-zA-Z0-9+\/=]+/i' => 'Header Manipulation',
    '/.*Cookie:.*wordpress_logged_in_[a-zA-Z0-9]+/i' => 'Header Manipulation',
    '/.*Host:.*localhost/i' => 'Header Manipulation',
    '/.*Host:.*127\.0\.0\.1/i' => 'Header Manipulation',
    '/.*Content-Type:.*application\/json/i' => 'Header Manipulation',
    '/.*Content-Type:.*text\/plain/i' => 'Header Manipulation',
    '/.*Connection:.*keep-alive/i' => 'Header Manipulation',
    '/.*Cache-Control:.*no-cache/i' => 'Header Manipulation',
    '/.*Accept-Encoding:.*gzip, deflate/i' => 'Header Manipulation',
];

	// Check GET, POST, and REQUEST data
	if ($description = protection_grid_malicious_patterns($_GET, $malicious_patterns)) {
		//print "GET : ";
		block_request($description);
	}else if ($description = protection_grid_malicious_patterns($_POST, $malicious_patterns)) {
		//print "POST : ";
		block_request($description);
	}else if ($description = protection_grid_malicious_patterns($_REQUEST, $malicious_patterns)) {
		//print "REQUEST : ";
		block_request($description);
	}
	// Check HTTP headers
	foreach (getallheaders() as $name => $value) {
		if ($description = protection_grid_malicious_patterns($value, $malicious_patterns)) {
		print "$name : $value";
			block_request($description);
		}
	}
}

// Function to log malicious attempts
function protection_grid_log_attempt($type, $data, $description) {
    $log = '[' . date('Y-m-d H:i:s') . '] ' . $type . ': ' . json_encode($data) . ' - Description: ' . $description . "\n";
    file_put_contents('waf_log.txt', $log, FILE_APPEND);
}

// Function to check request data against malicious patterns
function protection_grid_malicious_patterns($data, $patterns) {
    foreach ($patterns as $pattern => $description) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_string($value) && preg_match($pattern, $value)) {
                    protection_grid_log_attempt('Request', $data, $description);
                    return $description;
                }
            }
        } else {
            if (is_string($data) && preg_match($pattern, $data)) {
                protection_grid_log_attempt('Request', $data, $description);
                return $description;
            }
        }
    }
    return false;
}
// Function to block requests
function block_request($description = "") {
    header('HTTP/1.1 403 Forbidden');
    echo '403 Forbidden - ' . $description;
    exit;
}

if ( !function_exists('wp_authenticate') ) :
function wp_authenticate($username, $password) {
    $username = sanitize_user($username);
    $password = trim($password);

	$data=array();
	$request = protection_grid_API("bruteforcecheck",$data);
	if( is_wp_error( $request ) ) {
		return false; // Bail early
	} 

	$body = wp_remote_retrieve_body( $request );
	$rdata = json_decode( $body );
	
	// if BruteForce check fails, do not try to login
	if($rdata->{'settings'}->{'ssoonly'} == 1){
		$data['status']="fail";
		$data['error']="blocked-sso";
		$user = new WP_Error('loginblocked', __('<strong>ERROR</strong>: Login disabled'));
	// if BruteForce check fails, do not try to login
	}elseif(!$rdata->{'allowLogin'}){
		$data['status']="fail";
		$data['error']="blocked-bruteforce";
		$user = new WP_Error('bruteforceblock', __('<strong>ERROR</strong>: Login attempts exceeded. Please try again in 10 minutes.'));
		
		// Try to login
	}else{
		if($rdata->{'loginsRemaining'} < $rdata->{'loginAtemptsAllowed'}){
			$attempts = new WP_Error('authentication_failed', __('<strong>WARNING</strong>: '.$rdata->{'loginsRemaining'}.' Login attempts remaining'));
		}
		//try to actually login
		$user = apply_filters('authenticate', null, $username, $password);

		if ( $user == null ) {
			// TODO what should the error message be? (Or would these even happen?)
			// Only needed if all authentication handlers fail to return anything.
			$user = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.'));
		}

		$ignore_codes = array('empty_username', 'empty_password');

		// Login Failed
		if (is_wp_error($user) && !in_array($user->get_error_code(), $ignore_codes) ) {
			do_action('wp_login_failed', $username);

			$data['status']="fail";
			$data['error']=$user->get_error_code();
			// login successful
		}elseif($username!="" && $password!=""){
			$data['status']="success";
		}
	}
	if(isset($data['status'])){
		$data['username']=$username;
		
		// record logn attempt
		$request = protection_grid_API("loginattempt",$data);
	}	

    return $user;
}
endif;

