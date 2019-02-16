<?php

add_action('rest_api_init', 'universityLikeRoutes'); // Add a new custom route

function universityLikeRoutes() {
    register_rest_route('university/v1', 'manageLike', array(
        'methods' => 'POST', // Type of Http Request
        'callback' => 'createLike'
    ));

    register_rest_route('university/v1', 'manageLike', array(
        'methods' => 'DELETE',
        'callback' => 'deleteLike'
    ));
}

function createLike($data) {

    if (!is_user_logged_in()) {
        die("Only logged in users can use like functionality.");
    }

    $professorId = sanitize_text_field($data['professorId']);

    $existsQuery = new WP_Query(array(
        'author' => get_current_user_id(),
        'post_type' => 'like',
        'meta_query' => array(
          array(
            'key' => 'liked_professor_id',
            'compare' => '=',
            'value' => $professorId
          )
        )
      ));

    if ($existsQuery->found_posts && get_post_type($professorId) == 'professor') {
        die("Invalid professor ID: Professor has been already liked!");
    }

    return wp_insert_post(array(
        'post_type' => 'like',
        'post_status' => 'publish',
        'post_title' => '2nd PHP TEST',
        'meta_input' => array( 
            'liked_professor_id' => $professorId
        ) // Native way of WP to add custom field. We can use it to reference already created advanced custom fields
    ));
}

function deleteLike($data) {
    $likeId = sanitize_text_field($data['like']);

    if (get_current_user_id() == get_post_field('post_author', $likeId) && get_post_type($likeId) == 'like') {

    wp_delete_post($likeId, true); // Skip the trash step
    return 'Congrats! Like deleted';
    }

    else {
        die('You do not have permission to delete that.');
    }
}