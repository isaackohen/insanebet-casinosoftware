<?php $__env->startComponent('mail::message'); ?>

Hi {name},

You recently requested to reset your password for your [Product Name] account. Use the button below to reset it. **This password reset is only valid for the next 24 hours.**

<?php $__env->startComponent('mail::button',  ['url' => '#link', 'color' => 'success']); ?>
Reset your password
<?php echo $__env->renderComponent(); ?>

For security, this request was received from a {operating_system} device using {browser_name}. If you did not request a password reset, please ignore this email or [contact support](#link) if you have questions.

Thanks,<br>
The [Product Name] Team

-----

<small>If you’re having trouble with the button above, copy and paste the URL below into your web browser.</small>

{action_url}

<?php echo $__env->renderComponent(); ?><?php /**PATH /home/ploi/insane.bet/vendor/qoraiche/laravel-mail-editor/resources/views/skeletons/markdown/postmark/reset-password.blade.php ENDPATH**/ ?>