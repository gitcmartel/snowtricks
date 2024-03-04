import ButtonScroll from './classes/ButtonScroll.js';
import TricksScroll from './classes/TricksScroll.js';
import TricksDelete from './classes/TricksDelete.js';
import ModalMessage from './classes/ModalMessage.js';

let buttonScroll = new ButtonScroll();
let tricksDelete = new TricksDelete(new ModalMessage);
let tricksScroll = new TricksScroll(tricksDelete);
