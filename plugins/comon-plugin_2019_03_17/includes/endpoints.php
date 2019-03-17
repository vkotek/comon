<?php
/*
Nicename here.
*/

/* Custom REST API endpoints */


// Custom API endpoint to get Version of current plugins, wordpress, theme.
function api_get_versions($data){
  return null;
}

function api_get_post_by_author($data) {
  $posts = get_posts(array(
    'author' => $data['id'],
  ));
  if ( empty($posts) ){
    return null;
  }
  return $posts;
}

add_action('rest_api_init', function () {
  register_rest_route( 'comonplugin/v1', '/author/(?P<id>\d+)', array(
    'methods' => 'GET',
    'callback' => 'api_get_post_by_author',
  ));
} );

?>
