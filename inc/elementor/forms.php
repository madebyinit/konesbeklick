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
                        $('#bid-inc').text((parseInt($('#top-bid').html().replace(',', '')) + parseInt(values.amount.value)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ','));
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

