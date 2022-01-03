<?php $__env->startSection('title', 'New Template'); ?>

<?php $__env->startSection('editor', true); ?>

<?php $__env->startSection('content'); ?>

<div class="col-lg-10 col-md-12">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo e(route('templateList')); ?>">Templates</a></li>
        <li class="breadcrumb-item active" aria-current="page">Add Template</li>
      </ol>
    </nav>
                

                <div class="card mb-3">
                    <ul class="nav nav-pills" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pills-html-tab" data-toggle="pill" href="#pills-html" role="tab" aria-controls="pills-html" aria-selected="true">HTML</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-markdown-tab" data-toggle="pill" href="#pills-markdown" role="tab" aria-controls="pills-markdown" aria-selected="false">Markdown</a>
                        </li>
                    </ul>
                </div>

                <div class="card">
                    <div class="card-body">
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-html" role="tabpanel" aria-labelledby="pills-html-tab">
                            <div class="card-columns">

<?php $__currentLoopData = $skeletons->get('html'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $subskeleton): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<div class="card">
    <div class="content template-item" data-toggle="modal" data-target="#select<?php echo e($name); ?>Modal">
      <div class="content-overlay"></div>

      <?php if( file_exists( public_path("vendor/maileclipse/images/skeletons/html/{$name}.png") ) ): ?>

        <img class="content-image card-img-top" src="<?php echo e(asset('vendor/maileclipse/images/skeletons/html/'.$name.'.png' )); ?>" alt="<?php echo e($name); ?>">

      <?php elseif( file_exists( public_path( "vendor/maileclipse/images/skeletons/html/{$name}.jpg" ) ) ): ?>

        <img class="content-image card-img-top" src="<?php echo e(asset('vendor/maileclipse/images/skeletons/html/'.$name.'.jpg' )); ?>" alt="<?php echo e($name); ?>">

      <?php else: ?>

      <img class="content-image card-img-top" src="<?php echo e(asset('vendor/maileclipse/images/skeletons/no-image.png' )); ?>" alt="<?php echo e($name); ?>">

      <?php endif; ?>

      <div class="content-details">
        <h4 class="content-title mb-3"><?php echo e($name); ?></h4>
        <!-- <p class="content-text">This is a short description</p> -->
      </div>

</div>
</div>


<!-- Modal -->
<?php $__currentLoopData = $subskeleton; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skeleton): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="modal fade" id="select<?php echo e($name); ?>Modal" tabindex="-1" role="dialog" aria-labelledby="selectTemplateModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="selectTemplateModalLabel"><?php echo e(ucfirst($name)); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Select Template:</p>
                    <div class="list-group list-group-flush">
                        <?php $__currentLoopData = $subskeleton; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skeleton): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('newTemplate', ['type' => 'html','name' => $name, 'skeleton' => $skeleton])); ?>" class="list-group-item list-group-item-action"><?php echo e($skeleton); ?></a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <!-- End modal -->

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</div>

                        </div>
                        <div class="tab-pane fade" id="pills-markdown" role="tabpanel" aria-labelledby="pills-markdown-tab">
                            <div class="card-columns">
                            <!-- markdown -->
                            <?php $__currentLoopData = $skeletons->get('markdown'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $subskeleton): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="card">
<!-- <img class="card-img-top" src="https://1rj8i398ld62y6ih02fyvv4k-wpengine.netdna-ssl.com/wp-content/uploads/2018/12/mantra-welcome.png" alt="Card image cap"> -->
    <div class="content template-item" data-toggle="modal" data-target="#<?php echo e($name); ?>Modal">
      <div class="content-overlay"></div>

      <?php if( file_exists( public_path("vendor/maileclipse/images/skeletons/markdown/{$name}.png") ) ): ?>

        <img class="content-image card-img-top" src="<?php echo e(asset('vendor/maileclipse/images/skeletons/markdown/'.$name.'.png' )); ?>" alt="<?php echo e($name); ?>">

      <?php elseif( file_exists( public_path( "vendor/maileclipse/images/skeletons/markdown/{$name}.jpg" ) ) ): ?>

        <img class="content-image card-img-top" src="<?php echo e(asset('vendor/maileclipse/images/skeletons/markdown/'.$name.'.jpg' )); ?>" alt="<?php echo e($name); ?>">

      <?php else: ?>

      <img class="content-image card-img-top" src="<?php echo e(asset('vendor/maileclipse/images/skeletons/no-image.png' )); ?>" alt="<?php echo e($name); ?>">

      <?php endif; ?>

      <div class="content-details">
        <h4 class="content-title mb-3"><?php echo e($name); ?></h4>
      </div>

</div>
</div>

<!-- Modal -->

    <div class="modal fade" id="<?php echo e($name); ?>Modal" tabindex="-1" role="dialog" aria-labelledby="selectTemplateModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="selectTemplateModalLabel"><?php echo e(ucfirst($name)); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Select Template:</p>
                    <div class="list-group list-group-flush">
                        <?php $__currentLoopData = $subskeleton; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skeleton): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <a href="<?php echo e(route('newTemplate', ['type' => 'markdown','name' => $name, 'skeleton' => $skeleton])); ?>" class="list-group-item list-group-item-action"><?php echo e($skeleton); ?></a>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End modal -->

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    </div>
                </div>
</div>
            </div>
   
<?php $__env->stopSection(); ?>
<?php echo $__env->make('maileclipse::layout.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ploi/insane.bet/vendor/qoraiche/laravel-mail-editor/resources/views/sections/new-template.blade.php ENDPATH**/ ?>