<?php

/**
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 */
class Selcont_i18n {

    private $plugin_name;
    private $version;

    /**
     * Initialize the class and set its properties.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Load the plugin text domain for translation.
     */
    public function selcont_locale() {

        add_action( 'plugins_loaded', array( $this, 'textdomain' ) );

    }

	public function textdomain() {

		load_plugin_textdomain($this->plugin_name, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');

	}

}
