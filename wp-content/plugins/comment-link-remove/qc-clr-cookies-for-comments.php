<?php
defined('ABSPATH') or die("No direct script access!");

function get_qcld_clr_key() {
	if( function_exists( 'get_site_option' ) ) {
		$qcld_clr_key = get_site_option( 'qcld_clr_key' );
	} else {
		$qcld_clr_key = get_option( 'qcld_clr_key' );
	}
	if( !$qcld_clr_key ) {
		if( current_user_can('edit_plugins') ) {
			$qcld_clr_key = md5( time() );
			if( function_exists( 'add_site_option' ) ) {
				$qcld_clr_key = add_site_option( 'qcld_clr_key', $qcld_clr_key );
			} else {
				$qcld_clr_key = add_option( 'qcld_clr_key', $qcld_clr_key );
			}
		} else {
			return;
		}
	}
	return $qcld_clr_key;
}

function qcld_clr_conf() {
	
	?>
<div class="wrap" >
<h2><?php _e('Comments for Cookies Configuration'); ?></h2>
<p class="qc_clr_upgrade_pro"><?php  esc_html_e( 'Comment Spam Protection is a Pro Version Feature ', 'qc-clr' ); ?> <a href="<?php  echo esc_url( 'https://www.quantumcloud.com/products/comment-tools/', 'qc-clr' ); ?>" target="_blank"><span class="qc_clr_pro_feature" > <?php  esc_html_e( 'Upgrade to the Pro Version ', 'qc-clr' ); ?></span> </a> <?php  esc_html_e( 'to activate this feature.', 'qc-clr' ); ?></p>
<div class="narrow" style="padding:15px;">
<form action="" method="post" id="qcld_clr_conf">
	<?php wp_nonce_field('qcld_clr') ?>
	<h3> <?php  esc_html_e( 'Enable Spam Protection', 'qc-clr' ); ?> </h3> 
	<p><input type='checkbox' name='qcld_clr_spam_protection' value='enable' <?php checked( get_option( 'qcld_clr_spam_protection' ), 'enable' ) ?> /> <b> <?php  esc_html_e( 'Enable Spam Protection', 'qc-clr' ); ?></b> </p>

	<label><h3> <?php  esc_html_e( 'What should happen to comments caught', 'qc-clr' ); ?></h3> 
	<select name='qcld_clr_spam'><option value='delete' <?php echo ( get_option( 'qcld_clr_spam' ) == 'delete' ? 'selected' : '' ); ?> />  <?php  esc_html_e( 'Delete', 'qc-clr' ); ?> <option value='spam' <?php echo ( get_option( 'qcld_clr_spam' ) == 'spam' ? 'selected' : '' ); ?> />  <?php  esc_html_e( 'Spam', 'qc-clr' ); ?></select>
	</label><br />
	<h3> <?php  esc_html_e( 'Payload Delivery Mechanism', 'qc-clr' ); ?></h3> 
	<input type='radio' name='qcld_clr_delivery' value='css' <?php checked( get_option( 'qcld_clr_delivery' ), 'css' ) ?> />  <?php  esc_html_e( 'CSS file.', 'qc-clr' ); ?><br />
	<input type='radio' name='qcld_clr_delivery' value='img' <?php checked( get_option( 'qcld_clr_delivery' ), 'img' ) ?> />  <?php  esc_html_e( 'Image file. (recommended)', 'qc-clr' ); ?><br />
	<p> <?php  esc_html_e( 'The CSS file loads at the top of the page, possibly slowing down how fast the page renders. The image loads at the end but if your server is very slow someone may have time to enter a comment before the image loads. Unlikely but it might happen.', 'qc-clr' ); ?></p>
	<h3> <?php  esc_html_e( 'Speed Spammers', 'qc-clr' ); ?></h3> 

	<p> <?php  esc_html_e( 'Some spammers visit your site, leave a real looking comment and disappear again. They will do this quickly and then move on to the next target site.', 'qc-clr' ); ?> <br />  <?php  esc_html_e( 'To stop them we check how long they spend on your site before leaving the comment. Any value above 0 will reject those comments but might catch legitimate comments too. Recommended values from 3 to 6 seconds and send comments to the spam folder.', 'qc-clr' ); ?></p>
	<input type='text' name='qcld_clr_speed' size='3' value='<?php echo (int)get_option( 'qcld_clr_speed' ) ?>' /> <?php  esc_html_e( 'Seconds', 'qc-clr' ); ?></label><br /> <br />

	<h3> <?php  esc_html_e( 'Rejection Message', 'qc-clr' ); ?> </h3>
	<p> <?php  esc_html_e( 'When a comment fails to be posted it is not always a spammer. Add a message here to be shown to those users. Be aware that real spammers will see this message too! The comment they made will be shown below your message.', 'qc-clr' ); ?> </p>
	<textarea cols=40 rows=5 name='qcld_clr_spam_message'><?php echo esc_textarea( get_option( 'qcld_clr_spam_message' ) ); ?></textarea><br /><br />
	<input type='submit' name='submit' value='Save Options' />
	</form>
	<?php
	$qcld_clr_key = get_qcld_clr_key();
	if( $qcld_clr_key ) { ?>
	<p> <?php  esc_html_e( 'If you are feeling adventerous, you can add the following two lines', 'qc-clr' ); ?> <em> <?php  esc_html_e( 'before', 'qc-clr' ); ?> </em> <?php  esc_html_e( 'the regular WordPress mod_rewrite rules in your ', 'qc-clr' ); ?> <tt> <?php  esc_html_e( '.htaccess', 'qc-clr' ); ?> </tt>  <?php  esc_html_e( 'file. They will stop comments from spambots before they reach the database or are executed in PHP:', 'qc-clr' ); ?></p>
	<pre>
	RewriteCond %{HTTP_COOKIE} !^.*<?php echo $qcld_clr_key; ?>.*$
	RewriteRule ^wp-comments-post.php - [F,L]
	</pre>
	<?php 
}
global $wpmu_version;
if ( isset( $wpmu_version ) && $wpmu_version != '' ) {
	?><p> <?php  esc_html_e( 'As you are using WordPress MU, copy these lines into your .htaccess file, making sure the paths match the location of the signup form.', 'qc-clr' ); ?></p>
		<pre>
		RewriteCond %{HTTP_COOKIE} !^.*<?php echo $qcld_clr_key; ?>.*$
		RewriteRule ^wp-signup.php - [F,L]
		</pre>
	<?php
}
?>
</div>
</div>
<?php
}


function qcld_clr_add_referrer_to_notification( $text, $comment_id ) {
	$qcld_clr_key = get_qcld_clr_key();
	if( !$qcld_clr_key )
		return $text;
	if ( is_user_logged_in() )
		return $text;

	if ( $_COOKIE[ $qcld_clr_key ] > 1 ) {
		$ttp = ( time() - $_COOKIE[ $qcld_clr_key ] );
		$format = "seconds";
		if ( $ttp > 60 ) {
			$ttp = $ttp / 60;
			$format = "minutes";
		}
		if ( $ttp > 60 ) {
			$ttp = $ttp / 60;
			$format = "hours";
		}


		$text .= "\r\nTime to post comment: " . number_format( $ttp, 2 ) . " $format\r\n";
	}
	return $text;
}
add_filter( 'comment_notification_text', 'qcld_clr_add_referrer_to_notification', 10, 2 );
add_filter( 'comment_moderation_text', 'qcld_clr_add_referrer_to_notification', 10, 2 );
?>
