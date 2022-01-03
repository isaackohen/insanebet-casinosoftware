<script>
    if(window.charts === undefined) window.charts = [];

    var options =
    {
        chart: {
            type: '<?php echo $chart->type(); ?>',
            height: <?php echo $chart->height(); ?>

        },
        plotOptions: {
            bar: <?php echo $chart->horizontal(); ?>

        },
        colors: <?php echo $chart->colors(); ?>,
        series:
        <?php echo $chart->dataset(); ?>,
        dataLabels: {
            enabled: false
        },
        labels: [<?php echo $chart->labels(); ?>],
        title: {
            text: "<?php echo $chart->title(); ?>"
        },
        subtitle: {
            text: '<?php echo $chart->subtitle(); ?>',
            align: '<?php echo $chart->subtitlePosition(); ?>'
        },
        xaxis: {
            categories: <?php echo $chart->xAxis(); ?>

        },
        grid: <?php echo $chart->grid(); ?>,
    }

    var chart = new ApexCharts(document.querySelector("#<?php echo $chart->id(); ?>"), options);
    chart.render();

    window.charts.push(chart);

</script>
<?php /**PATH /home/ploi/insane.bet/resources/views/vendor/larapex-charts/chart/script.blade.php ENDPATH**/ ?>