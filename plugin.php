<?php
/*
 * Plugin Name: Category Limit for WooCommerce
 * Plugin URI:  https://wpexperto.es
 * Description: Woocommerce toolkit to only allow purchase from one or more parent categories for each order.
 * Text Domain: wcl-plugin
 * Version:     1.0
 * Author:      Amir JM
 * Author URI:  https://wpexperto.es/
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) or exit;

class cat_limit_wooCommerce {
	
	public function __construct(){
		add_action( 'woocommerce_add_to_cart', array($this,'validate1_cat'),15,6 );
		add_action( 'woocommerce_check_cart_items', array($this,'validate1_cat'),15,0 );
		add_action( 'init', array($this,'wcl_load_textdomain' ), 0);
	}
	
	//Link shortcodes
	public function validate1_cat(){
		$woo_limit   = array();
		$limit       = get_option('wc_settings_wcl_settings_limit');
		$exclude_cat = get_option('wc_settings_wcl_settings_exclude');
		$cat_label   = get_option('wc_settings_wcl_settings_label');
		
		if (!$limit ) return;
		
		if ($exclude_cat) $exclude_cat = explode(',', $exclude_cat);
		
		foreach( WC()->cart->get_cart() as $cart_item ){
			$terms = get_the_terms( $cart_item['product_id'], 'product_cat' );
			if (!empty($exclude_cat)) {
				foreach( $exclude_cat as $cat ){
					if ( !has_term( $cat, 'product_cat', $cart_item['product_id'] ) ) {
						if ($terms[0]->parent) $term_id = $terms[0]->parent;
						else $term_id = $terms[0]->term_id;
						$woo_limit[] = $term_id;
					}
				}
			} else {
				if ($terms[0]->parent) $term_id = $terms[0]->parent;
				else $term_id = $terms[0]->term_id;
				$woo_limit[] = $term_id;
			}
		}// end for each
		
		$woo_limit = array_unique($woo_limit);
		if (count($woo_limit)>$limit)
		wc_add_notice( sprintf( __('You can’t choose from more than %s %s in your cart', 'wcl-plugin'), $limit, $cat_label ), 'error' );
	}
	
	// plugin translation function
	public function wcl_load_textdomain() {
	  	load_plugin_textdomain( 'wcl-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
	}

} // en of class

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	include(plugin_dir_path(__FILE__) . 'inc/calss-settings.php');	
	$cat_limit_wooCommerce = new cat_limit_wooCommerce(); 	
}