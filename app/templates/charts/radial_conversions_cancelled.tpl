<div id="conversionIndexChart" ></div>

<?php

$percentActive = 1;
$percentCancelled = 1;
$cancel = 0;
$active = 0;
if ($article['conversions'] > 0) {
	$cancel = count($cancelled);
	$active = $article['conversions'] - $cancel;
	$percentCancelled = round($cancel / $article['conversions'] * 100);
	$percentActive = 100 - $percentCancelled;
}
?>

<script>
let conversionIndexChartOptions = {
	series: [<?=$percentActive?>, <?=$percentCancelled?>],
	labels: ['aktiv', 'gekündigt'],
	chart: {type: 'radialBar', width:800,},
	colors:['#df886d', '#aa4747'],

	plotOptions: {
		radialBar: {
			offsetY: -290,
			startAngle: 90,
			endAngle: -90,
			hollow: {
				margin: 10,
				size: '30%',
			},
			track: {
				strokeWidth: '90%',
				margin: 5, // margin is in pixels
			},

			dataLabels: {
				name: {fontSize: '50px', offsetY: -15},
				value: {fontSize: '70px', offsetY: 50, color:'#df886d',
					formatter: function (value) {
						if (value == '<?=$percentActive?>') {return <?=$active?>;}
						return <?=$cancel?>
					}
				},

				total: {
					show: true,
					color: '#333',
					label: 'Conversions',
					fontWeight: 600,
					formatter: function () {
						return '<?=$article['conversions']?>'
					}
				}
		    }
		}
	},
};

let conversionIndexChart = new ApexCharts(document.querySelector("#conversionIndexChart"), conversionIndexChartOptions);
conversionIndexChart.render();

</script>
