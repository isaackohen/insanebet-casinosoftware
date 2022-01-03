<?php
    $humanDiff = function(array $array) {
        foreach($array as $value) $array[array_search($value, $array)] = \Carbon\Carbon::parse($value)->toFormattedDateString();
        return $array;
    };
?>

<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body pb-0" style="position: relative;">
                <h5 class="card-title mb-0 header-title">Income</h5>

                <?php
                    $fix = function($n) {
                        return is_float($n) || is_int($n) ? floatval($n) : floatval($n->jsonSerialize()['$numberDecimal']);
                    };

                    $fill_data_currency = function($days, $currency_id) use($fix) {
                        $out = [];
                        for($i = 0; $i < $days; $i++)
                            array_push($out, $fix(\Illuminate\Support\Facades\DB::table('invoices')
                                ->where('created_at', '>=', \Carbon\Carbon::today()->subDays($i))
                                ->where('created_at', '<', \Carbon\Carbon::today()->subDays($i - 1))
                                ->where('currency', $currency_id)->where('status', 1)->sum('sum')));
                        return array_reverse($out);
                    };
                    $fill_labels = function($days) {
                        $out = [];
                        for($i = 0; $i < $days; $i++)
                            array_push($out, $i > 0 ? $i .' days ago' : 'Today');
                        return array_reverse($out);
                    };

                    $merge = [];
                    foreach (\App\Currency\Currency::all() as $currency) {
                        array_push($merge, [
                            'name' => $currency->name(),
                            'data' => $fill_data_currency(7, $currency->id())
                        ]);
                    }

                    $chart = new \ArielMejiaDev\LarapexCharts\LarapexChart();
                    $chart->setTitle('One week')->setType('area')->setHeight(680)->setXAxis($fill_labels(7))->setDataset($merge);
                ?>
                <div id="<?php echo e($chart->id); ?>" data-chart="m3" class="apex-charts mt-3"></div>
                <?php echo e($chart->script()); ?>

            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body pt-2">
                <a href="/admin/wallet" class="btn btn-primary btn-sm float-end">
                    View
                </a>
                <h6 class="header-title mb-4">Latest withdraws</h6>
                <?php if(\App\Withdraw::where('status', 0)->count() == 0): ?>
                    <i style="display: flex; margin-left: auto; margin-right: auto;" data-feather="clock"></i>
                    <div class="text-center mt-2">Nothing here</div>
                <?php else: ?>
                    <?php $__currentLoopData = \App\Withdraw::where('status', 0)->latest()->take(5)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $withdraw): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $user = \App\User::where('_id', $withdraw->user)->first(); if(!$user) continue; ?>
                        <div class="d-flex border-top pt-3">
                            <img src="<?php echo e($user->avatar); ?>" class="avatar rounded me-3" alt="shreyu">
                            <div>
                                <h6 class="mt-1 mb-0 font-size-15"><?php echo e($user->name); ?></h6>
                                <h6 class="text-muted font-weight-normal mt-1 me-3"><?php echo e(number_format($withdraw->sum, 8, '.', ' ')); ?> <?php echo e(\App\Currency\Currency::find($withdraw->currency)->name()); ?></h6>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="card">
            <div class="card-body pt-2">
                <h6 class="header-title mb-4">Countries</h6>
                <?php
                    $data = (array) \Spatie\Analytics\AnalyticsFacade::performQuery(\Spatie\Analytics\Period::days(7), 'ga:sessions', [
                        'metrics' => 'ga:sessions',
                        'dimensions' => 'ga:country'
                    ])['rows'];

                    $labels = []; $dataset = [];
                    foreach ($data as $info) {
                        array_push($labels, $info[0]);
                        array_push($dataset, intval($info[1]));
                    }

                    $chart = new \ArielMejiaDev\LarapexCharts\LarapexChart();
                    $chart->setType('pie')->setHeight(300)->setLabels($labels)->setDataset($dataset);
                ?>
                <div id="<?php echo e($chart->id); ?>" data-chart="m3" class="apex-charts mt-3"></div>
                <?php echo e($chart->script()); ?>

            </div>
        </div>
    </div>
</div>
<?php /**PATH /home/ploi/insane.bet/resources/views/admin/dashboard.blade.php ENDPATH**/ ?>