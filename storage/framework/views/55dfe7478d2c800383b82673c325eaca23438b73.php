<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo app('translator')->get("tracker::tracker.tracker_title"); ?></title>

	<script src="<?php echo e($stats_template_path); ?>/vendor/jquery/jquery.min.js"></script>

	<?php echo $__env->yieldContent('required-scripts-top'); ?>

    <!-- Core CSS - Include with every page -->
    <link href="<?php echo e($stats_template_path); ?>/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo e($stats_template_path); ?>/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<link href="<?php echo e($stats_template_path); ?>/vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Page-Level Plugin CSS - Dashboard -->
    <link href="<?php echo e($stats_template_path); ?>/vendor/morrisjs/morris.css" rel="stylesheet">

    <!-- SB Admin CSS - Include with every page -->
    <link href="<?php echo e($stats_template_path); ?>/dist/css/sb-admin-2.css" rel="stylesheet">

    <link href="//cdn.datatables.net/1.10.2/css/jquery.dataTables.css" rel="stylesheet">

	<link
		rel="stylesheet"
		type="text/css"
		href="https://github.com/downloads/lafeber/world-flags-sprite/flags16.css"
	/>
</head>

<body>
    <?php echo $__env->yieldContent('body'); ?>

    <!-- Core Scripts - Include with every page -->
    <script src="<?php echo e($stats_template_path); ?>/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?php echo e($stats_template_path); ?>/vendor/metisMenu/metisMenu.min.js"></script>

    <script src="<?php echo e($stats_template_path); ?>/vendor/raphael/raphael.min.js"></script>
    <script src="<?php echo e($stats_template_path); ?>/vendor/morrisjs/morris.min.js"></script>

    <!-- SB Admin Scripts - Include with every page -->
    <script src="<?php echo e($stats_template_path); ?>/js/sb-admin-2.js"></script>

    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.7.0/moment.min.js"></script>

    <script src="//cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script>

	<?php echo $__env->yieldContent('required-scripts-bottom'); ?>

    <script>
	    <?php echo $__env->yieldContent('inline-javascript'); ?>
    </script>
</body>

</html>
<?php /**PATH /home/ploi/insane.bet/vendor/pragmarx/tracker/src/views/html.blade.php ENDPATH**/ ?>