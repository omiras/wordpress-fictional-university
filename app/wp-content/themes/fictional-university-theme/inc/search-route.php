<?php

add_action('rest_api_init', 'universityRegisterSearch');

function universityRegisterSearch() {
  register_rest_route('university/v1', 'search', array(
    'methods' => WP_REST_SERVER::READABLE,
    'callback' => 'universitySearchResults'
  ));
}

function universitySearchResults($data) {
  $mainQuery = new WP_Query(array(
    'post_type' => array('post', 'page', 'professor', 'program', 'campus', 'event'),
    's' => sanitize_text_field($data['term'])
  ));

  $results = array(
    'generalInfo' => array(),
    'professors' => array(),
    'programs' => array(),
    'events' => array(),
    'campuses' => array()
  );

/** Keys: slug of each post type
 *  Values: the desired label for the key to output the JSON
 */

  $labels = array(
    'post' => 'generalInfo',
    'page' => 'generalInfo',
    'professor' => 'professors',
    'program' => 'programs',
    'campus' => 'campuses',
    'event' => 'events'
  );

  while($mainQuery->have_posts()) {
    $mainQuery->the_post();

    $post_type_group = $labels[get_post_type()];

    $post_array = array(
      'title' => get_the_title(),
      'permalink' => get_the_permalink(),
      'postType' => get_post_type(),
      'authorName' =>    get_the_author()
    );

    if (get_post_type() == "professor") {
      $post_array['image'] = get_the_post_thumbnail_url(0, 'professorLandscape');
    }

    else if (get_post_type() == "event") {

      $EventDate = new DateTime(get_field('event_date', false, false));

      $post_array['month'] = $EventDate->format('M');
      $post_array['day'] = $EventDate->format('d');
      $post_array['description'] = get_description_or_excerpt();
    }

    else if (get_post_type() == "program") {
      $post_array['id'] = get_the_id();

      $relatedCampuses = get_field('related_campus');

      foreach($relatedCampuses as $campus) {
        array_push($results['campuses'], array(
          'title' => get_the_title($campus),
          'permalink' => get_the_permalink($campus)
        ));
      }
    }

    array_push($results[$post_type_group], $post_array);
       
  }

  if ($results['programs']) {
    $programsMetaQuery = array('relation' => 'OR');

    foreach($results['programs'] as $item) {
      array_push($programsMetaQuery, array(
        'key' => 'related_programs',
        'compare' => 'LIKE',
        'value' => '"' . $item['id'] . '"'
      ));
    }
  
    $programRelationshipQuery = new WP_QUERY(array(
      'post_type' => array('professor', 'event'),
      'meta_query' => $programsMetaQuery
    ));
  
    while($programRelationshipQuery->have_posts()) {
      $programRelationshipQuery->the_post();
  
      if (get_post_type() == "professor") {

      array_push($results['professors'], array(
      'title' => get_the_title(),
      'permalink' => get_the_permalink(),
      'postType' => get_post_type(),
      'authorName' => get_the_author(),
      'image' => get_the_post_thumbnail_url(0, 'professorLandscape')
      ));

      }

      else {
        $EventDate = new DateTime(get_field('event_date', false, false));

        array_push($results['events'], array(

        'title' => get_the_title(),
        'permalink' => get_the_permalink(),
        'postType' => get_post_type(),
        'month' =>$EventDate->format('M'),
        'day' => $EventDate->format('d')
        ));
      }
    }
  
    $results['professors'] = array_values(array_unique($results['professors'], SORT_REGULAR));
    $results['events'] = array_values(array_unique($results['events'], SORT_REGULAR));

  }


  return $results;

}