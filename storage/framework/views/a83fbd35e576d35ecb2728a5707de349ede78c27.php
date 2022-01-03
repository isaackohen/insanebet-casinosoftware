<h1>Hello!</h1>

<p>You are receiving this email because we received a password reset request for your account.</p>

<p>Visit this URL for a password reset: <a href="<?php echo e(config('app.url')); ?>/password/reset/<?php echo e($user); ?>/<?php echo e($token); ?>"><?php echo e(config('app.url')); ?>/password/reset/<?php echo e($user); ?>/<?php echo e($token); ?></a></p>

<p>If you did not request a password reset, no further action is required.</p>

<p>Regards,<br><?php echo e(config('app.url')); ?></p>
<?php /**PATH /home/ploi/insane.bet/resources/views/mail/resetPassword.blade.php ENDPATH**/ ?>