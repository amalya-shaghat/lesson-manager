<?php
function hello_elementor_child_enqueue_styles() {
    // Enqueue the parent theme style
    wp_enqueue_style('hello-elementor-style', get_template_directory_uri() . '/style.css');

    // Enqueue the child theme style
    wp_enqueue_style('hello-elementor-child-style', get_stylesheet_directory_uri() . '/style.css', array('hello-elementor-style'));
}
add_action('wp_enqueue_scripts', 'hello_elementor_child_enqueue_styles');

function create_lesson_post_type() {
    register_post_type('lesson',
        array(
            'labels' => array(
                'name' => __('Lessons'),
                'singular_name' => __('Lesson')
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'custom-fields'),
            'menu_icon' => 'dashicons-book',
        )
    );
}
add_action('init', 'create_lesson_post_type');


function enqueue_ajax_script() {
    wp_enqueue_script('lesson-form-ajax', get_theme_file_uri('/assets/js/script.js'), array('jquery'), null, true);
    wp_localize_script('lesson-form-ajax', 'my_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'enqueue_ajax_script');

function handle_lesson_submission() {
    if (isset($_POST['title']) && isset($_POST['status']) && isset($_POST['date'])) {

        $title = sanitize_text_field($_POST['title']);
        $status = sanitize_text_field($_POST['status']);
        $date = sanitize_text_field($_POST['date']);

        $post_id = wp_insert_post(array(
            'post_title'    => $title,
            'post_status'   => 'publish',
            'post_type'     => 'lesson',
            'meta_input'    => array(
                'status' => $status,
                'date'   => $date,
            ),
        ));

        if ($post_id) {
            wp_send_json_success(array('message' => 'Lesson created successfully.'));
        } else {
            wp_send_json_error(array('message' => 'Failed to create lesson.'));
        }
    } else {
        wp_send_json_error(array('message' => 'Incomplete form data.'));
    }

    wp_die();
}
add_action('wp_ajax_submit_lesson_form', 'handle_lesson_submission');
add_action('wp_ajax_nopriv_submit_lesson_form', 'handle_lesson_submission');

