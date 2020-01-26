<?php 
   function endbid_redirect() {
       if(get_post_type() == 'product' && ! current_user_can('administrator')) {

        global $post;
        $id = $post->ID;

        $end_time = get_post_meta($id ,'woo_ua_auction_end_date', true);
        $now = strtotime("now");
        $raw_time = strtotime($end_time) - $now;
        
        if($raw_time <= 0) {
            wp_redirect(esc_url(site_url('/')));
        }
       }
   }
?>