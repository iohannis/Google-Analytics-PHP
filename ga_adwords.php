<?php
// If you need to debug, uncomment these four lines
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
error_reporting(E_ALL);

/**
 * @todo Create a settings file that initializes everything.
 * @todo Make the index.php a wrapper file to include everything else.
 * @todo Extract code to a library, to avoid repeating code.
 * @todo Add feature for specifying reports, with their own settings and templates
 * @todo Cache API responses, so we don't reach the data limits on API calls
 *
 * Here we go.
 **/
session_start();
require_once dirname(__FILE__).'/lib/GoogleClientApi/Google_Client.php';
require_once dirname(__FILE__).'/lib/GoogleClientApi/contrib/Google_AnalyticsService.php';
require_once dirname(__FILE__).'/gaphp.php';

 $client = $GAPHP->get_client( 'analytics' );

// $service = new Google_AnalyticsService($client);

if (isset($_GET['logout'])) {
    unset($_SESSION['token_ganalytics']);
    global $output_title, $output_body, $output_nav;
    $output_title = 'Adwords';
    $output_body = '<h1>You have been logged out.</h1>';
    $output_nav = '<li><a href="'.$GAPHP->get_option( 'script_uri' ).'?login">Login</a></li>'."\n";
    include("output.php");
    die;
}

if (isset($_GET['login'])) {
    $authUrl = $client->createAuthUrl();
    header("Location: ".$authUrl);
}

if (isset($_GET['code'])) { // we received the positive auth callback, get the token and store it in session
    $client->authenticate();
    $_SESSION['token_ganalytics'] = $client->getAccessToken();
	setcookie('token_ganalytics', $_SESSION['token_ganalytics'], strtotime( '+30 days' ), '/', $_SERVER['HTTP_HOST'], true); // Secure cookie for SSL connections
    header("Location: ".$GAPHP->get_option( 'script_uri' ));
    die;
}

if (isset($_SESSION['token_ganalytics']) || isset($_COOKIE['token_ganalytics'])) { // extract token from session and configure client
    $token = ( isset($_SESSION['token_ganalytics']) ) ? $_SESSION['token_ganalytics'] : $_COOKIE['token_ganalytics'];
    $client->setAccessToken($token);
}

if ( ! $google_access_token = $client->getAccessToken() ) { // auth call to google
    global $output_title, $output_body, $output_nav;
    $output_title = 'Adwords';
    $output_body = '<h1>Login with your Google account</h1><p>When clicking on login, you are redirected to Google. Login with a Google account that has access to a Google Adsense account, otherwise an error will occur.</p><div class="alert alert-info">We do not store the login credentials nor the data being displayed. This is just a simple demo page.</div>';
    $output_nav = '<li><a href="'.$GAPHP->get_option( 'script_uri' ).'?login">Login</a></li>'."\n";
    include("output.php");
    die;
} 

$report = 'list-profiles';
$cachefile = 'cache/report-'.$report.'.html';
include('gaphp/top-cache.php'); 
$GAPHP->report( $report );
include('gaphp/bottom-cache.php'); 

// http://code.google.com/apis/analytics/docs/mgmt/v3/mgmtReference.html#collection_webproperties
// try {
    // global $_params, $output_title, $output_body;
    // $output_title = 'Adwords';
    // $output_nav = '<li><a href="'.$GAPHP->get_option( 'script_uri' ).'?logout">Logout</a></li>'."\n";
    // $output_body = '<h1>Google Adwords Access demo</h1>
                    // <p>The following domains are in your Google Adwords account</p><ul>';
    // $props = $service->management_webproperties->listManagementWebproperties("~all");
    // foreach($props['items'] as $item) {
        // $output_body .= sprintf('<li><a href="%2$s" target="_blank" class="report-item link">%1$s</a></li>', $item['name'], $item['websiteUrl'] );
        // $output_body .= '<!-- ';
        // $output_body .= print_r($item, true);
        // $output_body .= ' -->';
    // }
    // $output_body .= '</ul>';
    // include("output.php");
// } catch (Exception $e) {
	// die('<html><body><h1>An error occured: ' . $e->getMessage()."\n </h1></body></html>");
// }