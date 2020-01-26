<?php

    function grid_bid_time_shortcode() {
        return '<div class="grid-bid-time"><span class="clock-h"></span> שעות <small>:</small> <span class="clock-m"></span> דקות <small>:</small> <span class="clock-s"></span> שניות</div>';
    }
    add_shortcode('grid-bid-time', 'grid_bid_time_shortcode');