<?php
function hello_elementor_child_enqueue_styles() {
    wp_enqueue_style('hello-elementor-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('hello-elementor-child-style', get_stylesheet_directory_uri() . '/style.css', array('hello-elementor-style'));
}
add_action('wp_enqueue_scripts', 'hello_elementor_child_enqueue_styles');

function get_post_id_shortcode() {
    return get_the_ID();
}
add_shortcode('post_id', 'get_post_id_shortcode');

function get_post_title_shortcode() {
    return get_the_title();
}
add_shortcode('post_title', 'get_post_title_shortcode');

function get_lesson_status_shortcode() {
    global $post;
    return get_post_meta($post->ID, 'status', true);
}
add_shortcode('lesson_status', 'get_lesson_status_shortcode');

function get_lesson_date_shortcode() {
    global $post;
    return get_post_meta($post->ID, 'date', true);
}
add_shortcode('lesson_date', 'get_lesson_date_shortcode');

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

function submit_lesson_form() {
    if (!empty($_POST['title']) && !empty($_POST['status']) && !empty($_POST['date'])) {
        $title = sanitize_text_field($_POST['title']);
        $status = sanitize_text_field($_POST['status']);
        $date = sanitize_text_field($_POST['date']);

        $new_post = array(
            'post_title'   => $title,
            'post_content' => '',
            'post_status'  => 'publish',
            'post_type'    => 'lesson',
            'meta_input'   => array(
                'status' => $status,
                'date'   => $date,
            ),
        );

        $post_id = wp_insert_post($new_post);

        if (is_wp_error($post_id)) {
            wp_send_json_error(array('message' => 'Error creating lesson: ' . $post_id->get_error_message()));
        } else {
            wp_send_json_success(array('message' => 'Lesson created successfully!'));
        }
    } else {
        wp_send_json_error(array('message' => 'Error creating lesson. Please try again.'));
    }

    wp_die();
}

add_action('wp_ajax_submit_lesson_form', 'submit_lesson_form');
add_action('wp_ajax_nopriv_submit_lesson_form', 'submit_lesson_form');


function update_lesson_data() {
    if (!current_user_can('edit_post', $_POST['post_id'])) {
        wp_send_json_error(['message' => 'Permission denied.']);
    }

    $post_id = intval($_POST['post_id']);
    $title = sanitize_text_field($_POST['title']);
    $status = sanitize_text_field($_POST['status']);
    $date = sanitize_text_field($_POST['date']);

    if (!empty($title) && !empty($status) && !empty($date)) {
        $post_data = [
            'ID'           => $post_id,
            'post_title'   => $title,
        ];

        $updated_post_id = wp_update_post($post_data);

        if (is_wp_error($updated_post_id)) {
            wp_send_json_error(['message' => 'Post update failed.']);
        } else {
            update_post_meta($post_id, 'status', $status);
            update_post_meta($post_id, 'date', $date);
            wp_send_json_success(['message' => 'Post updated successfully!']);
        }
    }else {
        wp_send_json_error(['message' => 'Please fill out all fields']);
    }

}

add_action('wp_ajax_update_lesson_data', 'update_lesson_data');
add_action('wp_ajax_nopriv_update_lesson_data', 'update_lesson_data');

function delete_lesson() {

    if (!current_user_can('edit_post', $_POST['post_id'])) {
        wp_send_json_error(['message' => 'Permission denied.']);
    }

    $post_id = intval($_POST['post_id']);


    if (!empty($post_id)) {

        $deleted = wp_delete_post($post_id, true);
        if ($deleted) {
            wp_send_json_success(['message' => 'Post deleted successfully!']);
        } else {
            wp_send_json_error(['message' => 'Post deletion failed.']);
        }
    }else {
        wp_send_json_error(['message' => 'Post deletion failed.']);
    }

}

add_action('wp_ajax_delete_lesson', 'delete_lesson');
add_action('wp_ajax_nopriv_delete_lesson', 'delete_lesson');
