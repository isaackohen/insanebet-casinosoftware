<?php $__env->startSection('title', 'View Mailable'); ?>

<?php $__env->startSection('content'); ?>

<div class="col-lg-10 col-md-12">

	<nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo e(route('mailableList')); ?>">Mailables</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo e($resource['name']); ?></li>
      </ol>
    </nav>

                <div class="card my-4">
                    <div class="card-header d-flex align-items-center justify-content-between"><h5>Details</h5>
                    </div>
                    <div class="card-body card-bg-secondary">
                        <table class="table mb-0 table-borderless">
                            <tbody>
                                <tr>
                                    <td class="table-fit font-weight-sixhundred">Name</td>
                                    <td>
                                        <?php echo e($resource['name']); ?>

                                    </td>
                                </tr>
                                <tr>
                                    <td class="table-fit font-weight-sixhundred">Namespace</td>
                                    <td>
                                        <?php echo e($resource['namespace']); ?>

                                    </td>
                                </tr>

                                <?php if( !empty($resource['data']->subject) ): ?>
				    				<tr>
	                                    <td class="table-fit font-weight-sixhundred">Subject</td>
	                                    <td>
	                                        <?php echo e($resource['data']->subject); ?>

	                                    </td>
                                	</tr>
				    			<?php endif; ?>

                                <?php if( !empty($resource['data']->locale) ): ?>
				    				<tr>
	                                    <td class="table-fit font-weight-sixhundred">Locale</td>
	                                    <td>
	                                        <?php echo e($resource['data']->locale); ?>

	                                    </td>
                                	</tr>
				    			<?php endif; ?>

				    				<tr>
	                                    <td class="table-fit font-weight-sixhundred">From</td>
	                                    <td><a href="mailto:<?php echo e(!collect($resource['data']->from)->isEmpty() ? collect($resource['data']->from)->first()['address'] : config('mail.from.address')); ?>" class="badge badge-info mr-1 font-weight-light">
	                                    	<?php if(!collect($resource['data']->from)->isEmpty()): ?>

                            					<?php echo e(collect($resource['data']->from)->first()['address']); ?>


                            					<?php else: ?>

												<?php echo e(config('mail.from.address')); ?> (default)

                            				<?php endif; ?>
                        				</a></td>
                                	</tr>

                                	<tr>
	                                    <td class="table-fit font-weight-sixhundred">Reply To</td>
	                                    <td><a href="mailto:<?php echo e(!collect($resource['data']->replyTo)->isEmpty() ? collect($resource['data']->replyTo)->first()['address'] : config('mail.reply_to.address')); ?>" class="badge badge-info mr-1 font-weight-light">
	                                    	<?php if(!collect($resource['data']->replyTo)->isEmpty()): ?>

                            					<?php echo e(collect($resource['data']->replyTo)->first()['address']); ?>


                            					<?php else: ?>

												<?php echo e(config('mail.reply_to.address')); ?> (default)

                            				<?php endif; ?>
                        				</a></td>

                                	</tr>

                                <?php if( !empty($resource['data']->cc) ): ?>
                                <tr>
                                    <td class="table-fit font-weight-sixhundred">cc</td>
                                    <td>
                                    	<?php $__currentLoopData = $resource['data']->cc; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <a href="mailto:<?php echo e($cc['address']); ?>" class="badge badge-info mr-1 font-weight-light"><?php echo e($cc['address']); ?></a>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <?php if( !empty($resource['data']->bcc) ): ?>
                                <tr>
                                    <td class="table-fit font-weight-sixhundred">bcc</td>
                                    <td>
                                    	<?php $__currentLoopData = $resource['data']->bcc; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bcc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <a href="mailto:<?php echo e($bcc['address']); ?>" class="badge badge-info mr-1 font-weight-light"><?php echo e($bcc['address']); ?></a>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card my-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5>Preview</h5>
                        <div>
                            <?php if( $resource['view_path'] !== null ): ?>
                            <button type="button" class="btn btn-info send-test"><svg fill="#fff" width="20" enable-background="new 0 0 24 24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="m8.75 17.612v4.638c0 .324.208.611.516.713.077.025.156.037.234.037.234 0 .46-.11.604-.306l2.713-3.692z"/>
                                    <path d="m23.685.139c-.23-.163-.532-.185-.782-.054l-22.5 11.75c-.266.139-.423.423-.401.722.023.3.222.556.505.653l6.255 2.138 13.321-11.39-10.308 12.419 10.483 3.583c.078.026.16.04.242.04.136 0 .271-.037.39-.109.19-.116.319-.311.352-.53l2.75-18.5c.041-.28-.077-.558-.307-.722z"/>
                                </svg> <?php echo e(__('Send Test')); ?></button>
                                <a class="btn btn-primary"
                                   href="<?php echo e(route('editMailable', ['name' => $resource['name']])); ?>">Edit Template</a>
                            <?php endif; ?>
                        </div>

                    </div>
                    <div class="embed-responsive embed-responsive-16by9">
					  <iframe class="embed-responsive-item" src="<?php echo e(route('previewMailable', [ 'name' => $resource['name'] ])); ?>" allowfullscreen></iframe>
					</div>
                </div>
            </div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.send-test').click(function(e){
            e.preventDefault();

            notie.input({
                text: 'Test email recipient:',
                type: 'text',
                placeholder: 'Email',
                submitCallback: function (email) {
                    sendTestMail(email)
                },
            });
        });

        function sendTestMail(email) {
            axios.post('<?php echo e(route('sendTestMail')); ?>', {
                email,
                name: '<?php echo e($resource['name']); ?>',
            })
                .then(function (response) {

                    if (response.status === 200) {
                        notie.alert({type: 'success', text: 'Test email sent', time: 4})
                    } else {
                        alert(response.data.message);
                    }
                })

                .catch(function (error) {
                    notie.alert({type: 'error', text: error, time: 4})
                });
        }

    });
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('maileclipse::layout.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ploi/insane.bet/vendor/qoraiche/laravel-mail-editor/resources/views/sections/view-mailable.blade.php ENDPATH**/ ?>