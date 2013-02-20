<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Google Analytics PHP - <?= $output_title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="_res/css/bootstrap.css" rel="stylesheet">
    <style>
      body {
        padding-top: 60px;
      }
    </style>
    <link href="_res/css/bootstrap-responsive.css" rel="stylesheet">
	<?php
		if( isset($this) && $head = $this->head() ) {
			foreach( $head as $h ) {
				echo "\n".$h;
			}
		}
	?>
  </head>

  <body>
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <div class="nav-collapse">
            <ul class="nav">
              <?= $output_nav ?>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="container">
		<?= $output_body ?>
    </div>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	
	<?php // This is the place to load scripts
		if( isset($this) && $footer = $this->footer() ) {
			foreach( $footer as $h ) { ?>
			<?php echo $h; ?>
			<?php
			}
		}
	?>
  </body>
</html>
