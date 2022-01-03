<?php
/**
 * @var  Arcanedev\LogViewer\Entities\Log            $log
 * @var  Illuminate\Pagination\LengthAwarePaginator  $entries
 * @var  string|null                                 $query
 */
?>



<?php $__env->startSection('content'); ?>
    <h1 class="page-header"><?php echo e(__('Log')); ?> [<?php echo e($log->date); ?>]</h1>

    <div class="row">
        <div class="col-md-2">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-fw fa-flag"></i> <?php echo e(__('Levels')); ?></div>
                <ul class="list-group">
                    <?php $__currentLoopData = $log->menu(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $levelKey => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($item['count'] === 0): ?>
                            <a href="#" class="list-group-item disabled">
                                <span class="badge">
                                    <?php echo e($item['count']); ?>

                                </span>
                                <?php echo $item['icon']; ?> <?php echo e($item['name']); ?>

                            </a>
                        <?php else: ?>
                            <a href="<?php echo e($item['url']); ?>" class="list-group-item <?php echo e($levelKey); ?>">
                                <span class="badge level-<?php echo e($levelKey); ?>">
                                    <?php echo e($item['count']); ?>

                                </span>
                                <span class="level level-<?php echo e($levelKey); ?>">
                                    <?php echo $item['icon']; ?> <?php echo e($item['name']); ?>

                                </span>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
        <div class="col-md-10">
            
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?php echo e(__('Log info')); ?> :

                    <div class="group-btns pull-right">
                        <a href="<?php echo e(route('log-viewer::logs.download', [$log->date])); ?>" class="btn btn-xs btn-success">
                            <i class="fa fa-download"></i> <?php echo e(__('Download')); ?>

                        </a>
                        <a href="#delete-log-modal" class="btn btn-xs btn-danger" data-toggle="modal">
                            <i class="fa fa-trash-o"></i> <?php echo e(__('Delete')); ?>

                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <td><?php echo e(__('File path')); ?> :</td>
                                <td colspan="5"><?php echo e($log->getPath()); ?></td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo e(__('Log entries')); ?> :</td>
                                <td>
                                    <span class="label label-primary"><?php echo e($entries->total()); ?></span>
                                </td>
                                <td><?php echo e(__('Size')); ?> :</td>
                                <td>
                                    <span class="label label-primary"><?php echo e($log->size()); ?></span>
                                </td>
                                <td><?php echo e(__('Created at')); ?> :</td>
                                <td>
                                    <span class="label label-primary"><?php echo e($log->createdAt()); ?></span>
                                </td>
                                <td><?php echo e(__('Updated at')); ?> :</td>
                                <td>
                                    <span class="label label-primary"><?php echo e($log->updatedAt()); ?></span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer">
                    
                    <form action="<?php echo e(route('log-viewer::logs.search', [$log->date, $level])); ?>" method="GET">
                        <div class=form-group">
                            <div class="input-group">
                                <input id="query" name="query" class="form-control" value="<?php echo e($query); ?>" placeholder="<?php echo e(__('Type here to search')); ?>">
                                <span class="input-group-btn">
                                    <?php if (! (is_null($query))): ?>
                                        <a href="<?php echo e(route('log-viewer::logs.show', [$log->date])); ?>" class="btn btn-default">
                                            (<?php echo app('translator')->get(':count results', ['count' => $entries->count()]); ?>) <span class="glyphicon glyphicon-remove"></span>
                                        </a>
                                    <?php endif; ?>
                                    <button id="search-btn" class="btn btn-primary">
                                        <span class="glyphicon glyphicon-search"></span>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            
            <div class="panel panel-default">
                <?php if($entries->hasPages()): ?>
                    <div class="panel-heading">
                        <?php echo e($entries->appends(compact('query'))->render()); ?>


                        <span class="label label-info pull-right">
                            <?php echo e(__('Page :current of :last', ['current' => $entries->currentPage(), 'last' => $entries->lastPage()])); ?>

                        </span>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table id="entries" class="table table-condensed">
                        <thead>
                            <tr>
                                <th><?php echo e(__('ENV')); ?></th>
                                <th style="width: 120px;"><?php echo e(__('Level')); ?></th>
                                <th style="width: 65px;"><?php echo e(__('Time')); ?></th>
                                <th><?php echo e(__('Header')); ?></th>
                                <th class="text-right"><?php echo e(__('Actions')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php /** @var  Arcanedev\LogViewer\Entities\LogEntry  $entry */ ?>
                                <tr>
                                    <td>
                                        <span class="label label-env"><?php echo e($entry->env); ?></span>
                                    </td>
                                    <td>
                                        <span class="level level-<?php echo e($entry->level); ?>"><?php echo $entry->level(); ?></span>
                                    </td>
                                    <td>
                                        <span class="label label-default">
                                            <?php echo e($entry->datetime->format('H:i:s')); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <p><?php echo e($entry->header); ?></p>
                                    </td>
                                    <td class="text-right">
                                        <?php if($entry->hasStack()): ?>
                                        <a class="btn btn-xs btn-default" role="button" data-toggle="collapse"
                                           href="#log-stack-<?php echo e($key); ?>" aria-expanded="false" aria-controls="log-stack-<?php echo e($key); ?>">
                                            <i class="fa fa-toggle-on"></i> <?php echo e(__('Stack')); ?>

                                        </a>
                                        <?php endif; ?>

                                        <?php if($entry->hasContext()): ?>
                                        <a class="btn btn-xs btn-default" role="button" data-toggle="collapse"
                                           href="#log-context-<?php echo e($key); ?>" aria-expanded="false" aria-controls="log-context-<?php echo e($key); ?>">
                                            <i class="fa fa-toggle-on"></i> <?php echo e(__('Context')); ?>

                                        </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php if($entry->hasStack() || $entry->hasContext()): ?>
                                    <tr>
                                        <td colspan="5" class="stack">
                                            <?php if($entry->hasStack()): ?>
                                            <div class="stack-content collapse" id="log-stack-<?php echo e($key); ?>">
                                                <?php echo $entry->stack(); ?>

                                            </div>
                                            <?php endif; ?>

                                            <?php if($entry->hasContext()): ?>
                                            <div class="stack-content collapse" id="log-context-<?php echo e($key); ?>">
                                                <pre><?php echo e($entry->context()); ?></pre>
                                            </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center">
                                        <span class="label label-default"><?php echo e(__('The list of logs is empty!')); ?></span>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if($entries->hasPages()): ?>
                    <div class="panel-footer">
                        <?php echo $entries->appends(compact('query'))->render(); ?>


                        <span class="label label-info pull-right">
                            <?php echo app('translator')->get('Page :current of :last', ['current' => $entries->currentPage(), 'last' => $entries->lastPage()]); ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modals'); ?>
    
    <div id="delete-log-modal" class="modal fade">
        <div class="modal-dialog">
            <form id="delete-log-form" action="<?php echo e(route('log-viewer::logs.delete')); ?>" method="POST">
                <?php echo method_field('DELETE'); ?>
                <?php echo csrf_field(); ?>
                <input type="hidden" name="date" value="<?php echo e($log->date); ?>">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title"><?php echo e(__('Delete log file')); ?></h4>
                    </div>
                    <div class="modal-body">
                        <p><?php echo e(__('Are you sure you want to delete this log file: :date ?', ['date' => $log->date])); ?></p>
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
                            location.replace("<?php echo e(route('log-viewer::logs.list')); ?>");
                        }
                        else {
                            alert('OOPS ! This is a lack of coffee exception !')
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

            <?php if (! (empty(log_styler()->toHighlight()))): ?>
                <?php
                    $htmlHighlight = version_compare(PHP_VERSION, '7.4.0') >= 0
                        ? join('|', log_styler()->toHighlight())
                        : join(log_styler()->toHighlight(), '|');
                ?>
                $('.stack-content').each(function() {
                    var $this = $(this);
                    var html = $this.html().trim()
                        .replace(/(<?php echo $htmlHighlight; ?>)/gm, '<strong>$1</strong>');

                    $this.html(html);
                });
            <?php endif; ?>
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('log-viewer::bootstrap-3._master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ploi/insane.bet/resources/views/vendor/log-viewer/bootstrap-3/show.blade.php ENDPATH**/ ?>