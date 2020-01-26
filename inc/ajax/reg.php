<?php 

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