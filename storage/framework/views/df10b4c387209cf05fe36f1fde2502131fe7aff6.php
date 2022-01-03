<?php $__env->startComponent('mail::message'); ?>

# Welcome, [name]!

Thanks for trying [Product Name]. We’re thrilled to have you on board.

To get the most out of [Product Name], do this primary next step:


<?php $__env->startComponent('mail::button',  ['url' => '#link']); ?>
Do this Next
<?php echo $__env->renderComponent(); ?>

For reference, here's your login information:


<?php $__env->startComponent('mail::panel'); ?>
**Login Page: **[login_url]<br>
**Username:** [username]
<?php echo $__env->renderComponent(); ?>

You've started a [trial_length] day trial. You can upgrade to a paying account or cancel any time.

<?php $__env->startComponent('mail::panel'); ?>
**Trial Start Date: **[trial_start_date]<br>
**Trial End Date:** [trial_end_date]
<?php echo $__env->renderComponent(); ?>


If you have any questions, feel free to [email our customer success team.](#) (We're lightning quick at replying.) We also offer live chat during business hours.

Thanks,<br>
[Sender Name] and the [Product Name] Team

P.S. Need immediate help getting started? Check out our [help documentation](#). Or, just reply to this email, the [Product Name] support team is always ready to help!

<?php echo $__env->renderComponent(); ?><?php /**PATH /home/ploi/insane.bet/vendor/qoraiche/laravel-mail-editor/resources/views/skeletons/markdown/postmark/welcome.blade.php ENDPATH**/ ?>