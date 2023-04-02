"use strict";

let userNavLink = document.querySelector('.fa-solid');
let dropMenu = document.querySelector('.dropdown-menu');

let hamburger = document.querySelector('.hamburger');
let mobileEventNav = document.querySelector('.mobile-nav');



userNavLink.addEventListener('click', show);
hamburger.addEventListener('click', reveal);

function show() {
    dropMenu.classList.toggle('hidden');
    dropMenu.style.maxHeight = '200px';
    dropMenu.style.width = '10rem';
    dropMenu.style.zIndex = '10';
    dropMenu.style.transition = '1s';

}

function reveal() {
    hamburger.classList.toggle('is-active');
    mobileEventNav.classList.toggle('hidden');
}