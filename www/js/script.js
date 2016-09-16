$(document).ready(function () {
    $(".button-collapse").sideNav();

    $('[data-link]')
        .each(function () {
            $(this).children()
                .wrapInner('<a class="js-link" href="' + $(this).attr('data-link') + '"></a>')
                .addClass('js-link');
        })
        .click(function () {
            location.href = $(this).attr('data-link');
        });
});
