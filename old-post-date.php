<?php
/*
Plugin Name: old_post_date
Plugin URI: http://wordpress.org/extend/plugins/old-post-date/
Description: Just as the core WordPress functionality now keeps track of old post slugs and redirects visitors to proper permalink, this plugin does the same thing for the post_date. When a visitor gets a 404 from a permalink that contains chronology, this plugin looks up the post meta for "old_post_date" and if found and the slug matches the current slug or one from _wp_old_slug, then the user is redirected to the requested post.
Version: 1.0.3
Author: Weston Ruter
Author URI: http://weston.ruter.net/
Copyright: 2008, Weston Ruter

GNU General Public License, Free Software Foundation <http://creativecommons.org/licenses/GPL/2.0/>
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


function old_post_date_activate(){
	global $wpdb;

	#For each post in the database, save the current post_date
	$post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts");
	foreach($post_ids as $post_id){
		$post = get_post($post_id);
		old_post_date_save_post($post_id, $post);
	}
}
register_activation_hook(__FILE__, 'old_post_date_activate');

function old_post_date_check(){
	global $wp_query, $wpdb;
	if(is_404()){
		$year = get_query_var('year');
		$month = get_query_var('monthnum');
		$day = get_query_var('day');
		
		#Create a regular expression to find the old post_date
		$regex = '^';
		$regex .= $year ? $year : '[:character_class:]+';
		$regex .= '-';
		$regex .= $month ? sprintf('%02d', $month) : '[:character_class:]+';
		$regex .= '-';
		$regex .= $day ? sprintf('%02d', $day): '[:character_class:]+';
			
		#Note: in the future we can add time granularity if desired
		$regex .= ' ';
		
		$redir_post_id = null;
		
		#Matching posts from old_post_id alone
		$post_ids = $wpdb->get_col("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'old_post_date' AND meta_value RLIKE '$regex'");
		
		#If only one match, then automatically choose it
		if(count($post_ids) == 1){
			$redir_post_id = $post_ids[0];
		}
		#If name provided, we should check post_name as well
		else if($name = get_query_var('name') && !empty($post_ids)){
			$post_ids_with_same_name = ($wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE ID in (" . join(',', $post_ids) . ") AND post_name = '" . $wpdb->escape($name) . "'"));
			#See if current post_name matches
			if(!empty($post_ids_with_same_name)){
				$redir_post_id = array_pop($post_ids_with_same_name);
			}
			#otherwise, search _wp_old_slug as well
			else {
				$post_ids_with_same_old_name = ($wpdb->get_col("SELECT ID FROM $wpdb->posts,$wpdb->postmeta WHERE ID in (" . join(',', $post_ids) . ") AND ID = post_id AND meta_key = '_wp_old_slug' AND meta_value = '" . $wpdb->escape($name) . "'"));
				if(!empty($post_ids_with_same_old_name)){
					$redir_post_id = array_pop($post_ids_with_same_old_name);
				}
			}
		}
		
		#Last resort
		if(empty($redir_post_id) && !empty($post_ids)) {
			$redir_post_id = array_pop($post_ids);
		}
		
		#Redirect to the post with $redir_post_id
		if($redir_post_id && ($redir_url = get_permalink($redir_post_id)))
			wp_redirect($redir_url, 301);
	}
}
add_action('template_redirect', 'old_post_date_check');


#Store the current post_date in the postmeta as old_post_date if not stored already
function old_post_date_save_post($post_id, $post){
	$old_post_date = get_post_meta($post_id, 'old_post_date', false);
	if(empty($old_post_date))
		$old_post_date = array();
	
	if(!in_array($post->post_date, $old_post_date) && $post->post_status != 'draft')
		add_post_meta($post_id, 'old_post_date', $post->post_date, false);
}
add_action('save_post', 'old_post_date_save_post', 10, 2);

?>