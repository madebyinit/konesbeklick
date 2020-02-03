<?php 

require_once get_theme_file_path('/inc/redirect.php');

add_action('template_redirect', 'endbid_redirect');

function shimi_enqueue_styles() {
    global $post;
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'parent-style-min', get_template_directory_uri() . '/style.min.css' );
    wp_enqueue_style( 'parent-theme', get_template_directory_uri() . '/theme.css' );
    wp_enqueue_style( 'parent-theme-min', get_template_directory_uri() . '/theme.min.css' );
    wp_enqueue_style( 'parent-editor', get_template_directory_uri() . '/editor-style.css' );
    wp_enqueue_style( 'parent-editor-min', get_template_directory_uri() . '/editor-style.min.css' );
    
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'parent-style', 'parent-style-min', 'parent-theme', 'parent-theme-min', 'parent-editor', 'parent-editor-min'), wp_get_theme()->get('Version'));

    wp_enqueue_script('gsap', get_stylesheet_directory_uri() . '/js/gsap.js', array(), wp_get_theme()->get('Version'), true);
    wp_enqueue_script('bundled', get_stylesheet_directory_uri() . '/js/bundled.js', array('gsap'), time(), true);

    wp_localize_script( 'bundled', 'shimi_obj', array(
        'ajax_url' => admin_url('/admin-ajax.php'),
        'nonce'    => wp_create_nonce('wp_rest'),
        'root_url' => get_site_url(),
        'user_id'  => get_current_user_id(),
        'post_id'  => $post->ID
        )); 
}

add_action( 'wp_enqueue_scripts', 'shimi_enqueue_styles');

function shimi_add_woocommerce_support() {
    add_theme_support( 'woocommerce' );
}

add_action( 'after_setup_theme', 'shimi_add_woocommerce_support' );


require_once(get_theme_file_path( '/inc/top-bid.php' ));
require_once(get_theme_file_path( '/inc/admin-dashbord.php' ));
require_once(get_theme_file_path( '/inc/countdown-title.php' ));
require_once(get_theme_file_path( '/inc/shortcodes/shortcodes.php' ));
require_once(get_theme_file_path( '/inc/flex-acf.php' ));
require_once(get_theme_file_path( '/inc/latest-offers.php' ));

add_action('ultimate_woocommerce_auction_before_bid_form', 'yoyo_topbid', 10); 
add_action('woocommerce_auction_add_to_cart', 'adminDash', 10); 
add_action('woocommerce_single_product_summary', 'yoyo_countdownTitle', 10);
add_action('woocommerce_before_single_product_summary', 'flexACF', 30);
add_action('woocommerce_after_single_product_summary', 'latestOffers', 10);


add_shortcode('carsloop', 'shimi_loop');

// AJAX SECTION
require_once(get_theme_file_path( '/inc/ajax/admin-jump-time.php' ));
add_action('wp_ajax_admin_jump_time', 'admin_jump_time');

require_once(get_theme_file_path( '/inc/ajax/admin-jump-price.php' ));
add_action('wp_ajax_admin_jump_price', 'admin_jump_price');

require_once(get_theme_file_path( '/inc/ajax/reg.php' ));
add_action('wp_ajax_nopriv_shimi_reg', 'shimi_reg');
add_action('wp_ajax_shimi_reg', 'shimi_reg');

add_action('wp_ajax_nopriv_generate_cardcom_iframe', 'generate_cardcom_iframe');
add_action('wp_ajax_generate_cardcom_iframe', 'generate_cardcom_iframe');

require_once(get_theme_file_path( '/inc/ajax/rest-api.php' ));
add_action('rest_api_init', 'shimiTimer');

require_once(get_theme_file_path( '/inc/ajax/is-user-manager.php' ));
add_action('wp_ajax_nopriv_is_user_manager', 'is_user_manager');
add_action('wp_ajax_is_user_manager', 'is_user_manager');

require_once(get_theme_file_path( '/inc/ajax/next-closing-auction.php' ));
add_action('wp_ajax_nopriv_next_closing_auction_countdown', 'next_closing_auction_countdown');
add_action('wp_ajax_next_closing_auction_countdown', 'next_closing_auction_countdown');

// Elementor hooks (used mainly in the dashboard )
require_once __DIR__ . '/inc/elementor/elementor.php';

// Remove Actions

remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);

// Add the Kones Setup settings page
if( function_exists('acf_add_options_page') ) {

    // Register options page.
    $option_page = acf_add_options_page(array(
        'page_title'    => __('Kones Setup'),
        'menu_title'    => __('Kones Setup'),
        'menu_slug'     => 'kones-bclick-setup',
        'capability'    => 'edit_posts',
        'redirect'      => false,
        'position'      => 2
    ));
}

function redirect_to_thankyou_page() {

    if(isset($_GET['redirect']) && $_GET['redirect'] == 'true' && ! isset($_GET['init'])) {
        echo '<script>parent.location.href = window.location.href + "&init=true";</script>';
    }
}
add_action('wp_footer', 'redirect_to_thankyou_page');

function create_new_user() {

    if(isset($_GET['lowprofilecode']) && isset($_GET['terminalnumber'])) {

        $vars = array( 
            'TerminalNumber' => get_field('cardcom_terminal_number', 'option'),
            'UserName' => get_field('cardcom_username', 'option'),
            'LowProfileCode' => $_GET['lowprofilecode']
        );
            
        $urlencoded = http_build_query($vars);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://secure.cardcom.solutions/Interface/BillGoldGetLowProfileIndicator.aspx');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $urlencoded);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
    
        $response = curl_exec($curl);
        curl_close($curl);
    
        $output = array();
        parse_str($response, $output);

        if ($output['ResponseCode'] == '0' && $output['OperationResponse'] == '0') {

            $data = json_decode($output['ReturnValue'], JSON_UNESCAPED_SLASHES);

            $name = $data['fullname'];
            $email = $data['email'];
            $tel = $data['tel'];
            $pass = $data['pass'];    

            $user_id = wp_insert_user(array(
                'user_login' => $email,
                'user_pass'  => $pass,
                'user_email' => $email,
                'first_name' => explode(' ', $name)[0],
                'last_name'  => explode(' ', $name)[1]
            ));
        
            if(! is_wp_error($user_id) ) {

                $creds = [];
                $creds['user_login'] = $email;
                $creds['user_password'] = $pass;
                $creds['remember'] = true;
                $user = wp_signon( $creds, false );
        
                update_user_meta($user_id, 'billing_phone', $tel);

                // Update the user's credit card meta info
                update_field('cc_token', $output['Token'], 'user_' . $user_id);
                update_field('cc_token_exp', $output['TokenExDate'], 'user_' . $user_id);
                update_field('cc_id_number', $output['CardOwnerID'], 'user_' . $user_id);
                update_field('cc_exp_date_month', $output['CardValidityYear'], 'user_' . $user_id);
                update_field('cc_exp_date_year', $output['CardValidityMonth'], 'user_' . $user_id);
                update_field('cc_token_approval_no', $output['TokenApprovalNumber'], 'user_' . $user_id);
            }
        }
    }    
}
add_action('template_redirect', 'create_new_user');

?>