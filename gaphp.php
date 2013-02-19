<?php

/**
 * Initialization of settings and variables
 **/
 
class GAPHP {
  private static $config;
  
  /**
   * Start the show
   **/
  static function init()
  {
	self::get_config();
  }
  
  /**
   * Load the config file
   **/
  static function get_config()
  {
	require_once dirname(__FILE__).'config.php';
    self::$config = $config;
	if( in_array('INSERT HERE', $config) || in_array('', $config) ) { // Don't allow default or empty strings
		return false;
	}
	return true;
  }
  
  /**
   * Get the Google client
   **/
  static function get_client( $type = 'analytics' )
  {
	if( empty(self::$config) ) {
		if( ! self::get_config() ) {
			return false; // Config was unsuccessful 
		}
	}
	
	// Identify the protocol
	if (isset($_SERVER['HTTPS']) &&
		($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
		isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
		$_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
	  $protocol = 'https://';
	}
	else {
	  $protocol = 'http://';
	}
	$scriptUri = $protocol.$_SERVER["HTTP_HOST"].$_SERVER['PHP_SELF'];
	
	$client = new Google_Client();
	$client->setAccessType( self::$config['access_type'] );
	$client->setApplicationName( self::$config['application_name'] );
	$client->setClientId( self::$config['client_id'] );
	$client->setClientSecret( self::$config['client_secret'] );
	$client->setRedirectUri($scriptUri);
	$client->setDeveloperKey( self::$config['api_key'] );
	return $client;
  }
}
GAPHP::init();