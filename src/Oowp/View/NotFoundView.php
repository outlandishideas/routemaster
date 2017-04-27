<?php

namespace Outlandish\Wordpress\Routemaster\Oowp\View;

use Outlandish\Wordpress\Oowp\Views\PostView;

class NotFoundView extends PostView
{
	public function render($args = [])
	{
		global $siteErrorMessage;
		?>
		<div id="404">
			<h1>Page not found</h1>
			<!-- <?php echo $siteErrorMessage; ?> -->
		</div>
		<?php
	}
}