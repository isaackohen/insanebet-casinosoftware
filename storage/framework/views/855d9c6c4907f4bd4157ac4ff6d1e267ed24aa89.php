<?php $__env->startComponent('mail::message'); ?>

**Hi {name},**

Thanks for using [Product Name]. This is an invoice for your recent purchase.

**Amount Due:** {total}<br>
**Due By:** {due_date}


<?php $__env->startComponent('mail::button',  ['url' => '#link', 'color' => 'success']); ?>
Pay this Invoice
<?php echo $__env->renderComponent(); ?>

<?php $__env->startComponent('mail::table'); ?>
| {invoice_id} | {date} |
| ------------- |:-------------:|
| { #each invoice_details} {/each} |
<?php echo $__env->renderComponent(); ?>

<?php $__env->startComponent('mail::table'); ?>
| Description | Amount |
| ------------- |:-------------:|
| {description} | {amount} |
| |
| | **Total** {total} |
<?php echo $__env->renderComponent(); ?>
			    
If you have any questions about this receipt, simply reply to this email or reach out to our [support team](#link) for help.

Cheers,
The [Product Name] Team

-----

<small>If you’re having trouble with the button above, copy and paste the URL below into your web browser.</small>

{action_url}

<?php echo $__env->renderComponent(); ?><?php /**PATH /home/ploi/insane.bet/vendor/qoraiche/laravel-mail-editor/resources/views/skeletons/markdown/postmark/invoice.blade.php ENDPATH**/ ?>