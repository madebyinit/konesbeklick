<?php

    function auction_bidding_history() {
        ob_start();

        global $post;
        $auction_id = $post->ID;
    
        global $wpdb;
        $table_name = $wpdb->prefix . 'woo_ua_auction_log';

        $offers = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$table_name} WHERE auction_id = %d ORDER BY date ASC", $auction_id), ARRAY_A
        );
    ?>

    <audio id="new-bid-sound" src="<?php echo get_theme_file_uri('/assets/eventually.mp3'); ?>"></audio>

    <div class="auction-bidding-history--wrapper">
        <ul class="auction-bidding-history">
        <?php foreach($offers as $offer_key => $offer): ?>
            <li class="auction-bidding-history--offer <?php echo (count($offers) == $offer_key + 1 ? ' is-highest': ''); ?>" data-bid-id="<?php echo $offer['id']; ?>">

                <?php
                    $offer_timestamp = strtotime($offer['date']);
                    $offer_time = date('H:i', $offer_timestamp);
                    $offer_date = date('d.m.Y', $offer_timestamp);

                    $is_manager = current_user_can('manage_woocommerce') || current_user_can('administrator');
                    $offer_type = $offer['proxy'] == 0 ? ($offer['userid'] == get_current_user_id() ? 'ההצעה שלי ' : (count($offers) == $offer_key + 1 ? ' הצעה מובילה': ' הצעה מתחרה')) : ($offer['proxy'] == 1 ? ($is_manager ? 'אני' : 'הקפצה מהאינטרנט') : ($is_manager ? 'אני' : 'הקפצה מהאולם'));
                ?>

                <div>
                    <span>
                        <?php echo $offer_type; ?> 
                        <?php printf('%s | %s', $offer_time, $offer_date); ?>
                    </span>

                    <?php if($is_manager && $offer['proxy'] == 0): ?>
                    <small>
                        <?php
                        printf('%s | %s', get_user_meta( $offer['userid'], 'first_name', true )
                        .  ' ' . get_user_meta( $offer['userid'], 'last_name', true), get_user_meta($offer['userid'], 'billing_phone', true));
                        ?>
                    </small>
                    <?php endif; ?>
                </div>

                <strong>
                    <?php printf('%s ₪', number_format($offer['bid'], 0, '.', ',')); ?>
                </strong>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>

    <?php
        $contents = ob_get_contents();
        ob_end_clean();

        return $contents;
    }
    add_shortcode('bidding-history', 'auction_bidding_history');