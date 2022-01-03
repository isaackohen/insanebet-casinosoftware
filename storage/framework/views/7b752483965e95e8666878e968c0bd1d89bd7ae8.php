<?php /** @var  Illuminate\Pagination\LengthAwarePaginator  $rows */ ?>

<?php $__env->startSection('content'); ?>
    <div class="page-header mb-4">
        <h1><?php echo app('translator')->get('Logs'); ?></h1>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-hover">
            <thead>
                <tr>
                    <?php $__currentLoopData = $headers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <th scope="col" class="<?php echo e($key == 'date' ? 'text-left' : 'text-center'); ?>">
                        <?php if($key == 'date'): ?>
                            <span class="badge badge-info"><?php echo e($header); ?></span>
                        <?php else: ?>
                            <span class="badge badge-level-<?php echo e($key); ?>">
                                <?php echo e(log_styler()->icon($key)); ?> <?php echo e($header); ?>

                            </span>
                        <?php endif; ?>
                    </th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <th scope="col" class="text-right"><?php echo app('translator')->get('Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <td class="<?php echo e($key == 'date' ? 'text-left' : 'text-center'); ?>">
                                <?php if($key == 'date'): ?>
                                    <span class="badge badge-primary"><?php echo e($value); ?></span>
                                <?php elseif($value == 0): ?>
                                    <span class="badge empty"><?php echo e($value); ?></span>
                                <?php else: ?>
                                    <a href="<?php echo e(route('log-viewer::logs.filter', [$date, $key])); ?>">
                                        <span class="badge badge-level-<?php echo e($key); ?>"><?php echo e($value); ?></span>
                                    </a>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <td class="text-right">
                            <a href="<?php echo e(route('log-viewer::logs.show', [$date])); ?>" class="btn btn-sm btn-info">
                                <i class="fa fa-search"></i>
                            </a>
                            <a href="<?php echo e(route('log-viewer::logs.download', [$date])); ?>" class="btn btn-sm btn-success">
                                <i class="fa fa-download"></i>
                            </a>
                            <a href="#delete-log-modal" class="btn btn-sm btn-danger" data-log-date="<?php echo e($date); ?>">
                                <i class="fa fa-trash-o"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="11" class="text-center">
                            <span class="badge badge-secondary"><?php echo app('translator')->get('The list of logs is empty!'); ?></span>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php echo e($rows->render()); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('modals'); ?>
    
    <div id="delete-log-modal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form id="delete-log-form" action="<?php echo e(route('log-viewer::logs.delete')); ?>" method="POST">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                <input type="hidden" name="date" value="">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo app('translator')->get('Delete log file'); ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary mr-auto" data-dismiss="modal"><?php echo app('translator')->get('Cancel'); ?></button>
                        <button type="submit" class="btn btn-sm btn-danger" data-loading-text="<?php echo app('translator')->get('Loading'); ?>&hellip;"><?php echo app('translator')->get('Delete'); ?></button>
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

            $("a[href='#delete-log-modal']").on('click', function(event) {
                event.preventDefault();
                var date    = $(this).data('log-date'),
                    message = "<?php echo e(__('Are you sure you want to delete this log file: :date ?')); ?>";

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

<?php echo $__env->make('log-viewer::bootstrap-4._master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ploi/insane.bet/vendor/arcanedev/log-viewer/views/bootstrap-4/logs.blade.php ENDPATH**/ ?>