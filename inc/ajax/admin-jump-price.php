<?php 

function admin_jump_price() {
    if(isset($_POST['price'])) {

        $price = $_POST['price'];
        $id = $_POST['id'];
        $userid = $_POST['userid'];
        $bid = get_post_meta($id, 'topbid', true);

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
            0 
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

        update_post_meta($id, 'topbid', $price);
        wp_send_json([$price, $bid_incrament, $id, $userid, $new_time], 200);
    }

    wp_send_json(["לא הזנת מחיר"], 200);
}


?>