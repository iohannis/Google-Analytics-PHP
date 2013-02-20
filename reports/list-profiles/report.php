<?php
try {
    global $_params, $output_title, $output_body;
	$this->head( '<link href="'.$report_uri.'/style.css" rel="stylesheet">' );
    $output_title = 'Analytics Profiles';
    $output_nav = '<li><a href="'.$this->get_option( 'script_uri' ).'?logout">Logout</a></li>'."\n";
    $output_body = '<h1>Google Analytics profiles list</h1>
                    <p>The following domains are in your Google Analytics account</p><ul>';
    // $props = $service->management_webproperties->listManagementWebproperties("~all");
    $props = $service->management_profiles->listManagementProfiles("~all","~all");
	$row_alt = true; // 
    foreach($props['items'] as $item) {
		$stats = $this->get_profile_data( $item['id'] );
		$url = parse_url($item['websiteUrl']);
		$row_class = (($row_alt = !$row_alt)?'odd':'even');
        $output_body .= '<li class="profile-'.$item['id'].' '.$row_class.'">';
        $output_body .= '<span class="report-item name">'.(isset($url['host'])?$url['host']:$item['name']).'</span> ';
        $output_body .= sprintf('<a href="%1$s" target="_blank" class="report-item link">[link]</a>', $item['websiteUrl'] );
        $output_body .= '<span class="statistics">';
        $output_body .= '<span class="report-item visits">Visits: '.$stats['totalsForAllResults']['ga:visits'].'</span> ';
        $output_body .= '<span class="report-item new-visits-percent">New visits: '.round($stats['totalsForAllResults']['ga:percentNewVisits'], 1).'%</span> ';
        $output_body .= '<span class="report-item bounces">Bounces: '.$stats['totalsForAllResults']['ga:bounces'].'</span> ';
        $output_body .= '</span> ';
        $output_body .= '</li>';
    }
    $output_body .= '</ul>';
    include("output.php");
} catch (Exception $e) {
	die('<html><body><h1>An error occured: ' . $e->getMessage()."\n </h1></body></html>");
}