<?php
/**
 * The param handler.
 *
 * @link       https://www.bvnode.com/plugins/dynamic-content-insertion/
 * @since      1.0.0
 *
 * @package    dki4wp
 * @subpackage dki4wp/public
 */

/**
 * The param handler.
 *
 * Defines the shortcode functianlity.
 *
 * @package    dki4wp
 * @subpackage dki4wp/public
 * @author     Your Name <email@example.com>
 */

class dki4wp_Param_Shortcode_Handler {
  
    private $shortcode;
    public $value;
    public $attrs;

    public function __construct($value, $shortcode) {
        $this->shortcode = $shortcode;
        $this->value = $value;
        $this->attrs = $shortcode->attrs;
    }
    public function handle() {

        global $dki4wp_set;

        $set = get_post_meta(get_queried_object_id(), '_namespace', true);

        if (isset($_GET['set'])) {
            $set = sanitize_text_field( $_GET['set']);
        }

        if (empty($dki4wp_set) && $set) {
            $sets = json_decode(get_option('dki4wp_sets_data')['dki4wp_sets_sets'], 1);
            if (isset($sets[$set])) {
                $dki4wp_set = $sets[$set];

            }
        }
        if (empty($dki4wp_set) && !$set) {
            $dki4wp_set = false;
        }

        if ($dki4wp_set && isset($this->attrs['param'])  ) {

            $params = $dki4wp_set['params'];
            foreach ($params as $param) {
                if ($param['name'] == $this->attrs['param']) {
                    $this->value = $param['value'];
                }
            }

        }

        if (isset($this->attrs['param']) && isset($_GET[$this->attrs['param']]) && !empty($_GET[$this->attrs['param']])) {
            $this->value = str_replace('+', ' ', htmlspecialchars(sanitize_text_field($_GET[$this->attrs['param']])));
        }

        return $this->value;
    }
}
