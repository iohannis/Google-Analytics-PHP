<?php
try {
    global $_params, $output_title, $output_body;
    $output_title = 'Analytics Profiles';
    $output_nav = '<li><a href="'.$this->get_option( 'script_uri' ).'?logout">Logout</a></li>'."\n";
    $output_body = '<h1>Google Analytics profiles list</h1>
                    <p>The following domains are in your Google Analytics account</p><ul>';
    $props = $service->management_webproperties->listManagementWebproperties("~all");
    foreach($props['items'] as $item) {
        $output_body .= sprintf('<li><a href="%2$s" target="_blank" class="report-item link">%1$s</a></li>', $item['name'], $item['websiteUrl'] );
        $output_body .= '<!-- ';
        $output_body .= print_r($item, true);
        $output_body .= ' -->';
    }
    $output_body .= '</ul>';
    include("output.php");
} catch (Exception $e) {
	die('<html><body><h1>An error occured: ' . $e->getMessage()."\n </h1></body></html>");
}