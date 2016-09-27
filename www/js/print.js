$(document).ready(function () {
    $("table.rowspanize").rowspanizer({
        td: 'td:nth-child(1)'
    });

    if (printDocument) {
        setTimeout(function () {
            window.print();
        }, 1000);
    }
});