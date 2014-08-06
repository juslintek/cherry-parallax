<?php
/*
  Plugin Name: Cherry Parallax Plugin
  Version: 1.0.1
  Plugin URI: http://www.cherryframework.com/
  Description: Create blocks with parallax effect
  Author: Cherry Team.
  Author URI: http://www.cherryframework.com/
  Text Domain: cherry-parallax
  Domain Path: languages/
  License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
if ( ! defined( 'ABSPATH' ) )
exit;

class cherry_parallax {
  
  public $version = '1.0.1';

  function __construct() {
    add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );
    add_shortcode( 'cherry_parallax', array( $this, 'parallax_shortcode' ) );
  }

  function assets() {
    wp_enqueue_script( 'chrome-smoothing-scroll', $this->url('js/utilits.js'), array('jquery'), '1.0', true );
    wp_enqueue_script( 'cherry-parallax', $this->url('js/cherry.parallax.js'), array('jquery'), $this->version, true );
    wp_enqueue_script( 'device-check', $this->url('js/device.min.js'), array('jquery'), '1.0.0', true );   
    wp_enqueue_style( 'cherry-parallax', $this->url('css/parallax.css'), '', $this->version );
  }

  /**
   * return plugin url
   */
  function url( $path = null ) {
    $base_url = untrailingslashit( plugin_dir_url( __FILE__ ) );
    if ( !$path ) {
      return $base_url;
    } else {
      return esc_url( $base_url . '/' . $path );
    }
  }

  /**
   * return plugin dir
   */
  function dir( $path = null ) {
    $base_dir = untrailingslashit( plugin_dir_path( __FILE__ ) );
    if ( !$path ) {
      return $base_dir;
    } else {
      return esc_url( $base_dir . '/' . $path );
    }
  }

  /**
   * Shortcode
   */
  function parallax_shortcode( $atts, $content = null ) {
    extract(shortcode_atts( array(
        'image' => '',
        'width' => '1920',
        'speed' => '3',
        'custom_class' => ''
      ),
      $atts,
      'cherry_parallax'
    ));
    if ( !$image ) {
      return;
    }

    // Get the URL to the content area.
    $content_url = untrailingslashit( content_url() );

    // Find latest '/' in content URL.
    $last_slash_pos = strrpos( $content_url, '/' );

    // 'wp-content' or something else.
    $content_dir_name = substr( $content_url, $last_slash_pos - strlen( $content_url ) + 1 );

    $pos = strpos( $image, $content_dir_name );

    if ( false !== $pos ) {

    $img_new = substr( $image, $pos + strlen( $content_dir_name ), strlen( $image ) - $pos );
    $image = $content_url . $img_new;

    }

    $width = intval($width);

    $result = '<section class="parallax-box ' . esc_attr( $custom_class ) . '">';
    $result .= '<div class="parallax-content">' . do_shortcode( $content ) . '</div>';
    $default_css = apply_filters( 'cherry_parallax_css', 'background:url(\'' . $image . '\') no-repeat center 0; left:50%; margin-left:-' . ($width/2) . 'px; width:' . $width . 'px', $custom_class );
    $result .= '<div data-speed="' . $speed . '" class="parallax-bg" style="' . $default_css . '"></div>';
    $result .= '</section>';

    $result = apply_filters( 'cherry_plugin_shortcode_output', $result, $atts, 'cherry_parallax' );

    return $result;
  }

}

new cherry_parallax();
?>