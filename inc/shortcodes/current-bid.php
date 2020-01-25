<?php

    function current_bid_shortcode() {
        return '<span class="top-current-bid">0</span> ₪';
    }
    add_shortcode('current-bid', 'current_bid_shortcode');