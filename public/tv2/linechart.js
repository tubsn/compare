let LineChartOptions = {
	series: [

	],

	noData: {
		text: 'Loading...'
	},

	chart: {
		type: 'line',
		foreColor: '#ddd',
		toolbar: {show:false},
		height: 200,
		sparkline: {enabled: false},
		animations: {enabled: true},
		stacked: false,
	},

	tooltip: {
			enabled: true,
			theme: 'dark',
	},

	grid: {

		show: true,
		row: {colors: ['#44c2ff', 'transparent'], opacity: 0.03},
		borderColor: '#000',

	},


	plotOptions: {
		bar: {
			borderRadius: 0,
			horizontal: false,
		}
	},

    stroke: {
    	show: true,
    	curve: 'smooth',
    	width: 2,
    	colors: ['#fff'],
	},

	legend: {position: 'top', theme: 'dark', },

	dataLabels: {
		textAnchor: 'middle',
				enabled: false,
		  		offsetX: 0,
  		offsetY: 20,
		style: {
		  fontSize: '18px',
		  fontFamily: 'Bebas Neue',
		  colors: ['#fff']
		},

		background: {
			enabled: true,
			foreColor: '#fff',
			padding: 4,
			borderRadius: 5,
			borderWidth: 0,
			opacity: 0.5,
		},

	},

	xaxis: {
		tooltip: {enabled: false},
		labels: {
			style: {
				fontSize: '12px',
				fontFamily: 'Bebas Neue',
			},
			rotate: 0,
		},
		tickAmount: 6,

		axisBorder: {
			show: false,
			color: '#44c2ff',
			height: 1,
			width: '100%',
			offsetX: 0,
			offsetY: 0
		},

		axisTicks: {
			show: false,
			borderType: 'solid',
			color: '#78909C',
			height: 6,
			offsetX: 0,
			offsetY: 0
		},


	},

	yaxis: {
		tickAmount: 4,
		labels: {
			style: {
				fontSize: '14px',
				fontFamily: 'Bebas Neue',

			},
			rotate: 0,
		},
	}

};
