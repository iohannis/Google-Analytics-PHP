<?php
try {
    global $_params, $output_title, $output_body;
	
	$profile_id = (isset($_GET['profile']) && (int)$_GET['profile'] == ($_GET['profile'])) ? $_GET['profile'] : FALSE;
	
	$this->head( '<link href="'.$report_uri.'/style.css" rel="stylesheet">' );
    $output_title = 'Analytics Latest Keywords';
    $output_nav = '<li><a href="'.$this->get_option( 'script_uri' ).'?logout">Logout</a></li>'."\n";
    $output_body = '<h1>Google Analytics profiles list</h1>
                    <p>Latest keywords for the profile in your Google Analytics account</p><ul style="margin:0">';
    // $props = $service->management_webproperties->listManagementWebproperties("~all");
    // $props = $service->management_profiles->listManagementProfiles("~all","~all");
	$row_alt = true; // 
	$stats = $this->get_profile_data( $profile_id, array(
			'dimensions' => 'ga:source,ga:keyword',
			'sort' => '-ga:visits,ga:source',
			'filters' => 'ga:medium==organic',
			'max-results' => '25',
		) 
	);
	$url = parse_url($stats['websiteUrl']);
	$row_class = (($row_alt = !$row_alt)?'odd':'even');
	$output_body .= '<li class="profile-'.$profile_id.' '.$row_class.' clearfix">';
	$output_body .= ' <span class="report-item name">'.(isset($url['host'])?$url['host']:$stats['name']).'</span> ';
	$output_body .= sprintf(' <a href="%1$s" target="_blank" class="report-item link">[link]</a>', $stats['websiteUrl'] );
	$output_body .= ' <span class="statistics pull-right">';
	$output_body .= ' <span class="report-item visits"><span class="label">Visits: </span><span class="value">'.$stats['totalsForAllResults']['ga:visits'].'</span></span> ';
	$output_body .= ' <span class="report-item new-visits-percent"><span class="label">New visits: </span><span class="value">'.round($stats['totalsForAllResults']['ga:percentNewVisits'], 1).'%</span></span> ';
	$output_body .= ' <span class="report-item bounces"><span class="label">Bounces: </span><span class="value">'.$stats['totalsForAllResults']['ga:bounces'].'</span></span> ';
	$output_body .= '</span> ';
	$output_body .= '</li>';
    $output_body .= '</ul>';
    include("output.php");
} catch (Exception $e) {
	die('<html><body><h1>An error occurred: </h1><ul><li>' . $e->getMessage()."</li></ul>\n<p>".__METHOD__."</p></body></html>");
}