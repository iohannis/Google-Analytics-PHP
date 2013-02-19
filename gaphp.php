<?php

/**
 * Initialization of options and variables
 **/
if( !class_exists('GAPHP') ) {
	class GAPHP {
	  private $_config;
	  
	  /**
	   * Start the show
	   **/
	  function __construct()
	  {
		$this->config();
	  }
	  
	  /**
	   * Load the config file
	   **/
	  function config( $type = 'analytics' )
	  {
		try {
			require_once( dirname(__FILE__).'/config.php' );
			$this->_config = $config[$type];
			if( empty($this->_config) || in_array('INSERT HERE', $this->_config) || in_array('', $this->_config) ) { // Don't allow default or empty strings
				return false;
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
			$this->_config['script_uri'] = $scriptUri;
			return true;
		} catch (Exception $e) {
			die('<html><body><h1>An error occured: ' . $e->getMessage()."\n </h1></body></html>");
		}
	  }
	  
	  /**
	   * Get a config option.
	   * Public options are: 
       *		'application_name'
       *		'script_uri'
	   **/
	  function get_option( $option )
	  {
		try {
			if( ! $option ) {
				return false;
			}
			$public_options = array(
				'application_name'	=>	'application_name',
				'script_uri'	=>	'script_uri',
			);
			return ( in_array( $option, $public_options ) ) ? $public_options[$option] : false;
		} catch (Exception $e) {
			die('<html><body><h1>An error occured: ' . $e->getMessage()."\n </h1></body></html>");
		}
	  }
	  
	  /**
	   * Get the Google client
	   * @param string $type - 'analytics' (default) or 'adsense'
	   **/
	  function get_client( $type = 'analytics' )
	  {
		try {
			if( empty($this->_config) ) {
				if( ! $this->config($type) ) {
					return false; // Config was unsuccessful 
				}
			}
			
			$client = new Google_Client();
			$client->setAccessType( $this->_config['access_type'] );
			$client->setApplicationName( $this->_config['application_name'] );
			$client->setClientId( $this->_config['client_id'] );
			$client->setClientSecret( $this->_config['client_secret'] );
			$client->setRedirectUri( $this->_config['script_uri'] );
			$client->setDeveloperKey( $this->_config['api_key'] );
			return $client;
		} catch (Exception $e) {
			die('<html><body><h1>An error occured: ' . $e->getMessage()."\n </h1></body></html>");
		}
	  }
	}
}
if( class_exists('GAPHP') ) {
	$GAPHP = new GAPHP;
}