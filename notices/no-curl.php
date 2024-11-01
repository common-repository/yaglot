<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="error settings-error notice is-dismissible">
	<p>
		<?php
    echo sprintf( esc_html__( '%1$s: You need to activate cURL. If you need help, just ask us directly at %2$s.', YAGLOT_SLUG ), '<strong>' . YAGLOT_NAME . '</strong>',  '<a href="mailto:' . YAGLOT_EMAIL . '">' . YAGLOT_EMAIL . '</a>' );
		?>
	</p>
</div>
