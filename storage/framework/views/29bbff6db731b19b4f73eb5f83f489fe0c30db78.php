<?php $__env->startSection('content'); ?>
    <div class="page-header mb-4">
        <h1>Dashboard</h1>
    </div>

    <div class="row">
        <div class="col-md-6 col-lg-3">
            <canvas id="stats-doughnut-chart" height="300" class="mb-3"></canvas>
        </div>

        <div class="col-md-6 col-lg-9">
            <div class="row">
                <?php $__currentLoopData = $percents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-sm-6 col-md-12 col-lg-4 mb-3">
                        <div class="box level-<?php echo e($level); ?> <?php echo e($item['count'] === 0 ? 'empty' : ''); ?>">
                            <div class="box-icon">
                                <?php echo log_styler()->icon($level); ?>

                            </div>

                            <div class="box-content">
                                <span class="box-number">
                                    <?php echo e($item['count']); ?> entries - <?php echo $item['percent']; ?> %
                                </span>
                                <div class="progress" style="height: 3px;">
                                    <div class="progress-bar" style="width: <?php echo e($item['percent']); ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        $(function() {
            new Chart(document.getElementById("stats-doughnut-chart"), {
                type: 'doughnut',
                data: <?php echo $chartData; ?>,
                options: {
                    legend: {
                        position: 'bottom'
                    }
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('log-viewer::bootstrap-4._master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ploi/insane.bet/resources/views/vendor/log-viewer/bootstrap-4/dashboard.blade.php ENDPATH**/ ?>