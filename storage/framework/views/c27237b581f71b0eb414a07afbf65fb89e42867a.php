<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>">
    <head>
        <title><?php echo e($code ?? -1); ?></title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <meta name="description" content="<?php echo e(__('general.head.description')); ?>">

        <meta property="og:description" content="<?php echo e(__('general.head.description')); ?>" />
        <meta property="og:image" content="<?php echo e(asset('/img/misc/logo_white.svg')); ?>" />
        <meta property="og:image:secure_url" content="<?php echo e(asset('/img/misc/logo_white.svg')); ?>" />
        <meta property="og:image:type" content="image/svg+xml" />
        <meta property="og:image:width" content="295" />
        <meta property="og:image:height" content="295" />
        <meta property="og:site_name" content="playin.team" />

        <link rel="icon" href="<?php echo e(asset('/img/misc/logo_white.svg')); ?>">
        <link rel="stylesheet" href="<?php echo e(mix('/css/error.css')); ?>">
    </head>
    <body>
        <div class="code">
            <div><?php echo e($code ?? -1); ?></div>
            <div><?php echo e($desc ?? 'An error has occurred'); ?></div>
        </div>
    </body>
</html>
<?php /**PATH /home/ploi/insane.bet/resources/views/errors/error.blade.php ENDPATH**/ ?>