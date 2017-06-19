<?php
/**
* Plugin Name: OSmod
* Plugin URI:
* Description: A custom plugin to integrate with the OSMOD
* Version: 1.0
**/

// Make sure we don't expose any info if called directly
defined('ABSPATH') or die('Exiting due to possible exploit.');
if (!function_exists('add_action')) {
  echo 'Hello, You cannot call me directly.';
  exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('OSMOD_PLUGIN_DIR', plugin_dir_path( __FILE__ ));

// Require OSMOD methods
require_once(OSMOD_PLUGIN_DIR . 'class.comments.php');
require_once(OSMOD_PLUGIN_DIR . 'class.articles.php');
require_once(OSMOD_PLUGIN_DIR . 'class.settings.php');

// Require OSMOD core
require_once(OSMOD_PLUGIN_DIR . 'class.osmod.php');
add_action('init', array('Osmod', 'init'));

// Obtain Settings
global $settings;
$settings = get_option('osmod_option');

// New comment
add_action('wp_insert_comment', 'comment_inserted', 99, 2);
function comment_inserted($comment_id, $comment_object) {
  if ($comment_object->comment_content) {
    (new Comments)->newComment($comment_object);
  }
}

// New article / updated article hook
add_action('publish_post', 'article_inserted', 10, 2);
function article_inserted($post_id, $post) {
  if ($post->post_date != $post->post_modified) {
    (new Articles)->updateArticle($post);
  } else {
    (new Articles)->newArticle($post);
  }
}

// New category
add_action('create_category', 'category_inserted');
function category_inserted($id) {
  (new Articles)->newArticleCategory($id);
}

// add_action('sync_osmod', 'execute_sync_osmod');
function execute_sync_osmod() {
  error_log("EXECUTE SYNC OSMOD!", 0);
  $decisions_json = (new Comments)->getDecisions();
  $json = json_decode($decisions_json['body'], true);

  $decisions = $json['data'];
  $included = $json['included'];

  $ids = array();

  foreach ($decisions as $decision) {
    $comment_id = $decision['attributes']['commentId'];
    $source_id = null;

    foreach ($included as $comment) {
      if ($comment['id'] == $comment_id) {
        $source_id = intval($comment['attributes']['sourceId']);
        break;
      }
    }

    $status = $decision['attributes']['status'];

    $comment = get_comment($source_id);

    if ($comment) {
      if ($status == 'Accept' || $status == 'Reject') {
        if ($status == 'Accept') {
          wp_set_comment_status($source_id, 'approve');
        } else if ($status == 'Reject') {
          wp_set_comment_status($source_id, 'trash');
        } else {
          echo $status;
        }

        array_push($ids, $decision['id']);
      }
    }
  }

  if (count($ids) > 0) {
    (new Comments)->resolveDecisions($ids);
  }
}

execute_sync_osmod();

// add_filter('cron_schedules', 'add_per_minute_cron');
// function add_per_minute_cron($schedules) {
//   $schedules['every_minute'] = array(
//     'interval' => 60,
//     'display'  => esc_html__('Every Sixty Seconds'),
//   );

//   return $schedules;
// }

// if (!wp_next_scheduled('sync_osmod')) {
// 	wp_schedule_event(time(), 'every_minute', 'sync_osmod');
// }

// function handle_decision($comment_id, $decision) {
//   $comment = get_comment($comment_id);
//   if ($comment) {
//     if ($decision == 'accepted' || $decision == 'rejected') {
//       // WP comment status, either 'hold', 'approve', 'spam', or 'trash'.
//       if ($json->status == 'accepted') {
//         wp_set_comment_status($comment_id, 'approve');
//       } else if ($json->status == 'rejected') {
//         wp_set_comment_status($comment_id, 'trash');
//       }
//     }
//   }
// }


// // API endpoint for approve and reject
// add_action( 'template_redirect', 'api_endpoint' );

// function api_endpoint() {

//     $query = add_query_arg( NULL, NULL );
//     if (strpos($query, '/v1/comment-review/') !== false) {
//         $parts = explode("/",$query);
//         $comment = get_comment($requestedId);
//         if ($comment) {
//             $json = json_decode(file_get_contents("php://input"));
//             if ($json->status == 'accepted' || $json->status == 'rejected') {
//                 // WP comment status, either 'hold', 'approve', 'spam', or 'trash'.
//                 if ($json->status == 'accepted') {
//                     wp_set_comment_status($requestedId,'approve');
//                 } else if ($json->status == 'rejected') {
//                     wp_set_comment_status($requestedId,'trash');
//                 }

//                 header("HTTP/1.1 200 OK");
//                 wp_send_json(array(
//                     'success'   => true
//                 ));
//             } else {
//                 header("HTTP/1.1 422 Unprocessable entity");
//                 wp_send_json(array(
//                     'success'   => false,
//                     'error'     => 'Incorrect status passed. Valid options: (accepted or rejected).'
//                 ));
//             }
//         } else {
//             header("HTTP/1.1 422 Unprocessable entity");
//             wp_send_json(array(
//                 'success'   => false,
//                 'error'     => 'Could not find comment by ID : ' . $requestedId
//             ));
//         }
//     }

// }

// Prepare for default settings.
if (is_admin()) {
  // Ensure comments are set to manual moderation
  update_option("comment_moderation", 1, true);

  // Ensure comments include name and email. This is required by the OSMOD data model.
  update_option("require_name_email", 1, true);

  // Init Settings Page
  new OsmodSettings();
}
