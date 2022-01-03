$(document).ready(function() {
    $('#table_div').dataTable( {
        "processing": true,
        "serverSide": true,
        "bFilter": false,
        "ajax": "<?php echo e($datatables_ajax_route); ?>",
        "columnDefs": [ {
            "targets": "_all",
            "defaultContent": ""
        } ],
        "columns": [
            <?php echo $datatables_columns; ?>
        ]
    } );
} );
<?php /**PATH /home/ploi/insane.bet/vendor/pragmarx/tracker/src/views/_datatables.blade.php ENDPATH**/ ?>