<?php $__env->startSection('page-contents'); ?>
	<div id="pageViewsLine" class="chart no-padding"></div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-secondary-contents'); ?>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-sun-o"></i> <?php echo app('translator')->get('tracker::tracker.page_views_by_country'); ?></h3>
				</div>
				<div class="panel-body">
					<div id="pageViewsByCountry" style="height: 450px;"></div>
				</div>
			</div>
		</div>
	</div><!-- /.row -->

	<?php echo $__env->make('pragmarx/tracker::_summaryPiechart', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('inline-javascript'); ?>
	jQuery(function()
    {
		console.log(jQuery('#pageViews'));

		var pageViewsLine = Morris.Line({
            element: 'pageViewsLine',
            parseTime:false,
			grid: true,
			data: [{'date': 0, 'total': 0}],
			xkey: 'date',
			ykeys: ['total'],
			labels: ['Page Views']
		});

		jQuery.ajax({
			type: "GET",
			url: "<?php echo e(route('tracker.stats.api.pageviews')); ?>",
			data: { }
		})
		.done(function( data ) {
		    console.log(data);
			pageViewsLine.setData(formatDates(data));
		});

		var convertToPlottableData = function(data)
		{
			plottable = [];

			jsondata = JSON.parse(data);

            for(key in jsondata)
            {
                plottable[key] = {
					label: jsondata[key].label,
					data: jsondata[key].value
				}
            }

			return plottable;
        };

		var formatDates = function(data)
        {
			data = JSON.parse(data);

            for(key in data)
            {
                if (data[key].date !== 'undefined')
                {
					data[key].date = moment(data[key].date, "YYYY-MM-DD").format('dddd[,] MMM Do');
				}
            }

			return data;
		};
	});
<?php $__env->stopSection(); ?>

<?php $__env->startSection('required-scripts-top'); ?>
	<!-- Page-Level Plugin Scripts - Main -->
	<script src="<?php echo e($stats_template_path); ?>/bower_components/raphael/raphael.min.js"></script>
	<script src="<?php echo e($stats_template_path); ?>/bower_components/morrisjs/morris.min.js"></script>

	<!-- Page-Level Plugin Scripts - Flot -->
	<!--[if lte IE 8]><script src="<?php echo e($stats_template_path); ?>/js/excanvas.min.js"></script><![endif]-->
	<script src="<?php echo e($stats_template_path); ?>/bower_components/flot/jquery.flot.js"></script>
	<script src="<?php echo e($stats_template_path); ?>/bower_components/flot/jquery.flot.resize.js"></script>
	<script src="<?php echo e($stats_template_path); ?>/bower_components/flot/jquery.flot.pie.js"></script>
    <script src="<?php echo e($stats_template_path); ?>/bower_components/flot.tooltip/js/jquery.flot.tooltip.min.js"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make($stats_layout, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ploi/insane.bet/vendor/pragmarx/tracker/src/views/summary.blade.php ENDPATH**/ ?>