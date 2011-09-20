<?php if ( ! defined('WP_CONTENT_DIR')) exit('No direct script access allowed'); 
/*------------------------------------------------------------------------------
Information about this plugin
// Too late!
//wp_enqueue_style( 'google-charts', 'https://www.google.com/jsapi');
// wp_enqueue_script( 'google-charts');

http://code.google.com/apis/chart/interactive/docs/gallery/piechart.html
------------------------------------------------------------------------------*/
$data=array();
$data['page_title'] = __('Information', CCTM_TXTDOMAIN);
$data['msg'] = '';
$data['menu'] = '';

global $wpdb;
$query = "SELECT post_type, count(*) as 'cnt' FROM {$wpdb->posts} WHERE post_type NOT IN ('revision','nav_menu_item') GROUP BY post_type";
$data['results'] = $wpdb->get_results( $query, OBJECT );

$pts = get_post_types();
$data['active_cnt'] = count($pts);
$data['all_cnt'] = count($data['results']);

$data['inactive_cnt'] = $data['all_cnt'] - $data['active_cnt'];

foreach ($data['results'] as &$r) {
	if ( !in_array($r->post_type, $pts) ) {
		$r->post_type = $r->post_type . ' (disabled)';
	}
}

$data['content'] = CCTM::load_view('info.php', $data);
print CCTM::load_view('templates/default.php', $data);
/*EOF*/

