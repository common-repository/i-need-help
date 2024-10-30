<?php
/*
Plugin Name: I Need Help!
Description: From any page on the front-end or back-end of your WordPress-powered site, send out an SOS to your webmaster if you run into any trouble.
Version: 0.2
Requires at least: 5.0
Author: Bryan Hadaway
Author URI: https://calmestghost.com/
License: GPL
License URI: https://www.gnu.org/licenses/gpl.html
Textdomain: ineedhelp
*/

if ( !defined( 'ABSPATH' ) ) {
	http_response_code( 404 );
	die();
}

add_action( 'admin_menu', 'ineedhelp_menu_link' );
function ineedhelp_menu_link() {
	add_options_page( __( 'I Need Help! Settings', 'ineedhelp' ), __( 'I Need Help!', 'ineedhelp' ), 'manage_options', 'ineedhelp', 'ineedhelp_options_page' );
}

add_action( 'admin_init', 'ineedhelp_admin_init' );
function ineedhelp_admin_init() {
	add_settings_section( 'ineedhelp-section', __( '', 'ineedhelp' ), 'ineedhelp_section_callback', 'ineedhelp' );
	add_settings_field( 'ineedhelp-field', __( '', 'ineedhelp' ), 'ineedhelp_field_callback', 'ineedhelp', 'ineedhelp-section' );
	register_setting( 'ineedhelp-options', 'ineedhelp_from_email', 'sanitize_email' );
	register_setting( 'ineedhelp-options', 'ineedhelp_to_email', 'sanitize_email' );
}

function ineedhelp_section_callback() {
	echo __( '', 'ineedhelp' );
}

function ineedhelp_field_callback() {
	echo "<div class='postbox'><h2>" . __( 'Email Settings', 'ineedhelp' ) . "</h2>";
	$from = get_option( 'ineedhelp_from_email' );
	$admin_email = sanitize_email( get_option( 'admin_email' ) );
	echo "<h3>" . __( 'From Email', 'ineedhelp' ) . "</h3><input type='email' name='ineedhelp_from_email' value='$from' placeholder='" . __( 'Defaults to ', 'ineedhelp' ) . "$admin_email'>";
	$to = get_option( 'ineedhelp_to_email' );
	echo "<h3>" . __( 'To Email', 'ineedhelp' ) . "</h3><input type='email' name='ineedhelp_to_email' value='$to' placeholder='" . __( 'Defaults to ', 'ineedhelp' ) . "$admin_email'></div>";
}

