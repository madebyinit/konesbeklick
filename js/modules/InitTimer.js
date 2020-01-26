
class InitTimer {
    constructor() {
        this.init = {
            s: true,
            m: true,
            h: true
        }
    }

    // methods
    timer(time, view, reload = true) {
        this.updateClock(time, 'sec', view.s, 's');
        if(this.init.s === 59) {
            this.updateClock(time, 'minutes', view.m, 'm');
            if(this.init.s === 59 && this.init.m === 0) {
                this.updateClock(time, 'hours', view.m, 'h');
            }
        }
        if(this.init.s === 0 && this.init.m === 0 && this.init.h === 0 && reload) {
            location.reload();
        }
    }

    updateClock(clock, cprop, viwe, vprop) {
        let time = clock;
            if(cprop == 'sec') {
                time[0][cprop] <= 0 ? time[0][cprop] = 59 : time[0][cprop]--;
            }
            else {
                time[0][cprop] <= 0 ? time[0][cprop] = 0 : time[0][cprop]--;
            }

            viwe.textContent = time[0][cprop] >= 0 ? time[0][cprop] : 0;
            this.init[vprop] = time[0][cprop];
    }
}

export default InitTimer;