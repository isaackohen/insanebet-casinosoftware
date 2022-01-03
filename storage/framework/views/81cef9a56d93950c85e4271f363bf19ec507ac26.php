<?php $__env->startComponent('mail::message'); ?>

# Your [Product Name] trial expired

Thanks for trying [Product Name]. You've added a lot of data, so here are several options for you to consider going forward.

<?php $__env->startComponent('mail::button',  ['url' => '#link', 'color' => 'success']); ?>
Upgrade your account
<?php echo $__env->renderComponent(); ?>

If you're not ready to upgrade to a paying account, you have a few other options available to you…

<?php $__env->startComponent('mail::panel'); ?>
[Restart your trial](#) - If you didn't get a chance to fully try out the product or need a little more time to evaluate, just let us know. Simply reply to this email and we'll extend your trial period.

[Share feedback](#) - If [Product Name] isn't right for you, let us know what you were looking for and we might be able to suggest some alternatives that might be a better fit.

[Export your data](#) - If [Product Name] wasn't a good fit, you can export your data for use elsewhere.

[Close your account](#) - You can close your account and delete your data. Or, do nothing and we'll automatically close it and delete your data for you in 90 days. But don't worry, we'll send you another email before that happens.
<?php echo $__env->renderComponent(); ?>

Regardless of your choice, we want to say thank you for trying [Product Name]. We know it's an investment of your time, and we appreciate you giving us a chance.

Thanks,<br>
[Sender Name] and the [Product Name] Team
			   
**P.S.** If you have any questions or need any help, please don't hesitate to reach out. You can reply to this email or join us on live chat during business hours.

<?php echo $__env->renderComponent(); ?><?php /**PATH /home/ploi/insane.bet/vendor/qoraiche/laravel-mail-editor/resources/views/skeletons/markdown/postmark/trial-expired.blade.php ENDPATH**/ ?>