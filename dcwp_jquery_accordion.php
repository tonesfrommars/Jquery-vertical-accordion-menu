<?php
/*
		Plugin Name: jQuery Vertical Accordion Menu
		Plugin URI: http://www.designchemical.com/blog/index.php/wordpress-plugins/wordpress-plugin-jquery-vertical-accordion-menu-widget/
		Tags: jquery, dropdown, menu, vertical accordion, animated, css, navigation, accordion
		Description: Creates vertical accordion menus from any Wordpress custom menu using jQuery. Add menus using either widgets or shortcodes. Features include - handles multiple levels, saved state using cookies and option of selecting "click" or "hover" events for triggering the menu.
		Author: Lee Chestnutt
		Version: 3.1.2
		Author URI: http://www.designchemical.com
*/

global $registered_skins;

class dc_jqaccordion {

	function __construct() {
		global $registered_skins;

		if(!is_admin()){

			// Header styles
			add_action( 'wp_enqueue_scripts', array('dc_jqaccordion', 'header') );

			// Shortcodes
			add_shortcode( 'dcwp-jquery-accordion', 'dcwp_dc_jqaccordion_shortcode' );
		}

		$registered_skins = array();
	}

	public static function header(){

		// Scripts
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jqueryhoverintent', plugin_dir_url( __FILE__ ) . '/js/jquery.hoverIntent.minified.js', array('jquery') );
		wp_enqueue_script( 'jquerycookie', plugin_dir_url( __FILE__ ) . '/js/jquery.cookie.js', array('jquery') );
		wp_enqueue_script( 'dcjqaccordion', plugin_dir_url( __FILE__ ) . '/js/jquery.dcjqaccordion.2.9.js', array('jquery') );
	}

};

// Include the widget
include_once('dcwp_jquery_accordion_widget.php');

// Initialize the plugin.
$dcjqaccordion = new dc_jqaccordion();

// Register the widget
add_action('widgets_init', create_function('', 'return register_widget("dc_jqaccordion_widget");'));

/**
* Create a menu shortcode
*/
function dcwp_dc_jqaccordion_shortcode($atts){

	extract(shortcode_atts( array(
		'menu' => '',
		'event' => 'click',
		'auto_close' => 'false',
		'save' => 'false',
		'expand' => 'false',
		'disable' => 'false',
		'close' => 'false',
		'count' => 'false',
		'menu_class' => 'menu',
		'disable_class' => '',
		'expand_class' => 'current-menu-item',
		'hover' => '600',
		'animation' => 'slow',
		'skin' => 'No Theme',
		'id' => ''
	), $atts));

	$_SESSION['dc_jqaccordion_menu'] = $_SESSION['dc_jqaccordion_menu'] != '' ? $_SESSION['dc_jqaccordion_menu'] + 1 : 1 ;
	$id = $id == '' ? 's'.$_SESSION['dc_jqaccordion_menu'] : 's'.$id ;
	$menuId = 'dc_jqaccordion_widget-'.$id.'-item';
	$out = '';

	if($skin != 'No Theme'){
		$out .= "\n\t<link rel=\"stylesheet\" href=\"".dc_jqaccordion::get_plugin_directory()."/skin.php?widget_id=".$id."&amp;skin=".strtolower($skin)."\" type=\"text/css\" media=\"screen\"  />";
	}

	$out .= '<script type="text/javascript">
				jQuery(document).ready(function($) {
					jQuery("#'.$menuId.'").dcAccordion({
						eventType: "'.$event.'",
						hoverDelay: '.$hover.',
						menuClose: '.$close.',
						autoClose: '.$auto_close.',
						saveState: '.$save.',
						autoExpand: '.$expand.',
						classExpand: "'.$expand_class.'",
						classDisable: "'.$disable_class.'",
						showCount: '.$count.',
						disableLink: '.$disable.',
						cookie: "'.$menuId.'",
						speed: "'.$animation.'"
					});
				});
			</script>';
	$out .= '<div class="dcjq-accordion" id="'.$menuId.'">';
	$out .= wp_nav_menu(
					array(
						'fallback_cb' => '',
						'menu' => $menu,
						'menu_class' => $menu_class,
						'echo' => false
						)
					);
	$out .= '</div>';
	return $out;
}
?>