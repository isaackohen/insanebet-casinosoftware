<?php $__env->startSection('page-contents'); ?>
	<table id="table_div" class="display" cellspacing="0" width="100%"></table>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('inline-javascript'); ?>
    <?php echo $__env->make(
        'pragmarx/tracker::_datatables',
        array(
            'datatables_ajax_route' => route('tracker.stats.api.events'),
            'datatables_columns' =>
            '
                { "data" : "name",  "title" : "'.trans('tracker::tracker.name').'", "orderable": true, "searchable": false },
                { "data" : "total", "title" : "'.trans('tracker::tracker.no_occurrences_in_period').'", "orderable": true, "searchable": false },
            '
        )
    , \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make($stats_layout, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ploi/insane.bet/vendor/pragmarx/tracker/src/views/events.blade.php ENDPATH**/ ?>