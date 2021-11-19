<div id="Chart-<?=$id?>" class="mb"></div>

<script>
let ChartOptions<?=$id?> = {
	series: [
		<?php if (is_array($metric)): ?>
		<?php foreach ($metric as $index => $currentMetric): ?>
		{
			name: '<?php if (is_array($name)): ?><?=$name[$index]?><?php else: ?><?=$name?><?php endif; ?>',
			<?php if (is_array($color)): ?>color: '<?=$color[$index]?>',<?php endif; ?>
			data: [<?=$currentMetric?>],
		},
		<?php endforeach; ?>
		<?php else: ?>
		{
			name: '<?=$name?>', color: '<?=$color?>',
			data: [<?=$metric?>],
		},
		<?php endif; ?>
	],
	chart: {
		type: 'bar',
		toolbar: {show:false},
		height: <?=$height ?? 300?>,
		<?php if (isset($stacked) && $stacked == true): ?>
		stacked: true,
		<?php else: ?>
		stacked: false,
		<?php endif; ?>
	},
	<?php if (is_array($metric) && !is_array($color)): ?>
	theme: {
		monochrome: {
			enabled: true,
			color: '<?=$color?>',
			shadeTo: 'light',
			shadeIntensity: 0.7
		}
	},
	<?php endif; ?>
	tooltip: {
		enabled: <?php if (isset($showValues)): ?>false<?php else: ?>true<?php endif; ?>,
	},

	grid: {
		show: true,
	},

	plotOptions: {
		bar: {
			borderRadius: 0,
			horizontal: false,
            //columnWidth: '25%',
		}
	},
	<?php if (is_array($metric)): ?>
    stroke: {
    	show: true,
    	width: 1,
    	colors: ['#fff']
    },
	<?php endif; ?>
	<?php if (isset($legend)): ?>
	legend: {position: '<?=$legend?>'},
	<?php else: ?>
	legend: {show:false,},
	<?php endif; ?>
	dataLabels: {
		textAnchor: 'middle',
		enabled: <?=$showValues ?? 'false'?>,
  		offsetX: 0,
  		offsetY: 20,
		style: {
		  fontSize: '18px',
		  fontFamily: 'fira sans condensed, sans-serif',
		  colors: ['#fff']
		},
		background: {
			enabled: true,
			foreColor: '#444',
			padding: 4,
			borderRadius: 5,
			borderWidth: 0,
			opacity: 0.5,
		},

	},
	grid: {row: {colors: ['#e5e5e5', 'transparent'], opacity: 0.2}},
	xaxis: {
		categories: [<?=$dimension?>],
		labels: {
			style: {
				<?php if (isset($xfont)): ?>
				fontSize: '<?=$xfont?>',
				<?php else: ?>
				fontSize: '16px',
				<?php endif; ?>
				fontFamily: 'fira sans, sans-serif',
				fontWeight: 400,
			},
		},
	}
};

let Chart<?=$id?> = new ApexCharts(document.querySelector("#Chart-<?=$id?>"), ChartOptions<?=$id?>);
Chart<?=$id?>.render();
</script>
