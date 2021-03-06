<?php
// If you need to debug, uncomment these four lines
// ini_set('display_errors', 1);
// ini_set('log_errors', 1);
// ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
// error_reporting(E_ALL);

/**
 * @todo Create a settings file that initializes everything.
 * @todo Extract code to a library, to avoid repeating code.
 * @todo Add feature for specifying reports, with their own settings and templates
 * @todo Cache API responses, so we don't reach the data limits on API calls
 *
 * Here we go.
 **/
session_start();
require_once dirname(__FILE__).'/lib/GoogleClientApi/Google_Client.php';
require_once dirname(__FILE__).'/lib/GoogleClientApi/contrib/Google_AdsenseService.php';
require_once dirname(__FILE__).'/gaphp.php';

$GAPHP->config( 'adsense' );
$client = $GAPHP->get_client( 'adsense' );

// $service = new Google_AdsenseService($client);

if (isset($_GET['logout'])) {
    unset($_SESSION['token_gadsense']);
    global $output_title, $output_body, $output_nav;
    $output_title = 'Adsense';
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
    $_SESSION['token_gadsense'] = $client->getAccessToken();
    header("Location: ".$GAPHP->get_option( 'script_uri' ));
    die;
}

if (isset($_SESSION['token_gadsense'])) { // extract token from session and configure client
    $token = $_SESSION['token_gadsense'];
    $client->setAccessToken($token);
}

if (!$client->getAccessToken()) { // auth call to google
    global $output_title, $output_body, $output_nav;
    $output_title = 'Adsense';
    $output_body = '<h1>Login with your Google account</h1>
        <p>When clicking on login, you are redirected to Google. Login with a Google account that has access to a Google Adsense account, otherwise an error will occur.</p><div class="alert alert-info">We do not store the login credentials nor the data being displayed. This is just a simple demo page.</div>';
    $output_body .= '<p>The following URL has to be registered in <a href="https://code.google.com/apis/console" target="_blank">Google API console</a>:</p>';
    $output_body .= '<p><pre>'.$GAPHP->get_option( 'script_uri' ).'</pre></p>';
    $output_nav = '<li><a href="'.$GAPHP->get_option( 'script_uri' ).'?login">Login</a></li>'."\n";
    include("output.php");
    die;
}

// accounts: https://developers.google.com/adsense/management/v1.1/reference/accounts
// report generation: https://developers.google.com/adsense/management/v1.1/reference/reports/generate
// metrics: http://code.google.com/apis/analytics/docs/gdata/v3/reference.html#metrics

$from = date('Y-m-d'); //, time()-2*24*60*60); // 2 days // => new data format available by Google '2011-06-24';
$to = date('Y-m-d'); // today

global $_params;
$_params[] = 'domain';
$_params[] = 'page views';
$_params[] = 'page CTR';
$_params[] = 'page RPM';
$_params[] = 'clicks';
$_params[] = 'earnings';

$optParams = array();
$optParams['dimension'] = array('DOMAIN_NAME');
$optParams['metric'] = array('PAGE_VIEWS', 'PAGE_VIEWS_CTR', 'PAGE_VIEWS_RPM', 'CLICKS', 'EARNINGS');

try {
    $result = $service->reports->generate($from, $to, $optParams);
    global $_params, $output_title, $output_body;
    $output_title = 'Adsense';
    $output_body = '<h1>Google Adsense Access demo</h1><p>The following domains are in your Google Adsense account</p><ul>';
    $output_nav = '<li><a href="'.$GAPHP->get_option( 'script_uri' ).'?logout">Logout</a></li>'."\n";
    foreach($result['rows'] as $row) {
        $output_body .= '<li>';
        foreach ($_params as $colNr => $column) {
            $output_body .= $column . ': ' . $row[$colNr] . ', ';
        }
        $output_body .= '</li>'."\n";
    }
    $output_body .= '</ul>';
    include("output.php");
} catch (Exception $e) {
	die('<html><body><h1>An error occured: ' . $e->getMessage()."\n </h1></body></html>");
}
