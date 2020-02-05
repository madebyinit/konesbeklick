import $ from 'jquery';

export default class Validation {
    constructor(initial = true) {
        this.initial = initial;
        this.form = $('.form--one');
        this.fullname = $('.reg__field-section #name');
        this.email = $('.reg__field-section #email');
        this.tel = $('.reg__field-section #tel');
        this.pass = $('.reg__field-section #pass');
        this.pass2 = $('.reg__field-section #pass2');
        this.privacy_policy = $('.reg__field-section #privacy_policy');
        this.status = {
            success: false,
            alert: false
        }
        
        if(initial) {
            this.events();
        }
    }

    events() {
        this.fullname.on("input", this.validatFN.bind(this));
        this.email.on("input", this.validatEmail.bind(this));
        this.tel.on("input", this.validatTel.bind(this));
        this.pass.on("input", this.validatPass.bind(this));
        this.pass2.on("input", this.validatPass2.bind(this));
    }

    validatEmpty(key, val = false) {

        const elem = this[key],
              text = val !== false ? val : elem.val();
              
        const condition = () => {
            if(! text || text.length <= 0) {
                return true;
            } else {
                return false;
            }
        }

        return ! this.generalValidate(elem, text, condition, "שדה זה הינו שדה חובה");
    }

    validatFN() {
        const text = this.fullname.val();
        const condition = () => {
            if(text.split(" ").length < 2 || text.split(" ")[1].length < 1) {
                return true;
            } else {
                return false;
            }
        }
        return ! this.generalValidate(this.fullname, text, condition, "יש להכניס שם פרטי ושם משפחה");
    }

    validatEmail() {
        const text = this.email.val();

        const condition = () => {
            if(text.split("@").length < 2 || text.split("@")[1].length < 1) {
                return true;
            } else {
                return false;
            }
        }

        return ! this.generalValidate(this.email, text, condition, "שדה המייל צריך להכיל @ ולאחריו שם חברת המייל");
    }

    validatTel() {
        const text = this.tel.val();

        const condition = () => {

            if(text.length < 9) {
                return true
            } else {
                return false;
            }
        }
        return ! this.generalValidate(this.tel, text, condition, "יש לרשום לפחות 9 ספרות");
    }

    validatPass() {
        const text = this.pass.val();
      
        const condition = () => {
            if(!this.lettersAndNumbers(text) || this.minNumInPass(text)) {
                return true;
            } else {
                return false
            }
        }
        return ! this.generalValidate(this.pass, text, condition, "יש לכתוב לפחות 6 תווים המשולבים מאותיות ומספרים");
    }

    minNumInPass(text) {
        return text.length < 6;
    }

    lettersAndNumbers(string) {
        const num = /[a-zA-Z]+/g;
        const lett = /[0-9]+/g
        return num.test(string) && lett.test(string);
    }

    validatPass2() {
        const text = this.pass2.val();

        const condition = () => {
            if(text !== this.pass.val()) {
                return true;
            } else {
                return false
            }
        }
        return ! this.generalValidate(this.pass2, text, condition, "דרושה התאמה מלאה בין הסיסמאות");
    }

    generalValidate(element ,input, condition, validatText) {

        if(condition()) {
            this.message(element, validatText, "alert-message", "alert");
        } else {
            this.message(element, "מצויין!", "success-message", "success");
        }

        if(input.length == 0 && this.initial) {
            this.status = {
                success: false,
                alert: false
            }
            element.siblings('p').remove();
        }

        return condition();
    }

    message(element, mess, className, status) {

        if(!this.status[status] || ! this.initial) {
            element.siblings('p').remove();
            $(element).parent().append(`<p class="${className}">${mess}</p>`);
        }

        this.status[status] = [true];
        
    }
    
}