<?php

    function auction_bidding_history() {
        ob_start();

        global $post;
        $auction_id = $post->ID;
    
        global $wpdb;
        $table_name = $wpdb->prefix . 'woo_ua_auction_log';

        $offers = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$table_name} WHERE auction_id = %d ORDER BY date DESC", $auction_id), ARRAY_A
        );

        // var_dump($offers);
    ?>

    <div class="auction-bidding-history--wrapper">
        <ul class="auction-bidding-history">

        </ul>
    </div>

    <?php
        $contents = ob_get_contents();
        ob_end_clean();

        return $contents;
    }
    add_shortcode('bidding-history', 'auction_bidding_history');