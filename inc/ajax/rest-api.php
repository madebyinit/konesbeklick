<?php 

function shimiTimer() {
    register_rest_route('shimi/v1', 'timer', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'shimiApTime'
        ));

      register_rest_route('shimi/v1', 'grid-home-update', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => 'gridHomeUPDATE'
      ));

   }

     function shimiApTime($data) {
      //   $end_time = get_post_meta($data['postid'] ,'woo_ua_auction_end_date', true);
      //   $end_second = round((strtotime($end_time) - strtotime("now")));
      //   $end_minute = floor($end_second / 60);
      //   $left_second = fmod($end_second, 60);
      //   $end_hours = floor($end_minute / 60);
      //   $left_minute = fmod($end_minute, 60);
        $id = $data['postid'];
        $end_time = get_post_meta($id ,'woo_ua_auction_end_date', true);
            $raw_time = strtotime($end_time) - strtotime("now");
            $end_days = $raw_time / 60 / 60 / 24;
            $end_hours = ($end_days - intval($end_days)) * 24;
            $end_minutes = ($end_hours - intval($end_hours)) * 60;
            $end_sec = ($end_minutes - intval($end_minutes)) * 60;
            $end_hours = ($end_hours - 2) > 0 ? $end_hours - 2 : 0;

        global $wpdb;
        $table_name = $wpdb->prefix . 'woo_ua_auction_log';
        $results = $wpdb->get_var($wpdb->prepare(
           "SELECT MAX(bid) FROM {$table_name} WHERE auction_id = %d", $id
        ));

        $row = $wpdb->get_row($wpdb->prepare(
         "SELECT * FROM {$table_name} WHERE auction_id = %d ORDER BY date DESC" , $id
      ));

        $topbid = substr($results, 0, strpos($results, '.'));

        if(! $topbid || empty($topbid) || $topbid == '0') {
         $topbid = get_post_meta($id, 'woo_ua_opening_price', true);
        }

        $times = array(
           'hours'   => floor($end_days * 24),
           'minutes' => floor($end_minutes),
           'sec'     => floor($end_sec)
        );

      //   the minimum bid incrament:
        $bid_incrament = get_post_meta($id, 'woo_ua_bid_increment', true);
        $user_id = get_current_user_id();

        return array(
           $times,
           $id,
           $topbid,
           $row,
           $bid_incrament,
         );
     }

     function gridHomeUPDATE($param) {

      $topBID = array();

      $IDs = explode(",", $param['id']);

      foreach($IDs as $id) {
         $top_bid = get_post_meta($id, 'topbid', true);

         if(! $top_bid || empty($top_bid)) {
            $top_bid = get_post_meta($id, 'woo_ua_opening_price', true);
         }

         $topBID[] = $top_bid;
      }

      return $topBID;
      
     }

     

?>