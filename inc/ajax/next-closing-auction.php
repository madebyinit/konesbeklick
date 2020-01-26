<?php

    function next_closing_auction_countdown($is_ajax = true) {
        
        $query_args = array('posts_per_page' => 1, 'post_status' => 'publish', 'post_type' => 'product');
		$query_args['meta_key'] = 'woo_ua_auction_end_date';
		$query_args['orderby']  = 'meta_value';
		$query_args['order']    = 'ASC';
		$query_args['meta_query'] = array(
		    array(
                'key' => 'woo_ua_auction_end_date',
                'value' => date('Y-m-d H:i:s'),
                'compare' => '>=',
                'type' => 'DATETIME'
            )
        );

        $uwa_query = new WP_Query($query_args);

        $end_time = strtotime(get_post_meta($uwa_query->posts[0]->ID ,'woo_ua_auction_end_date', true));
        $raw_time = $end_time - strtotime("now");
        $end_days = $raw_time / 60 / 60 / 24;
        $end_hours = ($end_days - intval($end_days)) * 24;
        $end_minutes = ($end_hours - intval($end_hours)) * 60;
        $end_sec = ($end_minutes - intval($end_minutes)) * 60;
        $end_hours = ($end_hours - 2) > 0 ? $end_hours - 2 : 0;

        if(! empty($end_time)) {
            
            if($is_ajax) {

                die(json_encode(array(
                    'hours'   => floor($end_days * 24),
                    'minutes' => floor($end_minutes),
                    'sec'     => floor($end_sec)
                )));    

            } else {

                return array(
                    'hours'   => floor($end_days * 24),
                    'minutes' => floor($end_minutes),
                    'sec'     => floor($end_sec)
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