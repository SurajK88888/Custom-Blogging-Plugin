<?php
namespace CBP\core;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Main Plugin Bootstrap Class
 */
final class Plugin {

    /**
     * Instance of this class.
     *
     * @var Plugin
     */
    private static $instance = null;

    /**
     * Return an instance of this class.
     *
     * @return Plugin A single instance of this class.
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize the hooks.
     */
    private function init_hooks() {
        // Activation & Deactivation hooks are registered in the main plugin file,
        // but we can bind the callbacks here.
        register_activation_hook( CBP_PLUGIN_FILE, [ $this, 'activate' ] );
        register_deactivation_hook( CBP_PLUGIN_FILE, [ $this, 'deactivate' ] );

        // Action hook to initialize the plugin components
        add_action( 'plugins_loaded', [ $this, 'init_components' ] );
    }

    /**
     * Plugin activation callback.
     */
    public function activate() {
        // Initialize Installer
        if ( class_exists( '\CBP\core\Installer' ) ) {
            Installer::run();
        }
    }

    /**
     * Plugin deactivation callback.
     */
    public function deactivate() {
        // Handle any deactivation cleanup if needed
    }

    /**
     * Initialize core components
     */
    public function init_components() {
        // Load text domain
        load_plugin_textdomain( 'custom-blog-pro', false, dirname( plugin_basename( CBP_PLUGIN_FILE ) ) . '/languages' );

        // Initialize Roles & Capabilities
        if ( class_exists( '\CBP\core\Roles' ) ) {
            Roles::init();
        }

        // Initialize Assets Loader
        if ( class_exists( '\CBP\core\Assets' ) ) {
            Assets::init();
        }

        // Initialize Blog Post Type & Taxonomies
        if ( class_exists( '\CBP\post\CPT' ) ) {
            \CBP\post\CPT::init();
        }
        if ( class_exists( '\CBP\post\Taxonomies' ) ) {
            \CBP\post\Taxonomies::init();
        }
        if ( class_exists( '\CBP\post\MetaBoxes' ) ) {
            \CBP\post\MetaBoxes::init();
        }

        // Initialize Admin Meta Boxes
        if ( is_admin() && class_exists( '\CBP\admin\MetaBoxes' ) ) {
            \CBP\admin\MetaBoxes::init();
        }

        // Initialize Analytics
        if ( class_exists( '\CBP\analytics\BlogAnalytics' ) ) {
            \CBP\analytics\BlogAnalytics::init();
        }

        // Initialize Frontend Template Loader
        if ( ! is_admin() && class_exists( '\CBP\frontend\TemplateLoader' ) ) {
            \CBP\frontend\TemplateLoader::init();
        }

        // Initialize AJAX Handlers
        if ( class_exists( '\CBP\ajax\FrontendAjax' ) ) {
            \CBP\ajax\FrontendAjax::init();
        }
        if ( class_exists( '\CBP\ajax\FrontendSubmission' ) ) {
            \CBP\ajax\FrontendSubmission::init();
        }

        // Initialize Shortcodes (frontend form, etc.)
        if ( class_exists( '\CBP\frontend\Shortcodes' ) ) {
            \CBP\frontend\Shortcodes::init();
        }

        // Initialize Email Module
        if ( class_exists( '\CBP\email\Cron' ) ) {
            \CBP\email\Cron::init();
        }
        if ( class_exists( '\CBP\email\Mailer' ) ) {
            \CBP\email\Mailer::init();
        }
        if ( class_exists( '\CBP\email\PublisherHook' ) ) {
            \CBP\email\PublisherHook::init();
        }

        // Initialize Ads Module
        if ( class_exists( '\CBP\ads\AdManager' ) ) {
            \CBP\ads\AdManager::init();
        }
        if ( class_exists( '\CBP\api\AdAnalyticsAPI' ) ) {
            \CBP\api\AdAnalyticsAPI::init();
        }
        if ( class_exists( '\CBP\widgets\AdWidget' ) ) {
            \CBP\widgets\AdWidget::init();
        }

        // Initialize Admin Settings & Customizer
        if ( is_admin() ) {
            if ( class_exists( '\CBP\admin\Dashboard' ) ) {
                \CBP\admin\Dashboard::init();
            }
            if ( class_exists( '\CBP\admin\Settings' ) ) {
                \CBP\admin\Settings::init();
            }
            if ( class_exists( '\CBP\admin\SettingsRegistry' ) ) {
                \CBP\admin\SettingsRegistry::init();
            }
            if ( class_exists( '\CBP\admin\EmailCampaigns' ) ) {
                \CBP\admin\EmailCampaigns::init();
            }
            if ( class_exists( '\CBP\admin\Tools' ) ) {
                \CBP\admin\Tools::init();
            }
        }

        if ( class_exists( '\CBP\customizer\DynamicCSS' ) ) {
            \CBP\customizer\DynamicCSS::init();
        }

    }
}
