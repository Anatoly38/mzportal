// needed for Table Column ordering
function tableOrdering( order, dir, task ) {
    var form = document.adminForm;
    form.filter_order.value = order;
    form.filter_order_Dir.value = dir;
    submitform( task );
}

function saveorder( n, task ) {
    checkAll_button( n, task );
}

//needed by saveorder function
function checkAll_button( n, task ) {
    if (!task ) {
        task = 'saveorder';
    }
    for ( var j = 0; j <= n; j++ ) {
        box = eval( "document.adminForm.cb" + j );
        if ( box ) {
            if ( box.checked == false ) {
                box.checked = true;
            }
        } else {
            alert("You cannot change the order of items, as an item in the list is `Checked Out`");
            return;
        }
    }
    submitform(task);
}