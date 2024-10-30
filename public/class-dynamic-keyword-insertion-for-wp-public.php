<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.bvnode.com/plugins/dynamic-content-insertion/
 * @since      1.0.0
 *
 * @package    dki4wp
 * @subpackage dki4wp/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    dki4wp
 * @subpackage dki4wp/public
 * @author     Your Name <email@example.com>
 */
class dki4wp_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $dki4wp    The ID of this plugin.
	 */
	private $dki4wp;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $dki4wp       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($dki4wp, $version)
	{

		$this->dki4wp = $dki4wp;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style($this->dki4wp, plugin_dir_url(__FILE__) . 'css/dynamic-keyword-insertion-for-wp-public.css', array(), $this->version, 'all');

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script($this->dki4wp, plugin_dir_url(__FILE__) . 'js/dynamic-keyword-insertion-for-wp-public.js', array('jquery'), $this->version, true);

	}

	/**
	 * Do Shortcode Dummy Function
	 *
	 * @since    1.0.0
	 */
	public function do_shortcode($content)
	{
		return htmlentities(do_shortcode(html_entity_decode($content)));
	}
	
	public function do_menu_shortcode($menu) {
		return do_shortcode($menu);
	}
	/**
	 * DKI4WP Shortcode 
	 *
	 * @since    1.0.0
	 */
	public function dki4wp_shortcode($attrs = [], $content = null, $shortcode_tag = null)

	{
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-dynamic-keyword-insertion-for-wp-shortcode.php';
		
		$iterator = new RecursiveDirectoryIterator(plugin_dir_path( __FILE__ ) . 'includes/handlers/');
		$iterator = new RecursiveIteratorIterator($iterator); 
		$iterator = new RegexIterator($iterator, "~(?<![/\\\\]index)\.php$~");
		
		foreach ($iterator as $filename=>$cur) {
			require_once $filename;
		}
		
		$shortcode = new dki4wp_Shortcode($attrs, $content, $shortcode_tag);
		return $shortcode->evaluate();
	}

	

	function dki4wp_shortcodes_callback( $data ) {
		// Access parameters from the URL
		$shortcodes = $data['shortcodes'];

		// Handle the request and return the response
		$response = array(
			'message' => 'shortcodes handled successfully!',
			'values' => json_decode($shortcodes),
		);

		$response['values'] = array_map(function($shortcode) { return do_shortcode('['.$shortcode.']'); }, $response['values']);
		
		nocache_headers();

		$result = new WP_REST_Response($response, 200);

		$result->set_headers(array('Cache-Control' => 'no-cache'));

		return $result;
		
	}

	function register_dki4wp_rest_shortcodes() {
		register_rest_route(
			'dki4wp/v1',
			'/shortcodes/',
			array(
				'methods'  => 'GET',
				'callback' => array($this, 'dki4wp_shortcodes_callback'),
				'args'     => array(
					'shortcodes' => array(
						'required' => true,
					),
				),
				'permission_callback' => '__return_true'
			)
		);
	}


}

