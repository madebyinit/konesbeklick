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

      register_rest_route('shimi/v1', 'bidding-history', array(
         'methods' => WP_REST_Server::READABLE,
         'callback' => 'singleProductBiddingHistory'
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

         global $wpdb;
         $table_name = $wpdb->prefix . 'woo_ua_auction_log';

         $results = $wpdb->get_var($wpdb->prepare(
            "SELECT MAX(bid) FROM {$table_name} WHERE auction_id = %d", $id
         ));
 
         $top_bid = substr($results, 0, strpos($results, '.'));
 
         if(! $top_bid || empty($top_bid)) {
            $top_bid = get_post_meta($id, 'woo_ua_opening_price', true);
         }

         $topBID[] = $top_bid;
      }

      return $topBID;
      
     }

     function singleProductBiddingHistory($data) {

         global $wpdb;
         $table_name = $wpdb->prefix . 'woo_ua_auction_log';

         $auction_id = esc_html($_GET['postid']);
         $current_view_bid = esc_html($_GET['lastbid']);
         $user_id = esc_html($_GET['userid']);

         $offers = $wpdb->get_results(
             $wpdb->prepare("SELECT * FROM {$table_name} WHERE auction_id = %d AND id > %d ORDER BY date ASC", $auction_id, $current_view_bid), ARRAY_A
         ); 

         ob_start();
         $contents = '';

         if(! empty($offers)) {
         ?>
            <?php foreach($offers as $offer_key => $offer): ?>
               <li class="auction-bidding-history--offer <?php echo (count($offers) == $offer_key + 1 ? ' is-highest': ''); ?>" data-bid-id="<?php echo $offer['id']; ?>" style="display: none;">
   
                   <?php
                       $offer_timestamp = strtotime($offer['date']);
                       $offer_time = date('H:i', $offer_timestamp);
                       $offer_date = date('d.m.Y', $offer_timestamp);
   
                       $is_manager = user_can($user_id, 'manage_woocommerce') || user_can($user_id, 'administrator');
                       $offer_type = $offer['proxy'] == 0 ? (count($offers) == $offer_key + 1 ? ' הצעה מובילה': ' הצעה מתחרה') : ($offer['proxy'] == 1 ? ($is_manager ? 'אני' : 'הקפצה מהאינטרנט') : ($is_manager ? 'אני' : 'הקפצה מהאולם'));
                   ?>
   
                   <div>
                       <span>
                           <?php echo $offer_type; ?> 
                           <?php printf('%s | %s', $offer_time, $offer_date); ?>
                       </span>
   
                       <?php if($is_manager && $offer['proxy'] == 0): ?>
                       <small>
                           <?php
                               $user_info = get_userdata($offer['userid']);
                               printf('%s | %s', $user_info->first_name .  ' ' . $user_info->last_name, get_user_meta($offer['userid'], 'billing_phone', true));
                           ?>
                       </small>
                       <?php endif; ?>
                   </div>
   
                   <strong>
                       <?php printf('%s ₪', number_format($offer['bid'], 0, '.', ',')); ?>
                   </strong>
               </li>
           <?php 
           endforeach;

           $contents = ob_get_contents();
           ob_end_clean();

           return $contents;
         }

         return false;
     }

?>