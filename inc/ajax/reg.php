<?php 

function generate_cardcom_iframe() {

    $fields = array();
    $referrer = ! empty($_POST['referrer']) ? esc_html($_POST['referrer']) : get_home_url();

    foreach($_POST as $posted_key => $posted) {
        if($posted_key !== 'action' && $posted_key !== 'referrer') {
            $fields[esc_html($posted_key)] = esc_html($posted);
        }
    }

    $fields_encoded = json_encode($fields);

    $cardcom_data = array(
        'codepage' => 65001,
        'Operation' => 3,
        'TerminalNumber' => get_field('cardcom_terminal_number', 'option'),
        'UserName' => get_field('cardcom_username', 'option'),
        'SumToBill' => get_field('cardcom_test_sum_to_bill', 'option'),
        'CoinID' => 1,
        'Language' => 'he',
        'ProductName' => get_field('cardcom_test_product_name', 'option'),
        'APILevel' => 10,
        'SuccessRedirectUrl' => $referrer . '?redirect=true',
        'ErrorRedirectUrl' => get_home_url(),
        'IndicatorUrl' => get_home_url() . '?action=new_user',
        'CardOwnerName' => $fields['fullname'],
        'ReturnValue' => $fields_encoded,
        'DefaultNumOfPayments' => 1,
        'MaxNumOfPayments' => 1,
        'CreditType' => 1,
        'CreateTokenJValidateType' => 2
    );

    $urlencoded = http_build_query($cardcom_data);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://secure.cardcom.solutions/Interface/LowProfile.aspx');
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_FAILONERROR, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $urlencoded);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_FAILONERROR, true);

    $response = curl_exec($curl);
    curl_close($curl);

    $resp_decoded = urldecode($response);
    $final_resp = array();

    if($resp_decoded && ! empty($resp_decoded)) {
        $resp_exploded = explode('&', $resp_decoded);

        if($resp_exploded && is_array($resp_exploded)) {

            foreach($resp_exploded as $resp) {
                $final_resp[explode('=', $resp)[0]] = explode('=', $resp)[1];
            }
        }
    }

    if($final_resp['url'] && ! empty($final_resp['url'])) {
        $redirect_to = $final_resp['url'] . '=' . $final_resp['LowProfileCode'];

        wp_send_json(array(
            'redirect_to' => $redirect_to
        ), 200);
    }

    wp_send_json(array(
    ), 500);
}

function shimi_reg() {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $tel = $_POST['tel'];
    $pass = $_POST['pass'];

    $user_id = wp_insert_user( array(
        'user_login' => $email,
        'user_pass'  => $pass,
        'user_email' => $pass,
        'first_name' => $name,
        'last_name'  => $name
    ) );

    if( is_wp_error($user_id) ) {
        wp_send_json(array(
            'error' => $return->get_error_message()
        ), 200);
    } else {
        $creds = [];
        $creds['user_login'] = $email;
        $creds['user_password'] = $pass;
        $creds['remember'] = true;
        $user = wp_signon( $creds, false );

        update_user_meta($user_id, 'billing_phone', $tel);

        wp_send_json(array(
            'user-id' => $user_id
        ), 200);
     }
}

?>