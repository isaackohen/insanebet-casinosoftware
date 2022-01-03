<?php $__env->startComponent('mail::message'); ?>

**Hi {name},**

Thanks for using [Product Name]. This email is the receipt for your purchase. No payment is due.

This purchase will appear as “[Credit Card Statement Name]” on your credit card statement for your {credit_card_brand} ending in {credit_card_last_four}. Need to [update your payment information](#link)?


<?php $__env->startComponent('mail::promotion'); ?>

# 10% off your next purchase!

Thanks for your support! Here's a coupon for 10% off your next purchase if used by {expiration_date}.


<?php $__env->startComponent('mail::button',  ['url' => '#link', 'color' => 'success']); ?>
Use this discount now...
<?php echo $__env->renderComponent(); ?>
			    
<?php echo $__env->renderComponent(); ?>


<?php $__env->startComponent('mail::table'); ?>
| {receipt_id} | {date} |
| ------------- |:-------------:|
| { #each receipt_details} {/each} |
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

<?php $__env->startComponent('mail::button',  ['url' => '#link']); ?>
Download as PDF
<?php echo $__env->renderComponent(); ?>

<?php echo $__env->renderComponent(); ?><?php /**PATH /home/ploi/insane.bet/vendor/qoraiche/laravel-mail-editor/resources/views/skeletons/markdown/postmark/receipt.blade.php ENDPATH**/ ?>