function ineedhelp_options_page() {
	?>
	<div id="ineedhelp-admin" class="wrap">
		<?php
		wp_register_style( 'ineedhelp-style', plugin_dir_url( __FILE__ ) . '/style.css' );
		wp_enqueue_style( 'ineedhelp-style' );
		?>
		<h1><?php _e( 'I Need Help! Settings', 'ineedhelp' ); ?></h1>
		<p><?php _e( 'Thank you for using I Need Help! by <a href="https://calmestghost.com/" target="_blank">Bryan Hadaway</a>. Need help? <a href="mailto:bhadaway@gmail.com">Email me</a>.', 'ineedhelp' ); ?></p>
		<p><?php _e( 'Set your prefered to (who you want to send an SOS to) and from (where you want them to reply to you at) emails below.', 'ineedhelp' ); ?></p>
		<p><?php _e( 'Only admins (that\'s probably just you) will be able to see the form, and helpful info about the page where the SOS is sent from along with general info about your site will automatically be attached to the message without the hassle of you having to track it all down or a lot of back and forth.', 'ineedhelp' ); ?></p>
		<p><?php _e( '<a href="https://wordpress.org/support/forums/" class="button-primary" target="_blank">Post a Support Request</a> <a href="https://jobs.wordpress.net/post-a-job/" class="button-primary" target="_blank">Post a Job</a> <a href="https://calmestghost.com/donate" class="button-primary" target="_blank">Make a Donation</a> <a href="https://webguy.io/subscribe-50" class="button-primary" target="_blank">Hire Me</a>', 'ineedhelp' ); ?></p>
		<form action="options.php" method="post">
			<?php settings_fields( 'ineedhelp-options' ); ?>
			<?php do_settings_sections( 'ineedhelp' ); ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

add_action( 'admin_footer', 'ineedhelp_form', 100 );
add_action( 'wp_footer', 'ineedhelp_form', 100 );
function ineedhelp_form() {
	if ( current_user_can( 'administrator' ) ) {
		?>
		<form id="ineedhelp" name="ineedhelp" tabindex="1" method="post" action="#send">
			<?php _e( 'I Need Help!', 'ineedhelp' ); ?> <a href="<?php echo esc_url( home_url() ); ?>/wp-admin/options-general.php?page=ineedhelp" class="dashicons dashicons-admin-generic"></a>
			<div>
				<p id="message"><textarea name="message" tabindex="1" placeholder="<?php _e( 'What do you need help with?', 'ineedhelp' ); ?>" rows="6" cols="80"></textarea></p>
				<p id="submit"><input type="submit" name="sos" tabindex="1" value="<?php _e( 'I Need Help!', 'ineedhelp' ); ?>"></p>
			</div>
		</form>
		<style>
			#ineedhelp, #ineedhelp *{box-sizing:border-box;transition:all 0.5s ease}
			#ineedhelp{display:block;position:fixed;bottom:100px;right:0;font-family:georgia,serif;font-size:20px;color:#fff;padding:15px;border-left:4px #7fccff solid;background:#007acc;opacity:0.8;cursor:pointer;z-index:99999}
			#ineedhelp:hover, #ineedhelp:focus, #ineedhelp:focus-within{opacity:1}
			#ineedhelp div{position:absolute;right:-9999px;width:0}
			#ineedhelp:hover div, #ineedhelp:focus div, #ineedhelp:focus-within div{position:relative;right:0;width:100%;display:block}
			#ineedhelp textarea{width:100%;font-family:arial,sans-serif;font-size:14px;color:#767676;padding:15px;border:1px solid transparent;border-radius:0;background:#f6f6f6}
			#ineedhelp textarea:focus{color:#000;border:1px solid #7fccff}
			#ineedhelp #submit{margin-bottom:0}
			#ineedhelp #submit input{font-family:georgia,serif;font-size:16px;color:#007acc;padding:15px 25px;border:0;background:#fff;opacity:1;cursor:pointer}
			#ineedhelp #submit input:hover, #ineedhelp #submit input:focus{opacity:0.8}
			#ineedhelp .dashicons{color:#fff;line-height:16px;vertical-align:middle}
			#send{position:fixed;bottom:165px;right:10px}
			#send.success{color:green}
			#send.fail{color:red}
		</style>
		<?php
		if ( isset( $_POST['sos'] ) ) {
			include_once( ABSPATH . 'wp-admin/includes/class-wp-debug-data.php' );
			WP_Debug_Data::check_for_updates();
			$info = WP_Debug_Data::debug_data();
			$url = esc_url( ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? "https" : "http" ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" );
			$fromset = get_option( 'ineedhelp_from_email' );
			if ( $fromset ) {
				$from = sanitize_email( $fromset );
			} else {
				$from = sanitize_email( get_option( 'admin_email' ) );
			}
			$toset = get_option( 'ineedhelp_to_email' );
			if ( $toset ) {
				$to = sanitize_email( $toset );
			} else {
				$to = sanitize_email( get_option( 'admin_email' ) );
			}
			$subject = __( 'I Need Help!', 'ineedhelp' );
			$message = esc_textarea( $_POST['message'] );
			$validated = true;
			if ( !$validated ) {
				print '<p id="send" class="fail">' . __( 'SOS Failed', 'ineedhelp' ) . '</p>';
				exit;
			}
			$body  = "";
			$body .= $message;
			$body .= "\n\n";
			$body .= "- - - - - -";
			$body .= "\n\n";
			$body .= "" . __( 'This SOS was sent from: ', 'ineedhelp' ) . "$url";
			$body .= "\n\n";
			$body .= "- - - - - -";
			$body .= "\n\n";
			$body .= esc_attr( WP_Debug_Data::format( $info, 'debug' ) );
			$success = wp_mail( $to, $subject, $body, "From: <$from>" );
			if ( $success ) {
				print '<p id="send" class="success">' . __( 'SOS Sent', 'ineedhelp' ) . '</p>';
			} else {
				print '<p id="send" class="fail">' . __( 'SOS Failed', 'ineedhelp' ) . '</p>';
			}
		}
		?>
		<?php
	}
}