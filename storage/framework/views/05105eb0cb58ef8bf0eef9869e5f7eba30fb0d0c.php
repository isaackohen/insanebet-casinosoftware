<!DOCTYPE html>
<html>
    <head>
        <title><?php echo e(env('APP_PAGETITLE') ?? 'Casino'); ?></title>
        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, height=device-height, minimum-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<link
  href="https://unpkg.com/sanitize.css"
  rel="stylesheet"
/>        
        <script type="text/javascript">
            window.Layout = {
                Frontend: '<?php echo base64_encode(file_get_contents(public_path('css/app.css'))); ?>'
            }
        </script>

        <link rel="icon" href="<?php echo e(asset(env('MIX_APP_FAVICON')) ?? asset('/img/misc/favicon.png')); ?>">
        <script src="https://kit.fontawesome.com/23f13eab24.js" crossorigin="anonymous"></script>

        <?php if(config('app.debug')): ?>
            <meta http-equiv="Expires" content="Mon, 26 Jul 1997 05:00:00 GMT">
            <meta http-equiv="Pragma" content="no-cache">
        <?php endif; ?>



        <link rel="manifest" href="/manifest.json">
		<script async src="https://www.googletagmanager.com/gtag/js?id=G-Z7HD7P5H38"></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());

		  gtag('config', 'G-Z7HD7P5H38');
		</script>

        <meta property="og:title" content="<?php echo e(env('APP_PAGETITLE') ?? 'Casino'); ?>" />
        <meta property="og:type" content="website" />
        <meta property="og:image" content="<?php echo e(env('MIX_APP_OGIMAGE') ?? 'Casino'); ?>" />
        <meta property="og:image:width" content="1200"/>
        <meta property="og:image:height" content="630"/>
        <meta property="og:image:secure_url" content="<?php echo e(env('MIX_APP_OGIMAGE') ?? 'Casino'); ?>" />
        <meta name="description" content="<?php echo e(env('MIX_APP_DESC') ?? 'Casino'); ?>"/>
        <meta property="og:image:type" content="image/svg+xml" />


        
        <style>
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 99999;
            display: flex;
            flex-flow: row nowrap;
            justify-content: center;
            align-items: center;
            background: radial-gradient(circle at 110% 70%,#323534,#323534 45%);
            transition: all 0.2s ease-in-out;
        }

        </style>

    </head>

    <body>

        <div id="app">
        <div class="preloader">
            <img style="height: 300px; position: absolute; top: 0; bottom: 0; margin: auto; left: 0; right: 0;" src="<?php echo e(asset(env('MIX_APP_BIG_PRELOADER')) ?? "https://games.cdn4.dk/loading.gif"); ?>">
        </div>
            <layout></layout>
        </div>

        <script src="<?php echo e(asset(mix('/js/app.js'))); ?>"></script>

        <?php if(config('app.debug')): ?>
            <script src="http://localhost:8098"></script>
        <?php endif; ?>


    <?php if(auth()->guard()->check()): ?>
    <?php if(env('APP_CRISPCHATKEY') !== 'off'): ?>
        <script type="text/javascript">window.$crisp=[];window.CRISP_WEBSITE_ID="<?php echo e(env('APP_CRISPCHATKEY')); ?>";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.chat/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();
    </script>
    <?php
    $deposits = (\App\Invoice::where('user', auth()->user()->_id)->where('status', 1)->where('ledger', '!=','Offerwall Credit')->count());
    $registercount = (\App\User::where('register_multiaccount_hash', auth()->user()->register_multiaccount_hash)->count());
    $logincount = (\App\User::where('login_multiaccount_hash', auth()->user()->login_multiaccount_hash)->count());
    $registeripcount = (\App\User::where('register_ip', auth()->user()->register_ip)->count());
    $loginipcount = (\App\User::where('login_ip', auth()->user()->login_ip)->count());

    ?>


    <script>
        $crisp.push(["set", "user:nickname", ["<?php echo e(auth()->user()->name); ?>"]])
        $crisp.push(["set", "user:email", ["<?php echo e(auth()->user()->email); ?>"]])
        $crisp.push(["set", "session:data", [[["uid", "<?php echo e(auth()->user()->id); ?>" ], ["vipLevel", "<?php echo e(auth()->user()->vipLevel()); ?>"], ["deposits", "<?php echo e($deposits); ?>"],   ["freespins", "<?php echo e(auth()->user()->freespins); ?>"], ["created", "<?php echo e(auth()->user()->created_at); ?>"], ["register_ip", "<?php echo e(auth()->user()->register_ip); ?>"], ["login_ip", "<?php echo e(auth()->user()->login_ip); ?>"], ["accounts_loginhash", "<?php echo e($logincount); ?>"], ["accounts_registerhash", "<?php echo e($registercount); ?>"], ["accounts_registerip", "<?php echo e($registeripcount); ?>"], ["accounts_loginip", "<?php echo e($loginipcount); ?>"],  ]]])

    </script>
    <?php endif; ?>
    <?php endif; ?>

    </body>           

</html>
<?php /**PATH /home/ploi/demo.managedcasino.com/resources/views/layouts/app.blade.php ENDPATH**/ ?>