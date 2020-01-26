import $ from 'jquery';
import { formatNumber } from './FormatNumber';
export const time = () => {

    $('form.jump__time').submit(function(e) {
        e.preventDefault();
        const field = $('#jumpTime'); 
        const time = field.val();
        const id = field.data('postid');

        var form = {
            action: 'admin_jump_time',
            time,
            id
        } 

        $.post(shimi_obj.ajax_url, form, function(res) {
            Countdown.values.hours = res[0];
            Countdown.values.minutes = res[1];
            Countdown.values.seconds = res[2];
            $('form.jump__time').append(`הקפצת את הזמן ב ${time} דקות`);
        });
    });

}

export const price = () => {

    // $('#jump-from-internet form').submit(function(e) {
    //     e.preventDefault();

    //     const field = $('#jump-from-internet #form-field-name').val();
    //     const currentBid = document.getElementById('top-bid').textContent.replace(',', '');
    //     const price = parseFloat(field) + parseFloat(currentBid);
    //     const id = shimi_obj.post_id;
    //     const userid = shimi_obj.user_id;
 
    //     var form = {
    //     action: 'admin_jump_price',
    //     price,
    //     id,
    //     userid
    // } 

    // $.post(shimi_obj.ajax_url, form, function(res) {
    //     document.getElementById('top-bid').textContent = formatNumber(res[0]);
    //     document.querySelectorAll('#bid-inc, #click-to-bid').forEach(item => item.textContent = formatNumber(res[1]));

    //     });

    // });

}

export const closedBid = () => {

    $('.close-auction__btn').click( (e) => {

        const field = $('#jumpTime'); 
        const id = field.data('postid');

          var data = {
            action: 'admin_jump_time',
            id,
            closed: 'closed'
        }

          Countdown.values.hours = 0;
          Countdown.values.minutes = 0;
          Countdown.values.seconds = 1;

          $.post(shimi_obj.ajax_url, data, function(res) {

            clearInterval(Countdown.countdown_interval);

            setTimeout(() => clearInterval(window.upTimePrice), 5000);

            $('.uwa_auction_product_ajax_change').html(
                `<p>המכרז הסתיים</p>`
              ).hide().slideDown(700);

            $(e.target).after(`<p id='locked'>${ res }</p>`);              
            setTimeout(() => $('#locked').remove(), 1000);

          });
          
        });

}