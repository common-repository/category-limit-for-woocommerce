<?php

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

if ( ! class_exists( 'WCL_WooCommerce_Settings_Page' ) ) :
function WCL_Add_Tab()
    {
class WCL_WooCommerce_Settings_Page extends WC_Settings_Page {
	
	public function __construct() {

		$this->id = 'wcl_settings';
		$this->label = __( 'Category Limit', 'wcl-plugin' );

		/**
		 *	Define all hooks instead of inheriting from parent
		 */

		// parent::__construct();

		// Add the tab to the tabs array
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 99 );

		// Add new section to the page
		add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );

		// Add settings
		add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );

		// Process/save the settings
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
	}

	/**
	 *	Get sections
	 *
	 *	@return array
	 */
	public function get_sections() {

		// Must contain more than one section to display the links
		// Make first element's key empty ('')
		$sections = array(
			''         => __( 'Map Settings', 'wcl-plugin' )
		);

		return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
	}

	/**
	 *	Output sections
	 */
	public function output_sections() {

		global $current_section;

		$sections = $this->get_sections();

		if ( empty( $sections ) || 1 === sizeof( $sections ) ) {
			return;
		}

		echo '<ul class="subsubsub">';

		$array_keys = array_keys( $sections );

		foreach ( $sections as $id => $label ) {
			echo '<li><a href="' . admin_url( 'admin.php?page=wc-settings&tab=' . $this->id . '&section=' . sanitize_title( $id ) ) . '" class="' . ( $current_section == $id ? 'current' : '' ) . '">' . $label . '</a> ' . ( end( $array_keys ) == $id ? '' : '|' ) . ' </li>';
		}

		echo '</ul><br class="clear" />';
	}

	/**
	 *	Get settings array
	 *
	 *	@return array
	 */
	public function get_settings() {

		global $current_section;

		$settings = array();

		if ( $current_section == '' ) {

			$settings = array(
            'section_title' => array(
                'name'     => __( 'Google Map Settings', 'wcl-plugin' ),
                'type'     => 'title',
                'desc'     => 'Allow purchase only from limited categories at once',
                'id'       => 'wc_settings_wcl_settings_map'
            ),
            'limit' => array(
                'name'     => __( 'Categories Allowed', 'wcl-plugin' ),
                'type'     => 'text',
                'desc_tip' => __( 'How many parent categories allowed', 'wcl-plugin' ),
                'id'       => 'wc_settings_wcl_settings_limit'
            ),
			'exclude' => array(
                'name'     => __( 'Exclude Categories', 'wcl-plugin' ),
                'type'     => 'text',
                'desc_tip' => __( 'Exclude categories and child sub categories, slugs comma seprated', 'wcl-plugin' ),
                'id'       => 'wc_settings_wcl_settings_exclude'
            ),
			'label' => array(
                'name'     => __( 'Category Label', 'wcl-plugin' ),
                'type'     => 'text',
                'desc_tip' => __( 'Label for category in limit error message, restaurant, shop...', 'wcl-plugin' ),
                'id'       => 'wc_settings_wcl_settings_label'
            ),   
            'section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'wc_settings_wcl_settings_map_end'
            )
        );
		
		} else {

			// Overview
			$settings = array();
		}

		return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings );
	}

	/**
	 *	Output the settings
	 */
	public function output() {
		$settings = $this->get_settings();
		WC_Admin_Settings::output_fields( $settings );
	}

	/**
	 *	Process save
	 *
	 *	@return array
	 */
	public function save() {

		global $current_section;

		$settings = $this->get_settings();

		WC_Admin_Settings::save_fields( $settings );

		if ( $current_section ) {
			do_action( 'woocommerce_update_options_' . $this->id . '_' . $current_section );
		}
	}
}

return new WCL_WooCommerce_Settings_Page();
}
add_filter('woocommerce_get_settings_pages', 'WCL_Add_Tab', 16);

endif;
