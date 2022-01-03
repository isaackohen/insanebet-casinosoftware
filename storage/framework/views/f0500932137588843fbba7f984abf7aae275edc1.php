<?php
  $error_number = 408;
?>

<?php $__env->startSection('title'); ?>
  Request timeout.
<?php $__env->stopSection(); ?>

<?php $__env->startSection('description'); ?>
  <?php
    $default_error_message = "Please <a href='javascript:history.back()''>go back</a>, refresh the page and try again.";

  ?>
  <?php echo isset($exception)? ($exception->getMessage()?$exception->getMessage():$default_error_message): $default_error_message; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('errors.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ploi/insane.bet/resources/views/errors/408.blade.php ENDPATH**/ ?>