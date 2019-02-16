<?php

add_action('after_setup_theme', 'university_features');

function university_post_types() {
    register_post_type('event', array(
        'capability_type' => 'event', // new capability for event
        'map_meta_cap' => true, // Event post type requieres this capability 'event' 
        'supports' => array('title', 'editor', 'excerpt'),
        'rewrite' => array('slug' => 'events'),
        'has_archive' => true,
        'public' => true,
        'labels' => array(
            'name' => 'Events',
            'add_new_item' => 'Add New Event',
            'edit_item' => 'Edit Event',
            'all_items' => 'All Events',
            'singular_name' => 'Event'
        ),
        'menu_icon' => 'dashicons-calendar'
    ));

    register_post_type('program', array(
        'supports' => array('title'),
        'rewrite' => array('slug' => 'programs'),
        'has_archive' => true,
        'public' => true,
        'labels' => array(
            'name' => 'Programs',
            'add_new_item' => 'Add New Program',
            'edit_item' => 'Edit Program',
            'all_items' => 'All Programs',
            'singular_name' => 'Program'
        ),
        'menu_icon' => 'dashicons-awards'
    ));

    // We do not need and archive template for this custom post. We can get rid of the lines
    // that rewrite the slug and the has_archive property

    register_post_type('professor', array(
        'supports' => array('title', 'editor', 'thumbnail'),
        'show_in_rest' => true,
        'public' => true,
        'labels' => array(
            'name' => 'Professors',
            'add_new_item' => 'Add New Professor',
            'edit_item' => 'Edit Professor',
            'all_items' => 'All Professors',
            'singular_name' => 'professor'
        ),
        'menu_icon' => 'dashicons-welcome-learn-more'
    ));

    register_post_type('campus', array(
        'capability_type' => 'campus',
        'map_meta_cap' => true,
        'supports' => array('title', 'editor', 'excerpt'),
        'rewrite' => array('slug' => 'campuses'),
        'has_archive' => true,
        'public' => true,
        'labels' => array(
            'name' => 'Campuses',
            'add_new_item' => 'Add New Campus',
            'edit_item' => 'Edit Campus',
            'all_items' => 'All Campuses',
            'singular_name' => 'Campus'
        ),
        'menu_icon' => 'dashicons-location-alt'
    ));

    register_post_type('note', array(
        'supports' => array('title', 'editor'),
        'capability_type' => 'note',
        'map_meta_cap' => true,
        'show_in_rest' => true, //work custom post type in the API
        'public' => false, // we want our notes to be private and specific
        'show_ui' => true, // Show in the admin dashboard UI
        'labels' => array(
            'name' => 'Notes',
            'add_new_item' => 'Add New Notes',
            'edit_item' => 'Edit Notes',
            'all_items' => 'All Notes',
            'singular_name' => 'Note'
        ),
        'menu_icon' => 'dashicons-welcome-write-blog'
    ));

    register_post_type('like', array(
        'supports' => array('title'),
        'public' => false, // we want our notes to be private and specific
        'show_ui' => true, // Show in the admin dashboard UI
        'labels' => array(
            'name' => 'Like',
            'add_new_item' => 'Add New Like',
            'edit_item' => 'Edit Like',
            'all_items' => 'All Likes',
            'singular_name' => 'Like'
        ),
        'menu_icon' => 'dashicons-heart'
    ));
}

add_action('init', 'university_post_types'); 

function universityMapKey($api) {
    $api['key'] = 'AIzaSyCHjR0w0i16utrenMnfHqJjhGMrPJsOgTY';
    return $api;
}

add_filter('acf/fields/google_map/api', 'universityMapKey');