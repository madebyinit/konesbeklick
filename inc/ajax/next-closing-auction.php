<?php

    function next_closing_auction_countdown($is_ajax = true) {
        
        $query_args = array('posts_per_page' => -1, 'no_found_rows' => 1, 'post_status' => 'publish', 'post_type' => 'product');
		$query_args['tax_query'] = array(array('taxonomy' => 'product_type' , 'field' => 'slug', 'terms' => 'auction')); 
		$query_args['auction_arhive'] = true;
		$query_args['meta_key'] = 'woo_ua_auction_end_date';
		$query_args['orderby']  = 'meta_value';
		$query_args['order']    = 'DESC';		

        $uwa_query = new WP_Query($query_args);
        $count_to = '';

        foreach($uwa_query->posts as $auction) {
            $oldTime = strtotime(get_post_meta($auction->ID ,'woo_ua_auction_end_date', true)); 

            if($oldTime > time()) {
                $count_to = $oldTime;
                break;
            }
        }

        if(! empty($count_to)) {
            
            if($is_ajax) {

                die(json_encode(array(
                    'hours' => date('H', $count_to),
                    'minutes' => date('i', $count_to),
                    'seconds' => date('s', $count_to)
                )));    

            } else {

                return array(
                    'hours' => date('H', $count_to),
                    'minutes' => date('i', $count_to),
                    'seconds' => date('s', $count_to)
                );
            }
        }

        if($is_ajax) {

            die(json_encode(array(
                'hours' => 0,
                'minutes' => 0,
                'seconds' => 0
            )));

        } else {
            return array(
                'hours' => 0,
                'minutes' => 0,
                'seconds' => 0
            );
        }
    }