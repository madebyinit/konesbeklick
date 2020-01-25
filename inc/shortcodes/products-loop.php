<?php 

function shimi_loop($atts) { 

    $a = shortcode_atts( array(
		'showall' => 'no'
	), $atts );
    
    $html = '<ul class="products products__grid">';
    
        $loop = new WP_Query(array(
            'post_type' => 'product',
			'posts_per_page' => -1
        ));

            while($loop->have_posts()) {
            $loop->the_post(); 

            $id = $loop->post->ID;  
            
            $end_time = get_post_meta($id ,'woo_ua_auction_end_date', true);
            $raw_time = strtotime($end_time) - strtotime("now");
            $end_days = $raw_time / 60 / 60 / 24;
            $end_hours = ($end_days - intval($end_days)) * 24;
            $end_minutes = ($end_hours - intval($end_hours)) * 60;
            $end_hours = ($end_hours - 2) > 0 ? $end_hours - 2 : 0;

            $href = $loop->posts[$loop->current_post]->guid; 
            $image = wp_get_attachment_image_src( get_post_thumbnail_id( $loop->post->ID ), 'shop_catalog' );
            $carName = $loop->posts[$loop->current_post]->post_title; 
            
            global $wpdb;
            $table_name = $wpdb->prefix . 'woo_ua_auction_log';
            $results = $wpdb->get_var($wpdb->prepare(
                "SELECT MAX(bid) FROM {$table_name} WHERE auction_id = %d", $id
            ));

            $topbid = substr($results, 0, strpos($results, '.'));

            if($a['showall'] === 'no') {
                if(floor($end_days) <= 0 && floor($end_hours) <= 0 && floor($end_minutes) <= 0) {
                    continue;
                }
            }
    ?>
    <?php ob_start(); ?>
    <li class='shadow' data-id='<?php echo $id ?>'>
        <a href='<?php echo $href ?>' style='display:block;'>
            <div class='products__image' style='background-image: url(<?php echo $image[0] ?>);' data-id='<?php echo $id ?>'>
                <h4 class='products__offer'><?php printf('הצעה מובילה: %s %s', '<span class="numToFormat">' . $topbid . '</span>', '<span>₪</span>') ?></h4>
            </div>
            <div class='products__info'>
                <h4><?php printf('%s ימים <span>:</span> %s שעות <span>:</span> %s דקות', "<span class='the-time'>" . floor($end_days) . "</span>", "<span class='the-time'>" . floor($end_hours) . "</span>", "<span class='the-time'>" . floor($end_minutes) . "</span>"); ?></h4>
                <h3><?php echo $carName ?></h3>
                <h4><?php printf('שנתון: %s', get_field('year', $id)) ?></h4>
                <h4><?php printf('מחיר מחירון: %s', '<span class="numToFormat">' . get_field('price', $id) . '</span>') ?></h4>
            </div>
        </a>
        <a class='products__inter' href='<?php echo get_permalink($id) ?>'>כניסה למכרז &rsaquo;&rsaquo;</a>
    </li>
    <?php $html .= ob_get_clean(); ?>
    <?php }

    $html .= "</ul>";

    wp_reset_postdata();
    
        return $html;
}


   

  