'use strict';

let ulElements = document.querySelector('.nav__links');
let lists = [...document.querySelector('.nav__links').children];

ulElements.addEventListener('mouseover', btnActive);
ulElements.addEventListener('mouseout', btnInactive);

function btnActive(e) {
    let id = e.target.dataset.anchor;
    if (e.target.tagName === 'A' && e.target.id === id) {
        for (let i = 0; i < lists.length; i++) {
            lists[i].classList.add('inactives');
        }
        lists[id].classList.remove('inactives');
    }
}

function btnInactive(e) {
    let id = e.target.dataset.anchor;
    if (e.target.tagName === 'A' && e.target.id === id) {
        for (let i = 0; i < lists.length; i++) {
            lists[i].classList.remove('inactives');
        }
    }
}