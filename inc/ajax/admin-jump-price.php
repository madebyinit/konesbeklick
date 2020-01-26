<?php 

function admin_jump_price($is_ajax = true, $amount = null, $postid = null, $user_id = null, $proxy = 0) {

    if(isset($_POST['price']) || ! $is_ajax) {

        $price = ! empty($amount) ? $amount : $_POST['price'];
        $id = ! empty($postid) ? $postid : $_POST['id'];
        $userid = ! empty($user_id) ? $user_id : $_POST['userid'];

        global $wpdb;
        $table_name = $wpdb->prefix . 'woo_ua_auction_log';

        $results = $wpdb->get_var($wpdb->prepare(
           "SELECT MAX(bid) FROM {$table_name} WHERE auction_id = %d", $id
        ));

        $bid = substr($results, 0, strpos($results, '.'));

        global $wpdb;
        $table_name = $wpdb->prefix . 'woo_ua_auction_log';

        $qur = $wpdb->query( $wpdb->prepare( 
            "
                INSERT INTO {$table_name}
                ( userid, auction_id, bid, proxy)
                VALUES ( %d, %d, %d, %d )
            ", 
            $userid, 
            $id, 
            $price,
            $proxy
        ));

        $bid_incrament = get_post_meta($id, 'woo_ua_bid_increment', true);
        $bid_incrament = $bid_incrament + $price;

        $now_one_minute = strtotime('now +1 minutes');

        $end_time = get_post_meta($id, 'woo_ua_auction_end_date', true);
        $end = strtotime($end_time);

        if(strtotime('now +5 minutes') >= $end) {

            $new_time = array(
                'hours' => date('H', $now_one_minute),
                'minutes' => date('i', $now_one_minute),
                'sec' => date('s', $now_one_minute)
            );

            update_post_meta($id, 'woo_ua_auction_end_date', date('Y-m-d H:i:s', strtotime($end, ' +1 minutes')));
            
        } else {

            $new_time = array(
                'hours' => date('H', $end),
                'minutes' => date('i', $end),
                'sec' => date('s', $end)
            );

        }

        update_post_meta($id, 'topbid', ($price + $bid));

        if($is_ajax) {
            wp_send_json([$price, $bid_incrament, $id, $userid, $new_time], 200);
        }
    }

    if($is_ajax) {
        wp_send_json(["לא הזנת מחיר"], 200);
    }
}


?>