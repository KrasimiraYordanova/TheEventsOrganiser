'use strict';

let modal = document.querySelector('.modal');
let overlay = document.querySelector('.overlay');

let iconClose = document.querySelector('.close');

modal.addEventListener('click', showForm);
iconClose.addEventListener('click', closeForm);


function showForm() {
   overlay.classList.remove('hidden');
}

function closeForm() {
    overlay.classList.add('hidden');
}