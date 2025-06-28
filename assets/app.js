import * as Turbo from "@hotwired/turbo"
import {disableBodyScroll, enableBodyScroll, clearAllBodyScrollLocks } from 'body-scroll-lock'
import '/css/app.scss'

let isFirstRenderer = true;

document.addEventListener('turbo:load', () => {
    clearAllBodyScrollLocks() // Enabled all scrolling
    isFirstRenderer = false
})
// Toggle Header
const btnHamburger = document.querySelector('#js-hamburger');
const headerNav = document.querySelector('.header__nav');
if (btnHamburger) {
    let isOpen = false;
    btnHamburger.addEventListener('click', () => {
        document.querySelector('#main-header').classList.toggle('open-menu');
        isOpen ? enableBodyScroll(headerNav) : disableBodyScroll(headerNav);
        isOpen = !isOpen;
    })
}

// start turbo
Turbo.start()
