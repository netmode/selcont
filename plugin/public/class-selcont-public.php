<?php


/**
 * The public-facing functionality of the plugin.
 *
 */
class Selcont_Public {

	private $plugin_name;
	private $version;

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

    public function init_public() {

        $this->scripts();
        $this->shortcodes();
        $this->selcont_templates();

    }

    public function scripts() {

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

    }

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function enqueue_styles() {

        wp_enqueue_style( 'bootstrap', plugin_dir_url( __FILE__ ) . 'css/bootstrap.css', array(), $this->version, 'all' );
        wp_enqueue_style( 'bootstrap-theme', plugin_dir_url( __FILE__ ) . 'css/bootstrap-theme.css', array(), $this->version, 'all' );
        wp_enqueue_style( 'videojs-min', plugin_dir_url( __FILE__ ) . 'css/videojs-min.css', array(), $this->version, 'all' );
        wp_enqueue_style( $this->plugin_name . '-public', plugin_dir_url( __FILE__ ) . 'css/selcont-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( 'bootstrap.min', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array( 'jquery' ), $this->version, false );
        wp_enqueue_script( 'jwplayer', plugin_dir_url( __FILE__ ) . 'js/jwplayer.js', array( ), false, false );
        wp_enqueue_script( 'jwplayer-html5', plugin_dir_url( __FILE__ ) . 'js/jwplayer.html5.js', array( ), false, false );
        wp_enqueue_script( 'videojs.min', plugin_dir_url( __FILE__ ) . 'js/videojs.min.js', array( ), false, false );
        wp_enqueue_script( $this->plugin_name . '-public', plugin_dir_url( __FILE__ ) . 'js/selcont-public.js', array( 'jquery' ), $this->version, false );

	}

    public function selcont_templates() {

        add_filter( 'template_include', array( $this, 'render_single_lecture_template'), 1 );

    }

    public function shortcodes() {

        add_shortcode( 'selcont_list_lectures', array( $this, 'register_shortcode' ) );

    }

    public function register_shortcode() {

        $loop = new WP_Query(
            array(
                'post_type' => 'selcont_lecture_type',
                'orderby' => 'title'
            )
        );

        if ( $loop->have_posts() ) {
            $output = '<div class="lectures-list">';

            while( $loop->have_posts() ) {
                $loop->the_post();
                $meta = get_post_meta(get_the_id(), '');

                $output .= '
                        <h3><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>
                        <div>
                        <strong>' . $meta['instructor_name_meta_box'][0] . '</strong>
                        <br/>
                        ' . get_the_excerpt() . '
                        </div>
                        <hr/>
                        <br/><br/>
                ';
            }

            $output .= '</div>';
        } else {
            $output = 'No Lectures Found.';
        }

        return $output;
    }

    function render_single_lecture_template( $template_path ) {

        if ( get_post_type() == 'selcont_lecture_type' ) {
            if ( is_single() ) {
                if ( $theme_file = locate_template( array ( 'single-selcont_lecture_type.php' ) ) ) {
                    $template_path = $theme_file;
                } else {
                    $template_path = plugin_dir_path( __FILE__ ) . '/templates/single-selcont_lecture_type.php';
                }
            }
        }

        return $template_path;

    }

}
