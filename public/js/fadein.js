
document.addEventListener('DOMContentLoaded', function() {
    var wrappers = [
        '.profile-wrapper', '.receipt-wrapper', '.dashBody', '.customerFrame', '.receiptFrame', '.tickFrame', '.bodyFrame', '.mainBlock', '.usersList', '.table-wrapper', '.info-box', '.title-wrapper', '.wrapper-title', '.activities', '.table', '.table-responsive'
    ];
    for (var i = 0; i < wrappers.length; i++) {
        var el = document.querySelector(wrappers[i]);
        if (el) {
            el.classList.add('fadein-animate');
        }
    }
});

