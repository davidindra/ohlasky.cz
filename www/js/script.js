$(document).ready(function () {
    $(".button-collapse").sideNav();

    $('.modal-trigger').leanModal();

    $('[data-link]')
        .each(function () {
            $(this).children()
                .wrapInner('<a class="js-link" href="' + $(this).attr('data-link') + '"></a>')
                .addClass('js-link');
        })
        .click(function () {
            location.href = $(this).attr('data-link');
        });

    $('.datepicker').pickadate({
        min: new Date().getFullYear() + "-" + new Date().getMonth() + "-" + new Date().getDay,
        format: 'dddd d. mmmm',
        formatSubmit: 'yyyy/mm/dd',
        firstDay: 1,
        selectYears: 2,

        selectMonths: true,
        // Strings and translations
        monthsFull: ["leden", "únor", "březen", "duben", "květen", "červen",
            "červenec", "srpen", "září", "říjen", "listopad", "prosinec"],
        monthsShort: ['led', 'úno', 'bře', 'dub', 'kvě', 'čer', 'čvc', 'srp', 'zář', 'říj', 'lis', 'pro'],
        weekdaysFull: ['neděle', 'pondělí', 'úterý', 'středa', 'čtvrtek', 'pátek', 'sobota'],
        weekdaysShort: ['ne', 'po', 'út', 'st', 'čt', 'pá', 'so'],
        showMonthsShort: false,
        //showWeekdaysFull: true,
        //showWeekdaysShort: true,
        today: 'Dnes',
        clear: '',
        close: 'Uložit',
        labelMonthNext: 'Další měsíc',
        labelMonthPrev: 'Předchozí měsíc',
        labelMonthSelect: 'Zvolit měsíc',
        labelYearSelect: 'Zvolit rok'
    });

    $('.timepicker').pickatime({
        twelvehour: false,
        donetext: 'Uložit'
    });
});
