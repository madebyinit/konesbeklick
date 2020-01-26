<?php

    function homepage_timer_shortcode() {
        $times = next_closing_auction_countdown(false);

        return '<p style="text-align: center; direction: ltr;"><span class="hours">' . $times['hours'] . '</span> <strong><span style="color: #3fd77f;">:</span></strong> <span class="minutes">' . $times['minutes'] . '</span><strong> <span style="color: #3fd77f;">:</span></strong> <span class="seconds">' . $times['seconds'] . '</span></p>';
    }
    add_shortcode('homepage-timer', 'homepage_timer_shortcode');