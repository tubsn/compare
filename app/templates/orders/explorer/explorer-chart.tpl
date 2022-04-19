<div id="Chart-Explorer"></div>

<script>
let ChartOptionsExplorer = {
	series: [
		{
			name: 'Tage', color: '#c65b5b',
			data: ['100','5'],
		},
	],
	chart: {
		type: 'bar',
		toolbar: {show:false},
		height: 650,
		//width: 500,
		animations: {enabled: true},
		stacked: false,
		//stackType: '100%',
	},
	tooltip: {
		enabled: true,
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
	legend: {show:false,},

	grid: {row: {colors: ['#e5e5e5', 'transparent'], opacity: 0.2}},
	dataLabels: {enabled: false},


	xaxis: {
		//categories: ['Tage'],
		tickAmount: 5,
		labels: {
			style: {
				fontSize: '16px',
				fontFamily: 'fira sans, sans-serif',
				fontWeight: 400,
			},

			formatter: function (value) {
				return 'Tag ' + value ;
			},
			rotate: 0
		},
	},

	yaxis: {
		tickAmount: 6,
		labels: {rotate: 0},
	}


};

let ChartExplorer = new ApexCharts(document.querySelector("#Chart-Explorer"), ChartOptionsExplorer);
ChartExplorer.render();
</script>
