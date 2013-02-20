Google Analytics for PHP
=================

PHP client for Google Analytics / Adsense API access using OAuth 2.0

This is based on the tutorial at <a href="http://enarion.net/programming/php/google-client-api/">http://enarion.net/programming/php/google-client-api/</a>
and forked from the repo at <a href="https://github.com/tobiaskluge/enarionGoogPhp">tobiaskluge/enarionGoogPhp</a> by <a href="https://github.com/tobiaskluge">Tobias Kluge</a>

A <a href="http://enarion.net/demos/">demo</a> is available as well.

I've forked this project to add some more features, and make it more as an app than a quick demo example.

Usage
-----
1. Copy the contents of the repo to your webserver

2. Open config-sample.php with your text editor, edit the values and save as config.php

3. Go to index.php in your browser, click the service you want to use, and authenticate with Google.

More to come
------------
This is a very early stage project, and here are some of the planned features:
- AJAX loading statistics
- Styling the reports to get a better overview
- Google Charts or other visual help
- Caching API responses
- Better reports templates
- Possibly a different framework than Twitter's Bootstrap, one that fits statistics well
- Animations in loading, creating a modern feel

License
-------
Licensed under MIT license (refer to <a href="LICENSE">LICENSE</a> for the license text and more information).
