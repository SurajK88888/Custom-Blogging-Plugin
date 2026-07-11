<?php
namespace CBP\email;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Mailer Class
 * 
 * Intercepts phpmailer_init to apply custom SMTP settings if configured.
 */
class Mailer {

    /**
     * Initialize mailer hooks.
     */
    public static function init() {
        add_action( 'phpmailer_init', [ __CLASS__, 'configure_smtp' ] );
    }

    /**
     * Configure PHPMailer with custom SMTP settings.
     * 
     * @param \PHPMailer\PHPMailer\PHPMailer $phpmailer
     */
    public static function configure_smtp( $phpmailer ) {
        // We will fetch these from our Settings module later.
        // For now, we stub the retrieval.
        $smtp_enabled = get_option( 'cbp_smtp_enabled', false );

        if ( ! $smtp_enabled ) {
            return;
        }

        $host = get_option( 'cbp_smtp_host', '' );
        $port = get_option( 'cbp_smtp_port', 587 );
        $user = get_option( 'cbp_smtp_username', '' );
        $pass = get_option( 'cbp_smtp_password', '' );
        $enc  = get_option( 'cbp_smtp_encryption', 'tls' ); // tls or ssl
        $from = get_option( 'cbp_smtp_from_email', get_option( 'admin_email' ) );
        $name = get_option( 'cbp_smtp_from_name', get_bloginfo( 'name' ) );

        if ( empty( $host ) ) {
            return;
        }

        $phpmailer->isSMTP();
        $phpmailer->Host       = $host;
        $phpmailer->SMTPAuth   = true;
        $phpmailer->Port       = $port;
        $phpmailer->Username   = $user;
        $phpmailer->Password   = $pass;
        $phpmailer->SMTPSecure = ( 'ssl' === $enc ) ? 'ssl' : 'tls';
        $phpmailer->From       = $from;
        $phpmailer->FromName   = $name;
    }
}
