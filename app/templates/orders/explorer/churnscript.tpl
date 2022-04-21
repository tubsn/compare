<script src="https://unpkg.com/vue@3"></script>
<script>

document.addEventListener("DOMContentLoaded", function(){

Vue.createApp({
	data() {
		return {
			from: '',
			to: '',
			product: '',
			segment: '',
			testgroup: '',
			ressort: '',
			type: '',
			audience: '',
			origin: '',
			source_grp: '',
			source: '',
			days: 1000,
			orders: 0,
			cancelled: 0,
			retention: 0,
		}
	},

	computed: {
		productReadable() {
			return decodeURI(this.product);
		},
		quote() {
			if (this.orders == 0) {return 0;}
			return ((this.cancelled / this.orders)*100).toFixed(2);
		}
	},

	created() {
			fetch(`/api/explorer`)
		.then(response => response.json())
		.then(data => {
			this.orders = data.orders;
			this.cancelled = data.cancelled;
			this.retention = data.retention;

			let chart = this.convert_chartdata(data.chart.cancelled_orders, data.chart.dimensions);
			ChartExplorer.updateSeries(chart);

		});
	},

	methods: {
		calculateChurn() {
			fetch(`/api/explorer?from=${this.from}&to=${this.to}&product=${this.product}&segment=${this.segment}&testgroup=${this.testgroup}&origin=${this.origin}&source_grp=${this.source_grp}&source=${this.source}&ressort=${this.ressort}&type=${this.type}&audience=${this.audience}&days=${this.days}`)
			.then(response => response.json())
			.then(data => {
				this.orders = data.orders;
				this.cancelled = data.cancelled;
				this.retention = data.retention;				


				//console.log(ChartExplorer);

				let chart = this.convert_chartdata(data.chart.cancelled_orders, data.chart.dimensions);
				ChartExplorer.updateSeries(chart);

			});
		},

		setDays(days = 30) {
			this.days = days;
			this.calculateChurn();
		},

		convert_chartdata (metric, dimension) {

			metric = metric.split(',');
			dimension = dimension.split(',');

			let output = [];
			metric.forEach(function(value, key) {

				let date = dimension[key] ?? '0';
				date = date.replaceAll("'","");

				output.push ({
					'x': date,
					'y': value
				});
			});

			let chartData =	[{
				name: 'Churns',
				color: '#c65b5b',
				data: output
			}];

			return chartData;

		},



	}


}).mount('#explorer-app')

});



</script>
