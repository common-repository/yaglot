<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="error settings-error notice is-dismissible">
	<p>
		<?php
			echo sprintf( esc_html__( '%1$s: You need to activate the mod_rewrite module. You can find more information here: %2$sUsing Permalinks%3$s. If you need help, just ask us directly at %4$s.', YAGLOT_SLUG ), '<strong>' . YAGLOT_NAME . '</strong>', '<a target="_blank" href="https://codex.wordpress.org/Using_Permalinks">', '</a>', '<a href="mailto:' . YAGLOT_EMAIL . '">' . YAGLOT_EMAIL . '</a>' );
		?>
	</p>
</div>
