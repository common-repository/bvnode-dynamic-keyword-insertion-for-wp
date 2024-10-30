<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.bvnode.com/plugins/dynamic-content-insertion/
 * @since      1.0.0
 *
 * @package    dki4wp
 * @subpackage dki4wp/includes
 */
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    dki4wp
 * @subpackage dki4wp/includes
 * @author     Your Name <email@example.com>
 */
global $dki4wp_geodata_server;
global $dki4wp_geodata;
global $dki4wp_set;
class dki4wp {
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      dki4wp_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $dki4wp    The string used to uniquely identify this plugin.
     */
    protected $dki4wp;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if ( defined( 'dki4wp_VERSION' ) ) {
            $this->version = dki4wp_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->dki4wp = 'dynamic-keyword-insertion-for-wp';
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - dki4wp_Loader. Orchestrates the hooks of the plugin.
     * - dki4wp_i18n. Defines internationalization functionality.
     * - dki4wp_Admin. Defines all hooks for the admin area.
     * - dki4wp_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-dynamic-keyword-insertion-for-wp-loader.php';
        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-dynamic-keyword-insertion-for-wp-i18n.php';
        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-dynamic-keyword-insertion-for-wp-admin.php';
        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-dynamic-keyword-insertion-for-wp-public.php';
        $this->loader = new dki4wp_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the dki4wp_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {
        $plugin_i18n = new dki4wp_i18n();
        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        $plugin_admin = new dki4wp_Admin($this->get_dki4wp(), $this->get_version());
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu_pages' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'dki4wp_settings_init' );
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-dynamic-keyword-insertion-for-wp-metabox.php';
        new dki4wp_Metabox();
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        $plugin_public = new dki4wp_Public($this->get_dki4wp(), $this->get_version());
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        $this->loader->add_action( 'rest_api_init', $plugin_public, 'register_dki4wp_rest_shortcodes' );
        add_shortcode( 'dki4wp', array($plugin_public, 'dki4wp_shortcode') );
        $this->loader->add_filter( 'wp_nav_menu', $plugin_public, 'do_menu_shortcode' );
        $this->loader->add_filter( 'the_title', $plugin_public, 'do_menu_shortcode' );
        /* Yoast SEO */
        $this->loader->add_filter( 'wpseo_title', $plugin_public, 'do_shortcode' );
        $this->loader->add_filter( 'wpseo_metadesc', $plugin_public, 'do_shortcode' );
        $this->loader->add_filter( 'wpseo_opengraph_title', $plugin_public, 'do_shortcode' );
        $this->loader->add_filter( 'wpseo_opengraph_desc', $plugin_public, 'do_shortcode' );
        /* TODO: Rank Math */
        $this->loader->add_filter( 'rank_math/frontend/title', $plugin_public, 'do_shortcode' );
        $this->loader->add_filter( 'rank_math/frontend/description', $plugin_public, 'do_shortcode' );
        $this->loader->add_filter( 'rank_math/frontend/keywords', $plugin_public, 'do_shortcode' );
        /* TODO: WP Meta SEO */
        /* TODO: Rank My WP  */
        /* TODO: Squirrly SEO  */
        /* TODO: SmartCrawl  */
        /* TODO: The SEO Framework */
        /* TODO: Slim SEO */
        /* TODO: SEOPress */
        /* TODO: AIOSEO  */
        $this->loader->add_filter( 'aioseo_title', $plugin_public, 'do_shortcode' );
        $this->loader->add_filter( 'aioseo_description', $plugin_public, 'do_shortcode' );
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_dki4wp() {
        return $this->dki4wp;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    dki4wp_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}
