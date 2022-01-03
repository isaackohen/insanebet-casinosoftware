<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script>
	google.load("visualization", "1", {packages:["corechart"]});

	google.setOnLoadCallback(drawChart);

	function drawChart()
	{
		jQuery.ajax({
			type: "GET",
			url: "<?php echo e(route('tracker.stats.api.pageviewsbycountry')); ?>",
			data: { }
		})
		.done(function( data ) {
			renderPie(data);
		});

		function renderPie(data)
		{
			var chartData = [];

			var data = JSON.parse(data);

			chartData.push(['Country', 'Requests']);

			for (var key in data)
			{
				chartData.push([data[key].label + '\ (' + data[key].value + ')', Number(data[key].value)]);
			}

			var data = google.visualization.arrayToDataTable(chartData);

			var options = {
				title: '',
				is3D: true,
				sliceVisibilityThreshold: 0
			};

			var chart = new google.visualization.PieChart(document.getElementById('pageViewsByCountry'));

			chart.draw(data, options);
		}
	}
</script>
<?php /**PATH /home/ploi/insane.bet/vendor/pragmarx/tracker/src/views/_summaryPiechart.blade.php ENDPATH**/ ?>