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

class dki4wp_Video_Shortcode_Handler {
  
    private $shortcode;
    public $value;
    public $attrs;

    public function __construct($value, $shortcode) {
        $this->shortcode = $shortcode;
        $this->value = $value;
        $this->attrs = $shortcode->attrs;
    }
    public function getSrc() {
        
        if (isset($this->attrs['html-defer']) && ctype_digit($this->attrs['html-defer'])) {
            $srcAttribute = 'data-defer="'.$this->attrs['html-defer'].'" data-src';
        } else if (isset($this->attrs['html-defer'])) {
            $srcAttribute = 'data-defer data-src';
        } else {
            $srcAttribute = 'src';
        }
        return $srcAttribute;
    }
    public function handle() {

        global $dki4wp_set;

        $set = get_post_meta(get_queried_object_id(), '_namespace', true);

        if (isset($_GET['set'])) {
            $set =sanitize_text_field(  $_GET['set']);
        }

        if (empty($dki4wp_set) && $set) {
            $sets =isset(get_option('dki4wp_sets_data')['dki4wp_sets_sets'])? json_decode(get_option('dki4wp_sets_data')['dki4wp_sets_sets'], 1):null;
            if (isset($sets[$set])) {
                $dki4wp_set = $sets[$set];

            }
        }
        if (empty($dki4wp_set) && !$set) {
            $dki4wp_set = false;
        }

        if ($dki4wp_set) {

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
        if (!$this->value && isset($this->attrs['default'])) {
            $this->value = $this->attrs['default'];
        }
        
        $output = isset($this->attrs['html-output']) && $this->attrs['html-output'] == 'raw' ? 'raw' : 'wordpress';
        $class = $this->attrs['html-class'] ?? false;
        $iframe_list= ['youtube','vimeo','flickr','facebook','dailymotion','videopress'];
        if(!empty($this->value) && isset($this->attrs['video'])){
            if (in_array($this->attrs['video'],$iframe_list)) {
                $video_params =  '';
                if (isset($this->attrs['html-autoplay'])) {
                    $video_params .= '&autoplay=1';
                }
                if (isset($this->attrs['html-loop'])) {
                    $video_params .= '&loop=1';
                }
                if (isset($this->attrs['html-muted'])) {
                    $video_params .= '&muted=1&mute=1';
                }    
                if (isset($this->attrs['html-controls']) && $this->attrs['html-controls'] == 'false') {
                    $video_params .= '&controls=0';
                }
                //&autoplay=1&loop=1&muted=1&controls=0
                    $lazy =  isset($this->attrs['html-loading']) && $this->attrs['html-loading'] == 'lazy' ? ' loading="lazy"' : '';
                    $getUrl = '';
                    switch($this->attrs['video']){
                        case 'youtube':
                            $getUrl = 'https://www.youtube.com/embed/';
                            break;
                        case 'vimeo':
                            $getUrl = 'https://player.vimeo.com/video/';
                            break;
                        case 'flickr':
                            $getUrl = 'https://embedr.flickr.com/photos/';
                            break;
                        case 'facebook':
                            $two = explode('|',$this->value);
                            $this->value =  isset( $two[1])? $two[1]: $two[0];
                            $getUrl = 'https://www.facebook.com/plugins/video.php?href=https://www.facebook.com/'.$two[0].'/videos/';
                            break;
                        case 'wistia':
                            $getUrl = 'https://fast.wistia.net/embed/iframe/';
                            break;
                        case 'dailymotion':
                            $getUrl = 'https://www.dailymotion.com/embed/video/';
                            break;
                        case 'videopress':
                            $getUrl = 'https://videopress.com/v/';
                            //hd=0&amp;cover=1
                            break;
                    }
                    $iframe = '<iframe title="Lorem Ipsum Video" width="500" height="281" '. $this->getSrc() . '="'.$getUrl.''. $this->value . '?feature=oembed'.$video_params.'" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen=""'.$lazy.'></iframe>';
                    if ($output == 'wordpress') {
                        $output_html = '<figure class="wp-block-embed is-type-video is-provider-youtube wp-block-embed-youtube wp-embed-aspect-16-9 wp-has-aspect-ratio'. ($class ? ' ' . $class : '') .'">
                        <div class="wp-block-embed__wrapper">
                            '. $iframe . '
                        </div>
                    </figure>';
                    } else {
                        if ($class) {
                            $output_html = str_replace('<iframe', '<iframe class="'.$class.'"', $iframe);
                        } else {
                            $output_html = $iframe;
                        }
                    }
                    $this->value = $output_html;

            }
            // TODO: Other available embeds
            if ($this->attrs['video'] == 'wistia') {
                wp_enqueue_script( 'wistia', '//fast.wistia.com/assets/external/E-v1.js' );
                wp_enqueue_script( 'wistia-vid', '//fast.wistia.com/embed/medias/'.$this->value.'.jsonp' );
                
                $video = '<div class="wistia_embed wistia_async_'. $this->value.'"  style="height:361px;width:650px">&nbsp;</div>';
                if ($output == 'wordpress') {
                    $this->value = '<figure class="wp-block-video'. ($class ? ' ' . $class : '') .'">'.  $video . '</figure>';
                } else {
                    $this->value = str_replace('<div class="', '<div class="'.$class.' ', $video);
                }
            }
            
            if($this->attrs['video']=='tiktok'){
                $video_params =  '';
                if (isset($this->attrs['html-autoplay'])) {
                    $video_params .= '&autoplay=1';
                }
                if (isset($this->attrs['html-loop'])) {
                    $video_params .= '&loop=1';
                }
                if (isset($this->attrs['html-muted'])) {
                    $video_params .= '&muted=1&mute=1';
                }    
                if (isset($this->attrs['html-controls']) && $this->attrs['html-controls'] == 'false') {
                    $video_params .= '&controls=0';
                }
                //wp_enqueue_script( 'tiktok', 'https://www.tiktok.com/embed.js' );
                
                $two = explode('|',$this->value);
                $this->value =  isset( $two[1])? $two[1]: $two[0];
                $name = $two[0];
                $video = '<blockquote class="tiktok-embed" cite="https://www.tiktok.com/@'.$name.'/video/'.$this->value.'?is_from_webapp=1&sender_device=pc" autoplay="0" muted data-video-id="'.$this->value.'" style="max-width: 605px;min-width: 325px;" ><iframe></iframe> </blockquote> <script async src="https://www.tiktok.com/embed.js"></script>';
              //  $video = '<blockquote class="tiktok-embed" cite="https://www.tiktok.com/@'.$name.'/video/'.$this->value.'" data-video-id="'.$this->value.'" style="max-width: 605px;min-width: 325px;" > </blockquote>';
                if ($output == 'wordpress') {
                    $this->value = '<figure class="wp-block-video'. ($class ? ' ' . $class : '') .'">'.  $video . '</figure>';
                } else {
                    $this->value = str_replace('<div class="', '<div class="'.$class.' ', $video);
                }
            }
            if ($this->attrs['video'] == 'media' || $this->attrs['video'] == 'url') {

                if ($this->attrs['video'] == 'media') {
                $file = wp_get_attachment_url($this->value);
                } else {
                    $file = $this->value;
                }
                
                $video_attrs = "";

                if (isset($this->attrs['html-preload']) && $this->attrs['html-preload'] == 'auto'){
                    $video_attrs .= ' preload="auto"';
                }
                
                if (isset($this->attrs['html-preload']) && $this->attrs['html-preload'] == 'metadata'){
                    $video_attrs .= ' preload="metadata"';
                }
                if (isset($this->attrs['html-autoplay'])) {
                    $video_attrs .= ' autoplay=""';
                }
                if (isset($this->attrs['html-loop'])) {
                    $video_attrs .= ' loop=""';
                }
                if (isset($this->attrs['html-muted'])) {
                    $video_attrs .= ' muted=""';
                }
                if (isset($this->attrs['html-controls']) && $this->attrs['html-controls'] == 'false') {
                } else {
                    $video_attrs .= ' controls=""';
                }
                if (isset($this->attrs['html-playsinline'])){
                    $video_attrs .= ' playsinline=""';
                }
                if (isset($this->attrs['html-poster'])){
                    $video_attrs .= ' poster="'.$this->attrs['html-poster'].'"';
                }
                $video = '<video ' . $video_attrs . ' '.$this->getSrc().'="'. $file . '"></video>';
                
                if ($output == 'wordpress') {
                    $this->value = '<figure class="wp-block-video'. ($class ? ' ' . $class : '') .'">'. str_replace('<video', '<video style="width: 100%"', $video) . '</figure>';
                } else {
                    $this->value = str_replace('<video', '<video class="'.$class.'"', $video);
                }
            }
        }
        return $this->value;
    }
}
