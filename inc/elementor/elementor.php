<?php
    require_once __DIR__ . '/forms.php';

    // Misc functions
    function single_product_bidding() {

        echo "<script>
            jQuery(function($) {
                $('#biding').on('click', (e) => {
                    e.preventDefault();
    
                    console.log(shimi_obj.user_id);
            
                    if(shimi_obj.user_id == 0) {
                        $('#trigger-popup-reg').find('a').trigger('click');
                    } else {
                        console.log($('#trigger-popup-reg').find('a'));
                        $('#trigger-popup-bid').find('a').trigger('click');
                    }
                });    
            });
        </script>";
    }
    add_action('wp_footer', 'single_product_bidding');