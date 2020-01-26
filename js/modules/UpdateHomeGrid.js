import $ from 'jquery';
import { formatNumber } from './FormatNumber';
import InitTimer from "./InitTimer";

export default class UpdateHomeGrid {

    constructor() {
        this.listsGrid = document.querySelectorAll('.jet-listing-grid__item');
        this.arrID = [];
    }

    queryRestApi() {
        const updateBid = this.updateBid;
        const params = this.collectId();
        const self = this;

        $.getJSON(shimi_obj.root_url + '/wp-json/shimi/v1/grid-home-update?id=' + params, (res) => {
            updateBid(res, self);
        });
    }

    collectId() {
        this.listsGrid.forEach(list => this.arrID.push(list.getAttribute('data-post-id')));
        return this.arrID;
    }

    updateBid(res, self) {
        res.forEach((bid, index) => {
            $(self.listsGrid).eq(index).find('.top-current-bid').text(formatNumber(bid));
        });
    }

    runTimer() {
        
        this.listsGrid.forEach(list => {
            
            $.getJSON(shimi_obj.root_url + '/wp-json/shimi/v1/timer?postid=' + list.getAttribute('data-post-id'), (res) => {
                var timer = new InitTimer();

                var time = {
                    h: list.querySelector('.clock-h'),
                    m: list.querySelector('.clock-m'),
                    s: list.querySelector('.clock-s')
                }

                time.h.textContent = res[0].hours;
                time.m.textContent = res[0].minutes;
                time.s.textContent = res[0].sec;

                setInterval(() => {
                    timer.timer(res, time, false);
                }, 1000);
            })    
        });

    }
}