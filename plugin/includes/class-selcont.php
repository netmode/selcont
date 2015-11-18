<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 */
class Selcont {

	/**
	 * The unique identifier of this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 */
	protected $version;

    /**
     * Static property to hold our singleton instance
     *
     */
    static $instance = false;

	/**
	 *
	 */
	public function __construct() {

		$this->plugin_name = 'selcont';
		$this->version = '0.0.1';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();

	}

    /**
     * If an instance exists, this returns it.  If not, it creates one and returns it.
     *
     * @return Selcont
     */
    public static function getInstance() {
        if ( !self::$instance )
            self::$instance = new self;
        return self::$instance;
    }

  	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Selcont_i18n. Defines internationalization functionality.
	 * - Selcont_Admin. Defines all hooks for the dashboard.
	 * - Selcont_Public. Defines all hooks for the public side of the site.
	 *
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-selcont-i18n.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-selcont-admin.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-selcont-public.php';

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 */
	private function set_locale() {

        $selcont_i18n = new Selcont_i18n( $this->get_plugin_name(), $this->get_version() );
        $selcont_i18n->selcont_locale();

	}

	/**
	 * Register all of the hooks related to the dashboard functionality of the plugin.
	 */
	private function define_admin_hooks() {

        $selcont_admin = new Selcont_Admin( $this->get_plugin_name(), $this->get_version() );

        add_action( 'init', array($selcont_admin, 'init_admin' ) );

	}


	/**
	 * Register all of the hooks related to the public-facing functionality of the plugin.
	 */
	private function define_public_hooks() {

		$selcont_public = new Selcont_Public( $this->get_plugin_name(), $this->get_version() );

        add_action( 'init', array($selcont_public, 'init_public' ) );

	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
