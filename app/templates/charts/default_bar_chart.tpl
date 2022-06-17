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
		<?php if (isset($animation) && $animation == false): ?>
		animations: {enabled: false},
		<?php else: ?>
		animations: {enabled: true},
		<?php endif; ?>
		<?php if (isset($stacked) && $stacked == true): ?>
		stacked: true,
		<?php if (isset($stackedTo100) && $stackedTo100 == true): ?>
		stackType: '100%',
		<?php endif; ?>
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
		<?php if (isset($showValues) && $showValues == true): ?>
		enabled: false,
		<?php else: ?>
		enabled: true,
		<?php endif; ?>
		<?php if (isset($percent) && $percent == true): ?>
        y: {
          formatter: function(value) {
              return value + '&thinsp;%'
          },
        },
		<?php endif; ?>
		<?php if (isset($seconds) && $seconds == true): ?>
        y: {
          formatter: function(value) {
              return value + '&thinsp;s'
          },
        },
		<?php endif; ?>
	},

	grid: {
		show: true,
	},

	plotOptions: {
		bar: {
			borderRadius: 0,
			horizontal: false,
      //columnWidth: '70%',
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
	legend: {position: '<?=$legend?>', fontSize: '20px', fontFamily: 'fira sans condensed, sans-serif',},
	<?php else: ?>
	legend: {show:false,},
	<?php endif; ?>
	dataLabels: {
		textAnchor: 'middle',
		<?php if (isset($showValues) && $showValues == false): ?>
		enabled: false,
		<?php else: ?>
		enabled: true,
		<?php endif; ?>
  		offsetX: 0,
  		offsetY: 20,
		style: {
		  fontSize: '16px',
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
	states: {active: {allowMultipleDataPointsSelection: false}},
	grid: {row: {colors: ['#e5e5e5', 'transparent'], opacity: 0.2}},
	xaxis: {
		categories: [<?=$dimension?>],
		<?php if (isset($tickamount)): ?>tickAmount: <?=$tickamount?>,<?php endif; ?>
		labels: {
			style: {
				<?php if (isset($xfont)): ?>
				fontSize: '<?=$xfont?>',
				<?php else: ?>
				fontSize: '13px',
				<?php endif; ?>
				fontFamily: 'fira sans, sans-serif',
				fontWeight: 400,
			},
			<?php if (isset($prefix)): ?>
			formatter: function (value) {
				return '<?=$prefix?>' + value;
			},
			<?php endif; ?>
			<?php if (isset($suffix)): ?>
			formatter: function (value) {
				return value + '<?=$suffix?>';
			},
			<?php endif; ?>
		},
	},

	yaxis: {
		tickAmount: 4,
		labels: {rotate: 0},
		<?php if (isset($ymax)): ?>max: <?=$ymax?>,<?php endif; ?>
		<?php if (isset($ymin)): ?>min: <?=$ymin?>,<?php endif; ?>		
	},

};

let Chart<?=$id?> = new ApexCharts(document.querySelector("#Chart-<?=$id?>"), ChartOptions<?=$id?>);
Chart<?=$id?>.render();
</script>
