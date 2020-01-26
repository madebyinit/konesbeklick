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

        update_post_meta($auction_id, 'topbid', $price);
        wp_send_json([$price, $bid_incrament, $id, $userid], 200);
    }

    wp_send_json(["לא הזנת מחיר"], 200);
}


?>