<?php /** @var  Illuminate\Pagination\LengthAwarePaginator  $rows */ ?>

<?php $__env->startSection('content'); ?>
    <h1 class="page-header"><?php echo e(__('Logs')); ?></h1>

    <?php echo e($rows->render()); ?>


    <div class="table-responsive">
        <table class="table table-condensed table-hover table-stats">
            <thead>
                <tr>
                    <?php $__currentLoopData = $headers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <th class="<?php echo e($key == 'date' ? 'text-left' : 'text-center'); ?>">
                        <?php if($key == 'date'): ?>
                            <span class="label label-info"><?php echo e($header); ?></span>
                        <?php else: ?>
                            <span class="level level-<?php echo e($key); ?>">
                                <?php echo e(log_styler()->icon($key)); ?> <?php echo e($header); ?>

                            </span>
                        <?php endif; ?>
                    </th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <th class="text-right"><?php echo e(__('Actions')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <td class="<?php echo e($key == 'date' ? 'text-left' : 'text-center'); ?>">
                                <?php if($key == 'date'): ?>
                                    <span class="label label-primary"><?php echo e($value); ?></span>
                                <?php elseif($value == 0): ?>
                                    <span class="level level-empty"><?php echo e($value); ?></span>
                                <?php else: ?>
                                    <a href="<?php echo e(route('log-viewer::logs.filter', [$date, $key])); ?>">
                                        <span class="level level-<?php echo e($key); ?>"><?php echo e($value); ?></span>
                                    </a>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <td class="text-right">
                            <a href="<?php echo e(route('log-viewer::logs.show', [$date])); ?>" class="btn btn-xs btn-info">
                                <i class="fa fa-search"></i>
                            </a>
                            <a href="<?php echo e(route('log-viewer::logs.download', [$date])); ?>" class="btn btn-xs btn-success">
                                <i class="fa fa-download"></i>
                            </a>
                            <a href="#delete-log-modal" class="btn btn-xs btn-danger" data-log-date="<?php echo e($date); ?>">
                                <i class="fa fa-trash-o"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="11" class="text-center">
                            <span class="label label-default"><?php echo e(trans('log-viewer::general.empty-logs')); ?></span>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php echo e($rows->render()); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('modals'); ?>
    
    <div id="delete-log-modal" class="modal fade">
        <div class="modal-dialog">
            <form id="delete-log-form" action="<?php echo e(route('log-viewer::logs.delete')); ?>" method="POST">
                <?php echo method_field('DELETE'); ?>
                <?php echo csrf_field(); ?>
                <input type="hidden" name="date" value="">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title"><?php echo e(__('Delete log file')); ?></h4>
                    </div>
                    <div class="modal-body">
                        <p></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-default pull-left" data-dismiss="modal"><?php echo e(__('Cancel')); ?></button>
                        <button type="submit" class="btn btn-sm btn-danger" data-loading-text="<?php echo e(__('Loading')); ?>&hellip;"><?php echo e(__('Delete')); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        $(function () {
            var deleteLogModal = $('div#delete-log-modal'),
                deleteLogForm  = $('form#delete-log-form'),
                submitBtn      = deleteLogForm.find('button[type=submit]');

            $("a[href=#delete-log-modal]").on('click', function(event) {
                event.preventDefault();
                var date    = $(this).data('log-date'),
                    message = "<?php echo e(__('Are you sure you want to DELETE this log file: :date ?')); ?>";

                deleteLogForm.find('input[name=date]').val(date);
                deleteLogModal.find('.modal-body p').html(message.replace(':date', date));

                deleteLogModal.modal('show');
            });

            deleteLogForm.on('submit', function(event) {
                event.preventDefault();
                submitBtn.button('loading');

                $.ajax({
                    url:      $(this).attr('action'),
                    type:     $(this).attr('method'),
                    dataType: 'json',
                    data:     $(this).serialize(),
                    success: function(data) {
                        submitBtn.button('reset');
                        if (data.result === 'success') {
                            deleteLogModal.modal('hide');
                            location.reload();
                        }
                        else {
                            alert('AJAX ERROR ! Check the console !');
                            console.error(data);
                        }
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        alert('AJAX ERROR ! Check the console !');
                        console.error(errorThrown);
                        submitBtn.button('reset');
                    }
                });

                return false;
            });

            deleteLogModal.on('hidden.bs.modal', function() {
                deleteLogForm.find('input[name=date]').val('');
                deleteLogModal.find('.modal-body p').html('');
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('log-viewer::bootstrap-3._master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ploi/insane.bet/resources/views/vendor/log-viewer/bootstrap-3/logs.blade.php ENDPATH**/ ?>