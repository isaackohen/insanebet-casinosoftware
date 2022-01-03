<?php $__env->startSection('body'); ?>
    <div class="container">
        <div class="jumbotron">
            <h1><?php echo e($message); ?></h1>
            <br>
            <p>
                <a class="btn btn-lg btn-primary" href="/" role="button"><?php echo app('translator')->get('tracker::tracker.go_home'); ?></a>
            </p>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('pragmarx/tracker::html', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ploi/insane.bet/vendor/pragmarx/tracker/src/views/message.blade.php ENDPATH**/ ?>