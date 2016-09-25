$(document).ready(function () {
    $(function() {
        // clone table to display "before"
        var beforeTable = $('table.masses').clone().removeAttr('id').appendTo('#before')
        // code for grouping in "after" table
        var $rows = $('table.masses tbody tr');
        var items = [],
            itemtext = [],
            currGroupStartIdx = 0;
        $rows.each(function(i) {
            var $this = $(this);
            var itemCell = $(this).find('td:eq(0),td:eq(1)')
            var item = itemCell.html();
            itemCell.remove();
            if ($.inArray(item, itemtext) === -1) {
                itemtext.push(item);
                items.push([i, item]);
                groupRowSpan = 1;
                currGroupStartIdx = i;
                $this.data('rowspan', 1)
            } else {
                var rowspan = $rows.eq(currGroupStartIdx).data('rowspan') + 1;
                $rows.eq(currGroupStartIdx).data('rowspan', rowspan);
            }

        });



        $.each(items, function(i) {
            var $row = $rows.eq(this[0]);
            var rowspan = $row.data('rowspan');
            $row.prepend('<td rowspan="' + rowspan + '">' + this[1] + '</td>');
        });


    });

    $(".button-collapse").sideNav();

    $('.modal-trigger').leanModal();

    $('select').material_select();

    $('[data-link]')
    /*.each(function () {
     $(this).children()
     .wrapInner('<a class="js-link" href="' + $(this).attr('data-link') + '"></a>')
     .addClass('js-link');
     })*/
        .click(function () {
            location.href = $(this).attr('data-link');
        });

    // clean URL GET parameters
    if (window.location.search.substring(1).length) {
        if (window.history != undefined && window.history.pushState != undefined) {
            window.history.pushState({}, document.title, window.location.pathname);
        }
    }

    $('a.teal-text, a .teal-text').mouseover(function () {
        $(this).addClass('text-darken-4');
    }).mouseout(function () {
        $(this).removeClass('text-darken-4');
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
        labelYearSelect: 'Zvolit rok',
        onClose: function(){
            $(document.activeElement).blur()
        }
    });

    $('form').submit(function () {
        $('[name=date]').val($('[name=date_submit]').val());
    })

    $('.timepicker').pickatime({
        twelvehour: false,
        donetext: 'Uložit'
    });
});
