<?php 

function admin_jump_time() {

    if(isset($_POST['time'])) {
        $time = $_POST['time'] * 60;
        $id = $_POST['id'];
        $oldTime = strtotime(get_post_meta($id ,'woo_ua_auction_end_date', true)); 
        $newTime = date("Y-m-d H:i:s", ($oldTime + $time));
        update_post_meta( $id, 'woo_ua_auction_end_date', $newTime);

        // calculate time for ajax update

    $end_second = ($oldTime + $time) - strtotime("now");
    $end_minute = floor($end_second / 60);
    $left_second = fmod($end_second, 60);
    $end_hours = floor($end_minute / 60);
    $left_minute = fmod($end_minute, 60);

    wp_send_json([$end_hours, $left_minute, $left_second, $_POST['postid']], 200);
    } 

    elseif (isset($_POST['closed'])) {
        $id = $_POST['id'];
        $now = date("Y-m-d H:i:s");
        update_post_meta( $id, 'woo_ua_auction_end_date', $now);
        update_post_meta( $id, 'woo_ua_lowest_price', 10000000);

        wp_update_post(array(
            'ID' =>  $id,
            'post_status' => 'private'
        ));
        
        wp_send_json('המכרז יסתיים תוך מספר שניות...', 200);
    }
    
}

?>