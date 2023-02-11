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
		height: <?=$height ?? 300?>,
		<?php if (isset($area) && $area == false): ?>
		type: 'line',
		<?php else: ?>
		type: 'area',
		<?php endif; ?>
		toolbar: {show:false},
		zoom: {enabled:false},
		<?php if (isset($animation) && $animation == false): ?>
		animations: {enabled: false},
		<?php else: ?>
		animations: {enabled: true},
		<?php endif; ?>
		sparkline: {enabled: false},
		<?php if (isset($stacked) && $stacked == true): ?>
		stacked: true,
		<?php if (isset($stackedTo100) && $stackedTo100 == true): ?>
		stackType: '100%',
		<?php endif; ?>
		<?php else: ?>
		stacked: false,
		<?php endif; ?>
	},

<?php if (PORTAL == 'LR'): ?>
  annotations: {
    points: [
      {
        x: '2022-12-08',
        y: 20,
        label: {
          text: "ho ho ho",
          offsetY: -25,
          offsetX: 20
        },
    marker: {
      size: 0,
    },        
        image: {
          path:
            "/styles/img/santaride.png",
          width: 37,
          height: 42,
          offsetX: 0,
          offsetY: -0
        }
      },
    ]
  },
<?php endif; ?>

<?php if (PORTAL == 'MOZ'): ?>
  annotations: {
    points: [
      {
        x: '2022-12-06',
        y: 46,
        label: {
          text: "ho ho ho",
          offsetY: -25,
          offsetX: 20
        },
    marker: {
      size: 0,
    },        
        image: {
          path:
            "/styles/img/santaride.png",
          width: 37,
          height: 42,
          offsetX: 10,
          offsetY: -0
        }
      },
    ]
  },
<?php endif; ?>


<?php if (PORTAL == 'SWP'): ?>
  annotations: {
    points: [
      {
        x: '2022-12-12',
        y: 142,
        label: {
          text: "ho ho ho",
          offsetY: -25,
          offsetX: 20
        },
    marker: {
      size: 0,
    },        
        image: {
          path:
            "/styles/img/santaride.png",
          width: 37,
          height: 42,
          offsetX: 10,
          offsetY: -0
        }
      },
    ]
  },
<?php endif; ?>


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
	<?php if (isset($legend)): ?>
	legend: {
	show:true,
	position: '<?=$legend?>',
	fontSize: '20px', 
	fontFamily: 'fira sans condensed, sans-serif',	
	horizontalAlign: 'center',
	onItemHover: {highlightDataSeries: true},
	floating: true,
	offsetY: 0,
	offsetX: 0,
	},
	<?php else: ?>
	legend: {show:false,},
	<?php endif; ?>

	stroke: {curve: 'smooth'},
	dataLabels: {enabled: false,},

	xaxis: {
		categories: [<?=$dimension?>],
		crosshairs: {show: true},
		tooltip: {enabled: false},
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

	states: {active: {allowMultipleDataPointsSelection: false}},
	grid: {row: {colors: ['#e5e5e5', 'transparent'], opacity: 0.2}, xaxis: {lines: {show: false}}},

	tooltip: {
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
}

let Chart<?=$id?> = new ApexCharts(document.querySelector("#Chart-<?=$id?>"), ChartOptions<?=$id?>);
Chart<?=$id?>.render();

</script>
