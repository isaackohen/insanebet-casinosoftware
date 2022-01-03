<div class="row">
    <div class="col-xl-3">
        <div class="card">
            <div class="card-body pb-0" style="position: relative;">
                <h5 class="card-title mb-0 header-title">All games</h5>

                <?php
                    $xAxis = []; $data = [];
                    for($i = 7; $i >= 0; $i--) {
                        array_push($xAxis, $i > 0 ? $i.' days ago' : 'Today');
                        array_push($data, \Illuminate\Support\Facades\DB::table('games')->where('created_at', '>=', \Carbon\Carbon::today()->subDays($i))
                            ->where('created_at', '<', \Carbon\Carbon::today()->subDays($i - 1))->count());
                    }

                    $chart = new \ArielMejiaDev\LarapexCharts\LarapexChart();
                    $chart->setType('area')->setHeight(300)->setXAxis($xAxis)->setDataset([[
                        'name' => 'Games',
                        'data' => $data
                    ]]);
                ?>
                <div id="<?php echo e($chart->id); ?>" data-chart="m3" class="apex-charts mt-3"></div>
                <?php echo e($chart->script()); ?>

            </div>
        </div>
    </div>
    <?php $__currentLoopData = \App\Games\Kernel\Game::list(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $game): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($game->metadata()->isPlaceholder()): ?> <?php continue; ?> <?php endif; ?>
        <div class="col-xl-3">
            <div class="card">
                <div class="card-body pb-0" style="position: relative;">
                    <h5 class="card-title mb-0 header-title"><?php echo e($game->metadata()->name()); ?></h5>

                    <?php
                        $xAxis = []; $data = [];
                        for($i = 7; $i >= 0; $i--) {
                            array_push($xAxis, $i > 0 ? $i.' days ago' : 'Today');
                            array_push($data, \Illuminate\Support\Facades\DB::table('games')->where('game', $game->metadata()->id())
                                ->where('created_at', '>=', \Carbon\Carbon::today()->subDays($i))
                                ->where('created_at', '<', \Carbon\Carbon::today()->subDays($i - 1))->count());
                        }

                        $chart = new \ArielMejiaDev\LarapexCharts\LarapexChart();
                        $chart->setType('area')->setHeight(300)->setXAxis($xAxis)->setDataset([[
                            'name' => 'Games',
                            'data' => $data
                        ]]);
                    ?>
                    <div id="<?php echo e($chart->id); ?>" data-chart="m3" class="apex-charts mt-3"></div>
                    <?php echo e($chart->script()); ?>

                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php /**PATH /home/ploi/insane.bet/resources/views/admin/games.blade.php ENDPATH**/ ?>