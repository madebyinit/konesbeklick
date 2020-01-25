<?php

    function is_user_manager() {
        $user = esc_html($_POST['userid']);
        die((user_can($user, 'manage_woocommerce') || user_can($user, 'administrator')) && $user != 0 ? 'true' : 'false');
    }
