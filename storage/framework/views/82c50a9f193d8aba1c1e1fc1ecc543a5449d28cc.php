<!DOCTYPE html>
<html>
    <head>
        <title>Casino Admin</title>
        <script src="https://kit.fontawesome.com/23f13eab24.js" crossorigin="anonymous"></script>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, height=device-height, minimum-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <!--
        <meta property="og:image" content="<?php echo e(asset('https://i.imgur.com/HEAm2j7.png')); ?>" />
        <meta property="og:image:secure_url" content="<?php echo e(asset('https://i.imgur.com/omJ7On3.png')); ?>" />
        <meta property="og:image:type" content="image/svg+xml" />
        <meta property="og:image:width" content="295" />
        <meta property="og:image:height" content="295" />
        !-->
        <?php if(config('app.debug')): ?>
            <meta http-equiv="Expires" content="Mon, 26 Jul 1997 05:00:00 GMT">
            <meta http-equiv="Pragma" content="no-cache">
        <?php endif; ?>

        <link rel="icon" href="<?php echo e(asset('/favicon.svg')); ?>">
        <link rel="manifest" href="/manifest.json">

        <script type="text/javascript">
            window.Layout = {
                Backend: '<?php echo base64_encode(file_get_contents(public_path('css/admin/app.css'))); ?>'
            }
        </script>

        <script>
            window.Notifications = {
                vapidPublicKey: '<?php echo e(config('webpush.vapid.public_key')); ?>'
            };
        </script>

    </head>
    <body>
        <div id="app">
            <layout></layout>
        </div>

        <script src="<?php echo e(asset(mix('/js/admin/app.js'))); ?>"></script>

        <?php if(config('app.debug')): ?>
            <script src="http://localhost:8098"></script>
        <?php endif; ?>
    </body>
</html>
<?php /**PATH /home/ploi/insane.bet/resources/views/layouts/admin.blade.php ENDPATH**/ ?>