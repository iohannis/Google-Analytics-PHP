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

require_once dirname(__FILE__).'/gaphp.php';

$report = 'default';
$cachefile = 'cache/report-'.$report.'.html';
include('gaphp/top-cache.php'); 
$GAPHP->report( $report );
include('gaphp/bottom-cache.php'); 