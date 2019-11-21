<?php

/**
 * The dashboard-specific functionality of the plugin.
 */
class Selcont_Admin {

    private $plugin_name;
    private $version;

    /**
     * Initialize the class and set its properties.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

	public function init_admin() {

        $this->scripts();
        $this->selcont_post_type();
        $this->taxonomies();
        $this->metaboxes();

	}

    public function scripts() {

        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

    }

	public function enqueue_styles() {

        wp_enqueue_style( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'css/selcont-admin.css', array(), $this->version, 'all' );

	}

	public function enqueue_scripts() {

        wp_enqueue_media();

        wp_enqueue_script( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'js/selcont-admin.js', array( 'jquery' ), $this->version, false );
        wp_enqueue_script( $this->plugin_name . '-slides', plugin_dir_url( __FILE__ ) . 'js/selcont-slides.js', array( 'jquery' ), $this->version, false );

        wp_localize_script( $this->plugin_name . '-slides', 'selcont_admin',
            array(
                'gallery_title' 	=> __( 'Add Slides to Lecture', 'selcont' ),
                'gallery_button' 	=> __( 'Add to lecture', 'selcont' ),
                'delete_image'		=> __( 'Delete slide', 'selcont' ),
                'default_title' 	=> __( 'Upload', 'selcont' ),
                'default_button' 	=> __( 'Select this', 'selcont' ),
            )
        );

	}

    public function selcont_post_type() {

        $labels = array(
            'name' => 'Lectures',
            'singular_name' => 'Lecture',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New',
            'edit_item' => 'Edit Item',
            'new_item' => 'Add New Item',
            'view_item' => 'View Item',
            'search_items' => 'Search Lectures',
            'not_found' => 'No Lectures Found',
            'not_found_in_trash' => 'No Lectures Found In Trash'
        );

        $args = array(
            'labels' => $labels,
            'query_var' => 'lectures',
            'rewrite' => array(
                'slug' => 'lectures',
            ),
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            //'menu_position' => 25,
            'menu_icon' => plugins_url( '../assets/icon.png', __FILE__ ),
            'supports' => array(
                'title',
                'editor',
                'thumbnail'
            )
        );

        register_post_type( $this->plugin_name . '_lecture_type', $args);

    }

    public function taxonomies() {

        $this->register_courses_taxonomy();
        $this->register_institutions_taxonomy();

    }

    public function register_courses_taxonomy() {

        $labels = array(
            'name'              => 'Courses',
            'singular_name'     => 'Course',
            'search_items'      => 'Search Courses',
            'all_items'         => 'All Courses',
            'edit_item'         => 'Edit Course',
            'update_item'       => 'Update Course',
            'add_new_item'      => 'Add New Course',
            'new_item_name'     => 'New Course Name',
            'menu_name'         => 'Courses'
        );

        $courses_taxonomy = array(
            'hierarchical' => true,
            'query_var' => 'lecture_course',
            'rewrite' => array(
                'slug' => 'courses'
            ),
            'labels' => $labels
        );

        register_taxonomy( 'courses', array('selcont_lecture_type'), $courses_taxonomy );

    }

    public function register_institutions_taxonomy() {

        $labels = array(
            'name'              => 'Institutions',
            'singular_name'     => 'Institution',
            'search_items'      => 'Search Institutions',
            'all_items'         => 'All Institutions',
            'edit_item'         => 'Edit Institution',
            'update_item'       => 'Update Institution',
            'add_new_item'      => 'Add New Institution',
            'new_item_name'     => 'New Institution',
            'menu_name'         => 'Institutions'
        );

        $institutions_taxonomy = array(
            'hierarchical' => true,
            'query_var' => 'lecture_institution',
            'rewrite' => array(
                'slug' => 'institutions'
            ),
            'labels' => $labels
        );

        register_taxonomy( 'institutions', array('selcont_lecture_type'), $institutions_taxonomy );

    }

    public function metaboxes() {

        add_action( 'add_meta_boxes', array( $this, 'add_instructor_name_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_instructor_name_meta_box' ) );

        add_action( 'add_meta_boxes', array( $this, 'add_video_url_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_video_url_meta_box' ) );

        add_action( 'add_meta_boxes', array( $this, 'add_school_name_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_school_name_meta_box' ) );

        add_action( 'add_meta_boxes', array( $this, 'add_image_file_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_image_file_meta_box' ) );

        add_action( 'add_meta_boxes', array( $this, 'add_timestamps_slides_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_timestamps_slides_meta_box' ) );

        add_action( 'add_meta_boxes', array( $this, 'add_repeatable_meta_boxes' ) );
        add_action( 'save_post', array( $this, 'save_repeatable_meta_boxes' ) );

    }

    public function add_instructor_name_meta_box( $post_type ) {
        $post_types = array('selcont_lecture_type');     //limit meta box to certain post type

        if ( in_array( $post_type, $post_types )) {
            add_meta_box(
                'instructor_name_meta_box',
                'Instructor',
                array( $this, 'render_instructor_name_meta_box' ),
                $post_type,
                'advanced',
                'default'
            );
        }
    }
    public function save_instructor_name_meta_box( $post_id ) {

        // Check if our nonce is set.
        if ( ! isset( $_POST['netmode_selcont_nonce'] ) )
            return $post_id;

        $nonce = $_POST['netmode_selcont_nonce'];

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'netmode_selcont' ) )
            return $post_id;

        // If this is an autosave, our form has not been submitted,
        //     so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return $post_id;

        // Check the user's permissions.
        if ( 'page' == $_POST['selcont_lecture_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) )
                return $post_id;
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) )
                return $post_id;
        }

        // Sanitize the user input.
        $name = sanitize_text_field( $_POST['instructor_name_meta_box'] );

        // Update the meta field.
        update_post_meta( $post_id, 'instructor_name_meta_box', $name );

    }
    public function render_instructor_name_meta_box( $post ) {

        // Add an nonce field so we can check for it later.
        wp_nonce_field( 'netmode_selcont', 'netmode_selcont_nonce' );

        // Use get_post_meta to retrieve an existing value from the database.
        $value = get_post_meta( $post->ID, 'instructor_name_meta_box', true );
        ?>

        <table class="form-table">
            <tbody>
            <tr valign="top">
                <th scope="row">
                    <label for="instructor_name_meta_box">Instructor's Name:</label>
                </th>
                <td>
                    <input type="text" id="instructor_name_meta_box" class="widefat" name="instructor_name_meta_box" value=" <?php echo esc_attr( $value ) ?>" />
                    <p class="description">Enter the instructor's name.</p>
                </td>
            </tr>
            </tbody>
        </table>

    <?php
    }

    public function add_video_url_meta_box( $post_type ) {
        $post_types = array('selcont_lecture_type');     //limit meta box to certain post type
        if ( in_array( $post_type, $post_types )) {
            add_meta_box(
                'video_url_meta_box',
                'Video',
                array( $this, 'render_video_url_meta_box' ),
                $post_type,
                'advanced',
                'default'
            );
        }
    }
    public function save_video_url_meta_box( $post_id ) {
        // Check if our nonce is set.
        if ( ! isset( $_POST['netmode_selcont_nonce'] ) )
            return $post_id;

        $nonce = $_POST['netmode_selcont_nonce'];

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'netmode_selcont' ) )
            return $post_id;

        // If this is an autosave, our form has not been submitted,
        //     so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return $post_id;

        // Check the user's permissions.
        if ( 'page' == $_POST['selcont_lecture_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) )
                return $post_id;
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) )
                return $post_id;
        }

        // Sanitize the user input.
        $name = sanitize_text_field( $_POST['video_url_meta_box'] );

        // Update the meta field.
        update_post_meta( $post_id, 'video_url_meta_box', $name );
    }
    public function render_video_url_meta_box( $post ) {
        // Add an nonce field so we can check for it later.
        wp_nonce_field( 'netmode_selcont', 'netmode_selcont_nonce' );

        // Use get_post_meta to retrieve an existing value from the database.
        $value = get_post_meta( $post->ID, 'video_url_meta_box', true );

        ?>

        <table class="form-table">
            <tbody>
            <tr valign="top">
                <th scope="row">
                    <label for="video_url_meta_box">Video URL:</label>
                </th>
                <td>
                    <input type="text" id="video_url_meta_box" class="widefat" name="video_url_meta_box" value="<?php echo esc_attr( $value ) ?>" />
                    <p class="description">Enter the URL of the corresponding video.</p>
                </td>
            </tr>
            </tbody>
        </table>

    <?php
    }

    public function add_school_name_meta_box( $post_type ) {
        $post_types = array('selcont_lecture_type');     //limit meta box to certain post type
        if ( in_array( $post_type, $post_types )) {
            add_meta_box(
                'school_name_meta_box',
                'School',
                array( $this, 'render_school_name_meta_box' ),
                $post_type,
                'advanced',
                'default'
            );
        }
    }
    public function save_school_name_meta_box( $post_id ) {
        // Check if our nonce is set.
        if ( ! isset( $_POST['netmode_selcont_nonce'] ) )
            return $post_id;

        $nonce = $_POST['netmode_selcont_nonce'];

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'netmode_selcont' ) )
            return $post_id;

        // If this is an autosave, our form has not been submitted,
        //     so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return $post_id;

        // Check the user's permissions.
        if ( 'page' == $_POST['selcont_lecture_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) )
                return $post_id;
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) )
                return $post_id;
        }

        // Sanitize the user input.
        $name = sanitize_text_field( $_POST['school_name_meta_box'] );

        // Update the meta field.
        update_post_meta( $post_id, 'school_name_meta_box', $name );
    }
    public function render_school_name_meta_box( $post ) {

        // Add an nonce field so we can check for it later.
        wp_nonce_field( 'netmode_selcont', 'netmode_selcont_nonce' );

        // Use get_post_meta to retrieve an existing value from the database.
        $value = get_post_meta( $post->ID, 'school_name_meta_box', true );
        ?>

        <table class="form-table">
            <tbody>
            <tr valign="top">
                <th scope="row">
                    <label for="school_name_meta_box">School:</label>
                </th>
                <td>
                    <input type="text" id="school_name_meta_box" class="widefat" name="school_name_meta_box" value=" <?php echo esc_attr( $value ) ?>" />
                    <p class="description">Enter school's name (e.g. School of Electrical Engineering).</p>
                </td>
            </tr>
            </tbody>
        </table>

    <?php
    }

    public function add_image_file_meta_box( $post_type ) {
        $post_types = array('selcont_lecture_type');     //limit meta box to certain post type
        if ( in_array( $post_type, $post_types )) {
            add_meta_box(
                'slide_image_meta_box',
                'Presentation File',
                array( $this, 'render_image_file_meta_box' ),
                $post_type,
                'advanced',
                'default'
            );
        }
    }
    public function render_image_file_meta_box( $post ) {

        // Add an nonce field so we can check for it later.
        wp_nonce_field( 'netmode_selcont', 'netmode_selcont_nonce' );

        $html = '<input id="slide_image_meta_box" class="button" type="file" name="slide_image_meta_box" value="" />';

        $html .= '<p class="description">';
        if( '' == get_post_meta( $post->ID, 'umb_file', true ) ) {
            $html .= 'You have no file attached to this post.';
        } else {
            $html .= get_post_meta( $post->ID, 'umb_file', true );
        }
        $html .= '</p>';

        echo $html;

    }
    public function save_image_file_meta_box( $post_id ) {
        // Check if our nonce is set.
        if ( ! isset( $_POST['netmode_selcont_nonce'] ) )
            return $post_id;

        $nonce = $_POST['netmode_selcont_nonce'];

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'netmode_selcont' ) )
            return $post_id;

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return $post_id;

        // Check the user's permissions.
        if ( 'page' == $_POST['selcont_lecture_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) )
                return $post_id;
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) )
                return $post_id;
        }

        // If the user uploaded an image, let's upload it to the server
        if( ! empty( $_FILES ) && isset( $_FILES['slide_image_meta_box'] ) ) {

            // Upload the goal image to the uploads directory, resize the image, then upload the resized version
            $goal_image_file = wp_upload_bits( $_FILES['slide_image_meta_box']['name'], null, file_get_contents( $_FILES['slide_image_meta_box']['tmp_name'] ) );
            // Set post meta about this image. Need the comment ID and need the path.
            if( false == $goal_image_file['error'] ) {

                update_post_meta($post_id, 'umb_file', $goal_image_file['url'] );

            }
        }
    }

    function add_repeatable_meta_boxes( $post_type ) {
        $post_types = array('selcont_lecture_type');
        if ( in_array( $post_type, $post_types )) {
            add_meta_box(
                'repeatable_fields',
                'Slides',
                array( $this, 'repeatable_meta_box_display' ),
                $post_type,
                'advanced',
                'default'
            );
        }
    }
    function repeatable_meta_box_display( $post ) {

        $repeatable_fields = get_post_meta($post->ID, 'repeatable_fields', true);

        if ( empty( $repeatable_fields ) ){
	    $repeatable_fields = [];
            $repeatable_fields[] = array (
                'stt_meta_box_image' => '',
                'stt_meta_box_title' => '',
                'stt_meta_box_time' => '' );
        }

        wp_nonce_field( 'hhs_repeatable_meta_box_nonce', 'hhs_repeatable_meta_box_nonce' );
        ?>

        <table id="repeatable-fieldset-one" class="widefat fixed" cellspacing="0">
            <thead>
            <tr>
                <th style="width:2%;">#</th>
                <th style="width:35%;">Image URL</th>
                <th style="width:40%;">Title</th>
                <th style="width:8%;">Timestamp</th>
                <th style="width:15%;">Remove</th>
            </tr>
            </thead>
            <tbody>

            <?php

            $i = 1;
            foreach ( $repeatable_fields as $field ) { ?>

                <tr class="single-movie-row ui-state-default">
                    <td style="width:2%;">
                        <label for="_slides[<?php echo $i;?>][stt_meta_box_aa]">
                            <input name="_slides[<?php echo $i;?>][stt_meta_box_aa]" id="_slides[<?php echo $i;?>][stt_meta_box_aa]" class="movie_rank_number" disabled="disabled" type="text" value="<?php echo $i; ?>" />
                        </label>
                    </td>
                    <td style="width:35%;">
                        <label for="_slides[<?php echo $i;?>][stt_meta_box_image]">
                            <input name="_slides[<?php echo $i;?>][stt_meta_box_image]" class="upload_image" id="_slides[<?php echo $i;?>][stt_meta_box_image]" type="text" value="<?php echo esc_attr( $field['stt_meta_box_image'] );?>" />
                        </label>
                    </td>
                    <td style="width:40%;">
                        <input type="text" name="_slides[<?php echo $i;?>][stt_meta_box_title]" id="_slides[<?php echo $i;?>][stt_meta_box_title]" class="" value="<?php echo esc_html( $field['stt_meta_box_title'] );?>" />
                    </td>
                    <td style="width:8%;">
                        <input type="text" id="_slides[<?php echo $i;?>][stt_meta_box_time]" name="_slides[<?php echo $i;?>][stt_meta_box_time]" class="movie_description_editor_hidden" value="<?php echo esc_html( $field['stt_meta_box_time'] );?>" size="3" />
                    </td>
                    <td style="width:15%;">
                        <a class="button remove-row" href="#">Remove</a>
                    </td>
                </tr>

            <?php
                $i++;
            }
            ?>

            <!-- empty hidden one for jQuery -->
            <tr class="empty-row screen-reader-text single-movie-row">
                <td style="width:2%;">
                    <label for="_slides[%s][stt_meta_box_aa]">
                        <input name="_slides[%s][stt_meta_box_aa]" id="_slides[%s][stt_meta_box_aa]" class="movie_rank_number" disabled="disabled" type="text" />
                    </label>
                </td>
                <td style="width:35%;">
                    <label for="_slides[%s][stt_meta_box_image]">
                        <input name="_slides[%s][stt_meta_box_image]" class="upload_image" id="_slides[%s][stt_meta_box_image]" type="text" value="" />
                        <!-- input class="upload_image_button button button-upload" id="_slides[< ?php echo $i;?>][upload_image_button]" type="button" value="Upload" / -->
                    </label>
                </td>
                <td style="width:40%;">
                    <input type="text" name="_slides[%s][stt_meta_box_title]" id="_slides[%s][stt_meta_box_title]" class="title_tinymce_editor" />
                </td>
                <td style="width:8%;">
                    <input type="text" id="_slides[%s][stt_meta_box_time]" name="_slides[%s][stt_meta_box_time]" class="movie_description_editor_hidden" size="3" />
                </td>
                <td style="width:15%;">
                    <a class="button remove-row" href="#">Remove</a>
                </td>
            </tr>

            </tbody>
        </table>

        <p id="add-row-p-holder"><a id="add-row" class="button" href="#">Insert Another Slide</a></p>
    <?php

        print "<pre>";
        print_r($repeatable_fields);
        print "</pre>";
    }
    function save_repeatable_meta_boxes($post_id) {

        if ( ! isset( $_POST['hhs_repeatable_meta_box_nonce'] ) ||
            !wp_verify_nonce( $_POST['hhs_repeatable_meta_box_nonce'], 'hhs_repeatable_meta_box_nonce' ) )
            return;

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        if (!current_user_can('edit_post', $post_id))
            return;

        $clean = array();

        if  ( isset ( $_POST['_slides'] ) && is_array( $_POST['_slides'] ) ) :

            foreach ( $_POST['_slides'] as $i => $slide ){
                if( $i == '%s' ){
                    continue;
                }

                $clean[] = array(
                    'stt_meta_box_image' => isset( $slide['stt_meta_box_image'] ) ? sanitize_text_field( $slide['stt_meta_box_image'] ) : null,
                    'stt_meta_box_title' => isset( $slide['stt_meta_box_title'] ) ? sanitize_text_field( $slide['stt_meta_box_title'] ) : null,
                    'stt_meta_box_time' => isset( $slide['stt_meta_box_time'] ) ? sanitize_text_field( $slide['stt_meta_box_time'] ) : null,
                );
            }

        endif;

        // save slides
        if ( ! empty( $clean ) ) {
            update_post_meta( $post_id, 'repeatable_fields', $clean );
        } else {
            delete_post_meta( $post_id, 'repeatable_fields' );
        }
    }

    public function add_timestamps_slides_meta_box( $post_type ) {

        $post_types = array('selcont_lecture_type');     //limit meta box to certain post type

        if ( in_array( $post_type, $post_types )) {
            add_meta_box(
                'timestamps_slides_meta_box',
                'Slides with timestamps',
                array( $this, 'render_timestamps_slides_meta_box' ),
                $post_type,
                'advanced',
                'default'
            );
        }

    }
    public function render_timestamps_slides_meta_box( $post ) {

        wp_nonce_field( 'netmode_selcont', 'netmode_selcont_nonce' );

        if ( metadata_exists( 'post', $post->ID, 'timestamps_slides_meta_box' ) ) {
            $timestamp_slides = get_post_meta($post->ID, 'timestamps_slides_meta_box', true);
        } else {
            // Backwards compatibility
            $attachment_ids = get_posts( 'post_parent=' . $post->ID . '&numberposts=-1&post_type=attachment&orderby=menu_order&order=ASC&post_mime_type=image&fields=ids' );
            $attachment_ids = array_diff( $attachment_ids, array( get_post_thumbnail_id() ) );
            $timestamp_slides = implode( ',', $attachment_ids );
        }

        $attachments = array_filter( explode( ',', $timestamp_slides ) );

        ?>
        <h2>Upload your slides with timestamps here</h2>
        <br>
        <div id="project_images_container">
            <ul class="project_images">

        <?php

        if ( $attachments )
            foreach ( $attachments as $attachment_id ) {
                echo '<li class="image" data-attachment_id="' . $attachment_id . '">
								' . wp_get_attachment_image( $attachment_id, 'thumbnail' ) . '
								<ul class="actions">
									<li><a href="#" class="delete" title="' . __( 'Delete image', 'selcont' ) . '">&times;</a></li>
								</ul>
							</li>';
            }

        ?>
            </ul>

            <input type="hidden" id="project_image_gallery" name="project_image_gallery" value="<?php echo esc_attr( $timestamp_slides ); ?>" />
        </div>

        <p class="add_project_images hide-if-no-js">
            <a class="button button-primary" href="#"><?php printf( __( 'Add slides', 'selcont' ) ); ?></a>
        </p>

        <hr>

        <?php

        foreach ( $attachments as $aid ) {
            $postObj = get_post($aid);
            echo '<br>';
            echo 'title: ' . $postObj->post_title . '<br>';
            echo 'name: ' . $postObj->post_name . '<br>';
            echo 'url: ' . $postObj->guid . '<br>';
            echo 'id: ' . $postObj->ID . '<br>';
            echo '------------------------------------------------';
        }

    }
    public function save_timestamps_slides_meta_box( $post_id ) {

        if ( ! isset( $_POST['netmode_selcont_nonce'] ) )
            return $post_id;

        $nonce = $_POST['netmode_selcont_nonce'];

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'netmode_selcont' ) )
            return $post_id;

        // If this is an autosave, our form has not been submitted,
        //     so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return $post_id;

        // Check the user's permissions.
        if ( 'page' == $_POST['selcont_lecture_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) )
                return $post_id;
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) )
                return $post_id;
        }

        // Save the lecture slides image IDs.
        $attachment_ids = array_filter( explode( ',', sanitize_text_field( $_POST['project_image_gallery'] ) ) );
        update_post_meta( $post_id, 'timestamps_slides_meta_box', implode( ',', $attachment_ids ) );

        $timestamps = array();
        $timestampsFormat = 'Y_m_d_H-i-s';
        foreach($attachment_ids as $a => $av) {
            $po = get_post($attachment_ids[$a]);
            $ts = date_create_from_format($timestampsFormat, $po->post_name);
            array_push($timestamps, date_timestamp_get($ts));
        }
        sort($timestamps);
        update_post_meta( $post_id, 'timestamps', implode(',', $timestamps) );

        $times = array();
        array_push($times, 0);
        for($z=0; $z<count($timestamps)-1; $z++) {
            $diff = $times[$z] + ($timestamps[$z+1] - $timestamps[$z]);
            array_push($times, $diff);
        }
        update_post_meta( $post_id, 'times', implode(',', $times) );

        $attachmentsArray = array();
        foreach($attachment_ids as $k => $v) {
            $postObj = get_post($attachment_ids[$k]);
            $on = array(
                'slide-id' => $v,
                'slide-url' => $postObj->guid,
                'slide-time' => $times[$k],
                'slide-title' => 'Slide ' . ++$k
            );
            array_push($attachmentsArray, $on);
        }
        update_post_meta( $post_id, 'attachmentsSlides', $attachmentsArray );

    }

}
