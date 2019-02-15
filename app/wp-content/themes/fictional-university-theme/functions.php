<?php

define("USER_CUSTOM_POST_TYPE_NOTE_LIMIT", 4);

function get_description_or_excerpt() {
    if(has_excerpt()) {
        return get_the_excerpt();
      }
      
    return wp_trim_words(get_the_content(), 18);
}

require get_theme_file_path('/inc/search-route.php');

function university_custom_rest() {
    register_rest_field('post', 'authorName', array(
        'get_callback' => function() {return get_the_author();}
    ));

    register_rest_field('post', 'userNoteCount', array(
        'get_callback' => function() {return count_user_posts(get_current_user_id(), 'note');}
    ));
}

add_action('rest_api_init', 'university_custom_rest');



function pageBanner($args = NULL) {
    // php logic will live here

    if (!$args['title']) {
        $args['title'] = get_the_title();
    }

    if (!isset($args['subtitle'])) {
        $args['subtitle'] = get_field('page_banner_subtitle');
    }

    if (isset($args['photo']) && !$args['photo']) {
        if (get_field('page_banner_background_image')) {
        $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
     }

     else {
        $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
    }
}



    ?>
<div class="page-banner">
    <div class="page-banner__bg-image" style="background-image: url(<?php echo $args['photo']; ?>);"></div>
    <div class="page-banner__content container container--narrow">
      <h1 class="page-banner__title"><?php echo $args['title']; ?></h1>
      <div class="page-banner__intro">
        <p><?php echo $args['subtitle']; ?></p>
      </div> 
    </div>  
  </div>
<?php
}

function university_files() {

    wp_enqueue_script('googleMap', '//maps.googleapis.com/maps/api/js?key=AIzaSyCHjR0w0i16utrenMnfHqJjhGMrPJsOgTY', NULL, '1.0', true);
    wp_enqueue_script('main-university-js', get_theme_file_uri('/js/scripts-bundled.js'), NULL, microtime(), true);

    wp_enqueue_style('custom-google-font', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('fontawasome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"');
    wp_enqueue_style('university_main_styles', get_stylesheet_uri(), NULL, microtime()); // only loaded in the front page of the site, no login page or registration page

    wp_localize_script('main-university-js', 'universityData', array(
        'root_url' => get_site_url(),
        'nonce' => wp_create_nonce('wp_rest')
    ));
}


add_action('wp_enqueue_scripts','university_files');

function university_features() {

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_image_size('professorLandscape', 400, 260, true);
    add_image_size('professorPortrait', 480, 650, true);   
    add_image_size('pageBanner', 1500, 350, true);
}

add_action('after_setup_theme', 'university_features');

function university_adjust_queries($query) {

    $today = date('Ymd');

    if (!is_admin() and is_post_type_archive('campus') and  $query->is_main_query()) {
        $query->set('posts_per_page', -1);
 
    }

    if (!is_admin() and is_post_type_archive('event') and  $query->is_main_query()) {
        $query->set('meta_key', 'event_date');
        $query->set('orderby', 'meta_value_num');
        $query->set('order', 'ASC');
        $query->set('meta_key', 'event_date');
        $query->set('meta_query', array(
            array(
              'key' => 'event_date',
              'compare' => '>=',
              'value' => $today,
              'type' => 'numeric'
            )
          ));
    }

    if (!is_admin() and is_post_type_archive('program') and  $query->is_main_query()) {
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
        $query->set('posts_per_page', -1);
 
    }
}

add_action('pre_get_posts', 'university_adjust_queries');

// Redirect subscribers account out of admin and onto homepage
function redirectSubsToFrontend() {
    
    if (university_check_if_user_is_only_subscriber()) 
   {
        wp_redirect(site_url('/'));
        exit; // Useful when redirecting users
    }
}

add_action('admin_init', 'redirectSubsToFrontend');

// Hide admin bar for subscribers only role
add_action('wp_loaded', 'noSubsAdminBar');

function noSubsAdminBar() {
    if (university_check_if_user_is_only_subscriber()) {
        show_admin_bar(false);
    }
}

// customize Login Screen
add_filter('login_headerurl', 'ourHeaderUrl');

function ourHeaderUrl() {
    return esc_url(site_url('/'));
}

function ourLoginCSS() {
    wp_enqueue_style('university_main_styles', get_stylesheet_uri(), NULL, microtime()); // only loaded in the front page of the site, no login page or registration page
    wp_enqueue_style('custom-google-font', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');

}

add_action('login_enqueue_scripts', 'ourLoginCSS');

function ourLoginTitle() {
    return get_bloginfo('name');
}

add_filter('login_headertitle', 'ourLoginTitle');

// Force note posts to be private
// This is also executed when we try to delete the post
add_filter('wp_insert_post_data', 'makeNotePrivate', 10, 2); 
// The '10' value stand for default filter priority. Only useful when you want to prioritize function calls to the same filter
// We want this function to work with 2 parameters

function makeNotePrivate($data, $postarr) {

    if ($data['post_type'] == 'note') {
        if (count_user_posts(get_current_user_id(), 'note') > 4 && !$postarr['ID'])  {
            die("ERR_POST_LIMIT: You have reached your note limit.");
        }


        $data['post_content'] = sanitize_textarea_field($data['post_content']);
        $data['post_title'] = sanitize_textarea_field($data['post_title']);

    }

    if ($data['post_type'] == 'note' && $data['post_status']!= 'trash') {
        $data['post_status'] = 'private';
    }

    return $data;
}

function university_check_if_user_is_only_subscriber() {
    $ourCurrentUser = wp_get_current_user();

    return  (count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber');
}