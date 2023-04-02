const buttons = [...document.querySelector('.event-menu').children];
buttons.forEach(button => button.addEventListener('click', onClick));

function onClick(ev) {
    const menu = [...document.querySelectorAll('.event-menu__button')];
    let target = ev.target;

    for(let i=0; i < menu.length; i++) {
        if(ev.target.tagName == 'A') {
            target = ev.target.parentElement;
        }
        if(ev.target.tagName == 'P' || ev.target.tagName == 'I') {
            target = ev.target.parentElement.parentElement;
        }

        if(target.dataset.id == i) {
            console.log(target);
            console.log(Number(target.dataset.id));
            console.log(menu[i].dataset.id);
            console.log(i);
            target.classList.add('event-menu__active');
        }
        target.classList.remove('event-menu__active');
    }
}