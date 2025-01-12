document.querySelector('#showAll').addEventListener('click', function() {
    document.querySelectorAll('#images > .col-md-3 > .card').forEach(el => el.style.display = 'block');
    this.style.display = 'none';
    document.querySelector('#hideAll').style.display = 'block';
}, false);

document.querySelector('#hideAll').addEventListener('click', function() {
    document.querySelectorAll('#images > .col > .card').forEach(el => el.style.display = 'none');
    this.style.display = 'none';
    document.querySelector('#showAll').style.display = 'block';
}, false);
