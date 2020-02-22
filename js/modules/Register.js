import $ from 'jquery';
import TemplateRegister from './TemplateRegister';
const template = new TemplateRegister();

import Validation from './Validation';

export const register = () => {

    $(".toggle-password").click(function() {
      console.log('adasd');
      $(this).toggleClass("fa-eye fa-eye-slash");

      var input = $($(this).attr("toggle"));

      if (input.attr("type") == "password") {
        input.attr("type", "text");
      } else {
        input.attr("type", "password");
      }
    });

    $(document).on('click', '.reg__step', (e) => {
    
        const target = $(e.target.closest('div'));
    
        if(target.next().attr('data-locked') === 'yes') {
    
          target.append("<p id='locked'>להמשך מלא את הטופס למעלה</p>");
          setTimeout(() => $('#locked').remove(), 1000);
    
        } else if(target.next().attr('data-locked') !== 'readonly') {
    
          target.next().slideToggle(300);
    
        }
    
      });
    
      $(document).on('submit', '#form--one', function(e) {
    
        e.preventDefault();
    
        var data = {
          action: 'generate_cardcom_iframe',
          fullname: $('#name').val(),
          email: $('#email').val(),
          tel: $('#tel').val(),
          pass: $('#pass').val(),
          pass2: $('#pass2').val(),
          referrer: document.referrer.length <= 0 ? $('#home_url').val() : document.referrer,
          privacy_policy: $('#privacy_policy').is(':checked') ? 'true' : ''
        }

        const validation = new Validation(false);
        var is_valid = true;

        $.each(data, function(key) {

          if(key !== 'action' && key !== 'referrer' && key !== 'privacy_policy') {
            if(! validation.validatEmpty(key)) {
              is_valid = false;
            }
          } else if(key === 'privacy_policy') {

            if(! validation.validatEmpty(key, data.privacy_policy)) {
              is_valid = false;
            }
          }
        });

        if(is_valid) {

          is_valid = ! validation.validatEmail() ? false : is_valid;
          is_valid = ! validation.validatTel() ? false : is_valid;
          is_valid = ! validation.validatPass() ? false : is_valid;
          is_valid = ! validation.validatPass2() ? false : is_valid;

          if(is_valid) {

            var now = new Date();
            var five_minutes = new Date(now.getTime() + 5 * 60000).toISOString();

            document.cookie = 'registration-email=' + data.email + '; expires=' + five_minutes + '; path=/';

            var referrer_url = new URL(data.referrer);
            referrer_url.searchParams.append('login', 'true');

            window.localStorage.setItem('registration-referrer', referrer_url);
            
            $.ajax({
              url: shimi_obj.ajax_url,
              type: 'POST',
              data: data,
              success(result,status,xhr) {

                if(result != false && result.redirect_to) {
                  $(e.target).parents('.reg').next().find('.reg__form').html('<iframe src="' + result.redirect_to + '" border="0" style="width: 100%; height: 420px;"></iframe>');
                  $(e.target).parents('.reg').find('.reg__form').slideUp().attr('data-locked', 'readonly');
                  $(e.target).parents('.reg').next().find('.reg__form').slideDown().attr('data-locked', 'no');  
                }
              },
              error(xhr,status,error) {
                
              }
            });  
          }
        }
      });
    
      // if user not register is not can offers bid
    
      if(!$('.latest-offers').data('user')) { 
        
        $('body').append(template.createHTML());

        const btn = $('.uwa_auction_form.cart').find('.bid_button');
        btn.attr('disabled', 'disabled');
        $(document).on('mouseenter', '.uwa_auction_form.cart', function() {
            $('.register-modal').addClass('active');
            $('body').css('overflow-y', 'hidden');
          });

        $('.register-modal__exit').on('click', function() {
          $('.register-modal').removeClass('active');
          $('body').css('overflow-y', 'scroll');
        });
        
      }

}