<?php $__env->startSection('page-contents'); ?>
	<table id="table_div" class="display" cellspacing="0" width="100%"></table>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('inline-javascript'); ?>
    <?php echo $__env->make(
        'pragmarx/tracker::_datatables',
        array(
            'datatables_ajax_route' => route('tracker.stats.api.errors'),
            'datatables_columns' =>
            '
                { "data" : "error.code",     "title" : "'.trans('tracker::tracker.code').'", "orderable": true, "searchable": false },
                { "data" : "session.uuid",   "title" : "'.trans('tracker::tracker.session').'", "orderable": true, "searchable": false },
                { "data" : "error.message",  "title" : "'.trans('tracker::tracker.message').'", "orderable": true, "searchable": false },
                { "data" : "path.path",      "title" : "'.trans('tracker::tracker.path').'", "orderable": true, "searchable": false },
                { "data" : "updated_at",     "title" : "'.trans('tracker::tracker.when').'", "orderable": true, "searchable": false },
            '
        )
    , \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make($stats_layout, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ploi/insane.bet/vendor/pragmarx/tracker/src/views/errors.blade.php ENDPATH**/ ?>