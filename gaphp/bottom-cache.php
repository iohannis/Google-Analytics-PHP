<?php
// Cache the contents to a file
try {
	$cached = fopen($cachefile, 'w');
	fwrite($cached, ob_get_contents());
	fclose($cached);
} catch (Exception $e) {
	try {
		file_put_contents($cachefile, ob_get_contents());
	} catch (Exception $e) {
		// No caching
	}
}
ob_end_flush(); // Send the output to the browser
