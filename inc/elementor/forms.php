<?php

// Do the relevant back-end action based on the form sent
function product_dashboard_actions($record, $handler) {

    $form_name = $record->get_form_settings('form_name');
    $raw_fields = $record->get('fields');

    $post_id = $raw_fields['postid']['value'];

    switch($form_name) {

        case 'update_increment':
            $amount = $raw_fields['amount']['value'];
            update_auction_bid_increment($amount, $post_id);
            break;

        case 'update_time':
            $time = $raw_fields['time_addition']['value'];
            add_time_to_auction($time, $post_id);
            break;

        default:
            break;
    }

    // Also, output the form data
    $handler->data['output'] = json_encode($raw_fields);
}
add_action('elementor_pro/forms/new_record', 'product_dashboard_actions', 10, 2);

// Add an event listener for elementor form submissions
function product_dashboard_listener() {

    if(is_singular('product') && (current_user_can('manage_woocommerce') || current_user_can('administrator'))) {

        echo "<script>
            jQuery(document).ready(function($) {
                $(document).on('submit_success', function(e, resp) {

                    var form_name = $(e.target).attr('name'),
                        values = $.parseJSON(resp.data.output);

                    if(form_name == 'update_increment') {
                        $('#bid-inc, #click-to-bid').text((parseInt($('#top-bid').html().replace(',', '')) + parseInt(values.amount.value)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ','));
                    }

                    else if(form_name == 'update_time') {
                        window.location.reload();
                    }
                });
            });            
        </script>";    
    }
}
add_action('wp_footer', 'product_dashboard_listener');

// Bid dashboard action functions
function update_auction_bid_increment($amount, $post_id) {
    return update_post_meta($post_id, 'woo_ua_bid_increment', $amount);
}

function add_time_to_auction($time, $post_id) {
    $current_end_date = get_post_meta($post_id, 'woo_ua_auction_end_date', true);

    if($current_end_date && ! empty($current_end_date)) {

        $end_date = strtotime($current_end_date);
        $end_date_exploded = explode(':', $time);

        $hours = $end_date_exploded[0];
        $minutes = $end_date_exploded[1];
        $decimal = convert_time($hours, $minutes);

        $new_end_date = date("Y-m-d H:i:s", $end_date + ($decimal * 60));
        
        if($new_end_date) {
            update_post_meta($post_id, 'woo_ua_auction_end_date', $new_end_date);
        }
    }
}

function convert_time($hours, $minutes) {
    return $hours + round($minutes / 60, 2);
}