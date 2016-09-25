$(document).ready(function () {
    $(function () {
        // clone table to display "before"
        var beforeTable = $('table.masses').clone().removeAttr('id').appendTo('#before')
        // code for grouping in "after" table
        var $rows = $('table.masses tbody tr');
        var items = [],
            itemtext = [],
            currGroupStartIdx = 0;
        $rows.each(function (i) {
            var $this = $(this);
            var itemCell = $(this).find('td:eq(0)')
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


        $.each(items, function (i) {
            var $row = $rows.eq(this[0]);
            var rowspan = $row.data('rowspan');
            $row.prepend('<td rowspan="' + rowspan + '">' + this[1] + '</td>');
        });


    });

    if (printDocument) {
        setTimeout(function () {
            window.print();
        }, 1000);
    }
});