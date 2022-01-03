

<?php $__env->startSection('body'); ?>
    <div id="wrapper">
	    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
	            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only"><?php echo app('translator')->get("tracker::tracker.toggle_navigation"); ?></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo e(route('tracker.stats.index')); ?>"><?php echo app('translator')->get("tracker::tracker.tracker_title"); ?></a>
            </div>
            <!-- /.navbar-header -->

		    <ul class="nav navbar-top-links navbar-right navbar-nav">
				<li <?php echo e(Session::get('tracker.stats.days') == '0' ? 'class="active"' : ''); ?>>
					<a href="<?php echo e(route('tracker.stats.index')); ?>?days=0"><?php echo app('translator')->get("tracker::tracker.today"); ?></a>
				</li>

				<li <?php echo e(Session::get('tracker.stats.days') == '1' ? 'class="active"' : ''); ?>>
					<a href="<?php echo e(route('tracker.stats.index')); ?>?days=1"><?php echo app('translator')->choice("tracker::tracker.no_days",1, ["count" => 1]); ?></a>
				</li>

				<li <?php echo e(Session::get('tracker.stats.days') == '7' ? 'class="active"' : ''); ?>>
					<a href="<?php echo e(route('tracker.stats.index')); ?>?days=7"><?php echo app('translator')->choice("tracker::tracker.no_days",7, ["count" => 7]); ?></a>
				</li>

				<li <?php echo e(Session::get('tracker.stats.days') == '30' ? 'class="active"' : ''); ?>>
					<a href="<?php echo e(route('tracker.stats.index')); ?>?days=30"><?php echo app('translator')->choice("tracker::tracker.no_days",30, ["count" => 30]); ?></a>
				</li>

				<li <?php echo e(Session::get('tracker.stats.days') == '365' ? 'class="active"' : ''); ?>>
					<a href="<?php echo e(route('tracker.stats.index')); ?>?days=365"><?php echo app('translator')->choice("tracker::tracker.no_years",1, ["count" => 1]); ?></a>
				</li>
            </ul>
            <!-- /.navbar-top-links -->

		    <div class="navbar-default sidebar" role="navigation">
			    <div class="sidebar-nav navbar-collapse">
				    <ul class="nav" id="side-menu">
                        <li>
                            <a href="<?php echo e(route('tracker.stats.index')); ?>?page=visits" class="<?php echo e(Session::get('tracker.stats.page') =='visits' ? 'active' : ''); ?>" ><i class="fa fa-dashboard fa-fw"></i> <?php echo app('translator')->get("tracker::tracker.visits"); ?> <span class="<?php echo e(Session::get('tracker.stats.page') =='visits' ? 'fa arrow' : ''); ?>"></span></a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('tracker.stats.index')); ?>?page=summary" class="<?php echo e(Session::get('tracker.stats.page') =='summary' ? 'active' : ''); ?>"><i class="fa fa-bar-chart-o fa-fw"></i> <?php echo app('translator')->get("tracker::tracker.summary"); ?> <span class="<?php echo e(Session::get('tracker.stats.page') =='summary' ? 'fa arrow' : ''); ?>"></span></a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('tracker.stats.index')); ?>?page=users" class="<?php echo e(Session::get('tracker.stats.page') =='users' ? 'active' : ''); ?>"><i class="fa fa-user fa-fw"></i> <?php echo app('translator')->get("tracker::tracker.users"); ?> <span class="<?php echo e(Session::get('tracker.stats.page') =='users' ? 'fa arrow' : ''); ?>"></span></a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('tracker.stats.index')); ?>?page=events" class="<?php echo e(Session::get('tracker.stats.page') =='events' ? 'active' : ''); ?>"><i class="fa fa-bolt fa-fw"></i> <?php echo app('translator')->get("tracker::tracker.events"); ?> <span class="<?php echo e(Session::get('tracker.stats.page') =='events' ? 'fa arrow' : ''); ?>"></span></a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('tracker.stats.index')); ?>?page=errors" class="<?php echo e(Session::get('tracker.stats.page') =='errors' ? 'active' : ''); ?>"><i class="fa fa-exclamation fa-fw"></i><?php echo app('translator')->get("tracker::tracker.errors"); ?> <span class="<?php echo e(Session::get('tracker.stats.page') =='errors' ? 'fa arrow' : ''); ?>"></span></a>
                        </li>
                    </ul>
                    <!-- /#side-menu -->
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h2 class="page-header"><?php echo e($title); ?></h2>
                </div>

	            <div class="col-lg-12">
		            <?php echo $__env->yieldContent('page-contents'); ?>
	            </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">
			        <?php echo $__env->yieldContent('page-secondary-contents'); ?>
		        </div>
	        </div>

            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('pragmarx/tracker::html', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ploi/insane.bet/vendor/pragmarx/tracker/src/views/layout.blade.php ENDPATH**/ ?>