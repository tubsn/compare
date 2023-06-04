let BarChartOptions = {
	series: [

	],

	noData: {
		text: 'Loading...'
	},

	chart: {
		type: 'bar',
		foreColor: '#ddd',
		toolbar: {show:false},
		height: 300,
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
    	width: 0,
    	colors: ['#fff'],
	},

	legend: {position: 'top', theme: 'dark',},

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
		labels: {
			style: {
				fontSize: '12px',
				fontFamily: 'Bebas Neue',
			},
			rotate: 0,
		},
		tickAmount: 6,
	},

	yaxis: {tickAmount: 4,}

};
