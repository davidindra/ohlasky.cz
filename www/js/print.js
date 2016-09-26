$(document).ready(function () {
    $("table.rowspanize").rowspanizer({
        vertical_align: 'middle'
    });

    if (printDocument) {
        setTimeout(function () {
            window.print();
        }, 1000);
    }
});