import { formatNumber } from './FormatNumber';
import $ from 'jquery';
export default class InitProduct {
    constructor(timer) {
        this.id = shimi_obj.post_id;
        this.user_id = shimi_obj.user_id;
        this.api;
        this.time = {
            h: document.getElementById('clock-h'),
            m: document.getElementById('clock-m'),
            s: document.getElementById('clock-s')
        }
        this.timer = timer;
        this.events(); 
    }

    // events
    events(initial = true) {
         $.getJSON(shimi_obj.root_url + '/wp-json/shimi/v1/timer?postid=' + this.id , (res) => {
            this.api = res;

            this.bid = parseFloat(this.api[2]);
            this.inc = parseFloat(this.api[4]);
            this.topBid();
            this.bidInc();

            if(initial) {

                $('body').on('click', '#popup-accept-bid', (e) => {
                    e.preventDefault();
                    this.clickToBid();
                });
    
                $('#auction-end-now').click((e) => {
                    e.preventDefault();
                    this.auctionEnd();
                });
    
                this.upDateClock(this.api[0]);
    
                setInterval(() => {
                    this.timer.timer(this.api, this.time);
                }, 1000);
    
                setInterval(() => {
    
                    var last_bid_id = $('.auction-bidding-history--offer').last().attr('data-bid-id');
    
                    $.getJSON(shimi_obj.root_url + '/wp-json/shimi/v1/bidding-history?postid=' + this.id + '&lastbid=' + last_bid_id + '&userid=' + this.user_id, (res) => {
    
                        if(res.newbids) {
                            $('.auction-bidding-history').find('.auction-bidding-history--offer.is-highest').removeClass('is-highest');
    
                            $(res.newbids).hide().appendTo('.auction-bidding-history').fadeIn(() => {
                                this.latestBidsScroll();
                            });

                            var sound = $('#new-bid-sound').attr('src');
                            new Audio(sound).play()
                        }
    
                        if(res.timer) {
                            this.upDateClock(res.timer);
                        }
                    });
                }, 5000);    
            }
        });
    }


    // methods
    topBid() {
        $('#top-bid').text(formatNumber(this.api[2]));
    }

    bidInc() {
        $('#bid-inc, #click-to-bid').text(formatNumber(this.bid + this.inc));
    }

    clickToBid() {
        const form = {
            action: 'admin_jump_price',
            price: this.bid + this.inc,
            id: this.id,
            userid: shimi_obj.user_id
        }

        $.post(shimi_obj.ajax_url, form, (res) => {
            $('#top-bid').text(formatNumber(parseInt(res[0])));
            this.events(false);
            $('body').trigger('click');
            this.upDateClock(this.api[4]);            
        });
    }

    auctionEnd() {
        const form = {
            action: 'admin_jump_time',
            closed: 'closed',
            id: this.id
        }

        $.post(shimi_obj.ajax_url, form, (res) => {
            window.location.reload();
        });
    }

    upDateClock(time) {
        this.time.h.textContent = time.hours < 0 ? 0 : time.hours;
        this.time.m.textContent = time.minutes < 0 ? 0 : time.minutes;
        this.time.s.textContent = time.sec < 0 ? 0 : time.sec;
    }

    restrictSingleProductDashboard() {

        var data = {
          action: 'is_user_manager',
          userid: shimi_obj.user_id
        };
      
        $.post(shimi_obj.ajax_url, data, (res) => {
          if(res == 'true') {
            $('.single-product-admin-dashboard').addClass('user-can-access');
          } else {
            $('.single-product-admin-dashboard').remove();
          }
        });
    }
    
    latestBidsScroll() {
        var element = document.querySelector('.auction-bidding-history--wrapper');
        element.scrollTop = element.scrollHeight;
    }
}

