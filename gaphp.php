<?php

/**
 * Initialization of options and variables
 **/
if( !class_exists('GAPHP') ) {
	class GAPHP {
	  private $_config;
	  private $_client;
	  private $_type;
	  private $_service;
	  
	  /**
	   * Start the show
	   **/
	  function __construct()
	  {
		$this->config();
	  }
	  
	  /**
	   * Service getter and setter
	   **/
	  function service( $service = '' )
	  {
		try {
			if( $service === 'adsense' ) {
				$this->_service = new Google_AdsenseService($this->_client);
			}
			if( $service === 'analytics' ) {
				$this->_service = new Google_AnalyticsService($this->_client);
			}
			return $this->_service;
		} catch (Exception $e) {
			die('<html><body><h1>An error occured: ' . $e->getMessage()."\n </h1></body></html>");
		}
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
			$this->_type = $type;
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
			return ( in_array( $option, $public_options ) ) ? $this->_config[$option] : false;
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
			$this->_client = $client;
			$this->service($type);
			return $client;
		} catch (Exception $e) {
			die('<html><body><h1>An error occured: ' . $e->getMessage()."\n </h1></body></html>");
		}
	  }
	  
	  /**
	   * Get data for the Google client
	   * @param array $profiles - Array of profile IDs
	   * @param array $data - Data to retrieve
	   **/
	  function get_data( $profiles = array(), $data = array( 'visits', 'pageviews' ) )
	  {
		try {
			
		} catch (Exception $e) {
			die('<html><body><h1>An error occured: ' . $e->getMessage()."\n </h1></body></html>");
		}
	  }

		/**
		* Queries the Core Reporting API and returns the top 25 organic
		* search terms.
		* @param string $id The profileId to use in the query.
		* @param array $attr Options for the query.
		* @return GaData the results from the Core Reporting API.
		*/
		function get_profile_data($id, $attr = array() ) {
			if(empty($id)) {
				return false;
			}
			
			$additional = array();
			if( isset($attr['dimensions']) ) $additional['dimensions'] = $attr['dimensions'];
			if( isset($attr['sort']) ) $additional['sort'] = $attr['sort'];
			if( isset($attr['filters']) ) $additional['filters'] = $attr['filters'];
			if( isset($attr['max-results']) ) $additional['max-results'] = $attr['max-results'];
			
			$attr['metrics'] = (isset($attr['metrics'])) ? $attr['metrics'] : 'ga:visits,ga:percentNewVisits,ga:bounces';
			$attr['period'] = (isset($attr['period'])) ? $attr['period'] : '30 days';
			$attr['offset'] = (isset($attr['offset'])) ? $attr['offset'] : 0;
			$attr['startdate'] = (isset($attr['startdate'])) ? $attr['startdate'] : 'calculate';
			$attr['enddate'] = (isset($attr['enddate'])) ? $attr['enddate'] : 'calculate';
			switch($attr['period']) {
				case '30 days':
					if($attr['startdate']==='calculate') {
						$ago = ($attr['offset']) ? $attr['offset'] + 31 : 31;
						$attr['startdate'] = date( 'Y-m-d', time() - ($ago * 86400) ); // 31 days ago default
					}
					if($attr['enddate']==='calculate') {
						$ago = ($attr['offset']) ? $attr['offset'] + 1 : 1;
						$attr['enddate'] = date( 'Y-m-d', time() - ($ago * 86400) ); // 1 day ago default (more accurate than today)
					}
					break;
				case '1 year':
					if($attr['startdate']==='calculate') {
						$attr['startdate'] = date( 'Y-m-d', mktime(0, 0, 0, date("m"),   date("d"),   date("Y")-1) ); // One year ago
					}
					if($attr['enddate']==='calculate') {
						$attr['enddate'] = date( 'Y-m-d', time() - (86400) ); // 1 day ago (OK, so a year minus a day)
					}
					break;
			}
			return $this->_service->data_ga->get(
					'ga:' . $id,
					$attr['startdate'],
					$attr['enddate'],
					$attr['metrics'],
					$additional			
				);
		}	
		
	  /**
	   * Load a report
	   * @param string $name
	   **/
	  function report( $name = '' )
	  {
		try {
			if( !is_string($name) ) {
				return false;
			}
			// Check if $name report dir exists in /reports and load report.php if it exists
			if( file_exists( dirname(__FILE__) . '/reports/'.$name . '/report.php' ) ) {
				$service = $this->service( $this->_type );
				include( dirname(__FILE__) . '/reports/' . $name . '/report.php' );
			}
			// Check if there is an config.php file for settings
			// Check if there is a template.php file 
			// Load stuff from the API based on params specified in the config file, into the template
		} catch (Exception $e) {
			die('<html><body><h1>An error occured: ' . $e->getMessage()."\n </h1></body></html>");
		}
	  }
	}
}
if( class_exists('GAPHP') ) {
	$GAPHP = new GAPHP;
}