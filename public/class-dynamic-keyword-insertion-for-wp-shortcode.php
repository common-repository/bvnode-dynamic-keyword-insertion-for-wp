<?php

/**
 * The shortcode functionality of the plugin.
 *
 * @link       https://www.bvnode.com/plugins/dynamic-content-insertion/
 * @since      1.0.0
 *
 * @package    dki4wp
 * @subpackage dki4wp/public
 */
/**
 * The shortcode functionality of the plugin.
 *
 * Defines the shortcode functianlity.
 *
 * @package    dki4wp
 * @subpackage dki4wp/public
 * @author     Your Name <email@example.com>
 */
class dki4wp_Shortcode {
    /**
     * The attrs used by shortcode
     *
     * @since    1.0.0
     * @access   private
     * @var      array    $shortcode_tag    The attrs used by shortcode
     */
    public $attrs;

    /**
     * The content used by shortcode
     *
     * @since    1.0.0
     * @access   private
     * @var      array    $content    The content used by shortcode
     */
    private $content;

    /**
     * The tag used by shortcode
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $shortcode_tag    The tag used by shortcode
     */
    private $shortcode_tag;

    /**
     * The type used by shortcode
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $type    The type used by shortcode
     */
    private $type;

    public function __construct( $attrs = [], $content = null, $shortcode_tag = null ) {
        $this->attrs = $attrs;
        $this->content = $content;
        $this->shortcode_tag = $shortcode_tag;
        $this->type = $attrs['type'] ?? 'param';
    }

    private function handle_default( $value ) {
        if ( isset( $this->attrs['default'] ) && !$value ) {
            $value = strip_tags( $this->attrs['default'] );
        } else {
            if ( isset( $this->attrs['prefix'] ) ) {
                $value = strip_tags( $this->attrs['prefix'] ) . $value;
            }
            if ( isset( $this->attrs['sufix'] ) ) {
                $value = $value . strip_tags( $this->attrs['sufix'] );
            }
        }
        return $value;
    }

    private function check_datestime_string( $datetime ) {
        return (bool) strtotime( $datetime, time() );
    }

    private function handle_keep( $value ) {
        $keep = $this->attrs['keep'] ?? false;
        if ( $keep && $keep != 'false' ) {
            $cookie_name = $this->type . '_' . (( $this->type == 'geo' ? $this->attrs['geo'] : $this->attrs['param'] ));
            if ( isset( $_COOKIE[$cookie_name] ) ) {
                $value = sanitize_text_field( wp_unslash( $_COOKIE[$cookie_name] ) );
            } else {
                if ( $keep == 'session' || $keep == 'true' ) {
                    $expires = 0;
                } elseif ( $keep == 'forever' || $keep == 'max' ) {
                    $expires = 2147483647;
                } else {
                    if ( $this->check_datestime_string( '+' . $keep ) ) {
                        $expires = strtotime( '+' . $keep, time() );
                    } else {
                        $expires = 0;
                    }
                }
                if ( !$value ) {
                    return $value;
                }
                setcookie( $cookie_name, $value, $expires );
            }
        }
        return $value;
    }

    private function handle_style( $value ) {
        if ( isset( $this->attrs['style'] ) && $this->attrs['style'] == 'upper' ) {
            $value = mb_strtoupper( $value );
        }
        if ( isset( $this->attrs['style'] ) && $this->attrs['style'] == 'lower' ) {
            $value = mb_strtolower( $value );
        }
        if ( isset( $this->attrs['style'] ) && $this->attrs['style'] == 'title' ) {
            $value = mb_convert_case( $value, MB_CASE_TITLE );
        }
        if ( isset( $this->attrs['style'] ) && $this->attrs['style'] == 'sentence' ) {
            $value = mb_strtoupper( mb_substr( $value, 0, 1 ) ) . mb_substr( $value, 1 );
        }
        return $value;
    }

    private function handle_cache( $value ) {
        if ( !empty( $this->attrs['cache'] ) && $this->attrs['cache'] == 'false' ) {
            $full_shortcode = $this->shortcode_tag;
            foreach ( $this->attrs as $attribute_key => $attribute_value ) {
                if ( $attribute_key == 'cache' ) {
                    continue;
                }
                // Use htmlspecialchars to escape special characters in the attribute values
                $full_shortcode .= ' ' . $attribute_key . '=\'' . htmlspecialchars( $attribute_value ) . '\'';
            }
            $value = '<span class="no-cache" data-bvnode-dki4wp="' . $full_shortcode . '">' . $value . '</span>';
        }
        return $value;
    }

    public function evaluate() {
        $return = null;
        if ( class_exists( 'dki4wp_' . ucfirst( $this->type ) . '_Shortcode_Handler' ) ) {
            $handler_class_name = 'dki4wp_' . ucfirst( $this->type ) . '_Shortcode_Handler';
            $handler = new $handler_class_name($return, $this);
            $return = $handler->handle();
        }
        $return = $this->handle_keep( $return );
        $return = $this->handle_default( $return );
        $return = $this->handle_style( $return );
        $return = $this->handle_cache( $return );
        return $return;
    }

}
