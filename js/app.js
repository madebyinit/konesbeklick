// formatNumber
import { formatNumber } from './modules/FormatNumber';

const topbids = document.querySelectorAll('.numToFormat');
topbids.forEach(topbid => topbid.textContent = formatNumber(topbid.textContent));

// Gridjs
import { gridShadow } from './modules/Grid';
gridShadow();

// update the bid on home page grid
import UpdateHomeGrid from './modules/UpdateHomeGrid';
const updateHomeGrid = new UpdateHomeGrid();

if(document.querySelector('.jet-listing-grid__item')) {
  updateHomeGrid.queryRestApi();

  setInterval(() => {
    updateHomeGrid.queryRestApi();
  }, 15000);
}

import InitTimer from "./modules/InitTimer";
import InitProduct from "./modules/InitProduct";
const timer = new InitTimer();
const init = new InitProduct(timer);

// The countDown clock
import { Countdown } from './modules/Codpen';
  Countdown.init(); 
  window.Countdown = Countdown;

// the setInterval query for update price time etc..
import { update } from './modules/Update';
// update();

// the jump function
import * as jump from './modules/Jump';
jump.time();
jump.price();
jump.closedBid();  

// Registration function
import { register } from './modules/Register';
register();

// miscellaneous parts
import * as misc from './modules/Misc';
misc.remove();
misc.inputOffer();
misc.changeText();

if(document.querySelector('.single-product-admin-dashboard')) {
  misc.restrictSingleProductDashboard();
}
// misc.conf();

// form registration valdations
import Validation from './modules/Validation';
const validForm = new Validation();
