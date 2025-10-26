document.querySelector('.btn-save').addEventListener('click', function() {
    const popup = document.querySelector('.popup');
    popup.classList.add('show');
    setTimeout(() => popup.classList.remove('show'), 2000);
});

