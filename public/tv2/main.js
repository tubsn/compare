



class dashboardManager {

	constructor() {

		this.statsYesterday();
		this.statsToday();
		this.ordersToday();
		this.articlesToday();
		this.articlesToday(true);
		//this.activeUsers();
		this.currentUsers = 0;

		this.portalConversionsTodayLR();
		this.portalConversionsTodayMOZ();

		//this.initLiveChartChart();
		//this.initLiveSubsChartChart();

		//this.autoUpdates();

	}

	autoUpdates() {

		const minute = 1000*60;
		const _this = this;
		setInterval(function() { _this.activeUsers(); }, 3 * minute);
		setInterval(function() { _this.articlesToday(); }, 10 * minute);
		setInterval(function() { _this.articlesToday(true); }, 10 * minute);
		setInterval(function() { _this.statsToday(); }, 10 * minute);
		setInterval(function() { _this.ordersToday(); }, 10 * minute);
		
		setInterval(function() { _this.portalConversionsTodayLR(); }, 10 * minute);
		setInterval(function() { _this.portalConversionsTodayMOZ(); }, 10 * minute);
		//setInterval(function() { _this.portalConversionsTodaySWP(); }, 10 * minute);

	}

	initLiveChartChart() {
		let liveClickDevelopmentElement = document.querySelector('.js-diagram');
		if (!liveClickDevelopmentElement) {return;}
		this.liveClickChart = new ApexCharts(liveClickDevelopmentElement, LineChartTodayOptions);
		this.liveClickChart.render();
	}

	initLiveSubsChartChart() {
		let subscriberChartElement = document.querySelector('.js-diagram-subs');
		if (!subscriberChartElement) {return;}
		this.subscriberChart = new ApexCharts(subscriberChartElement, LineChartTodayOptions);
		this.subscriberChart.render();
	}

	animateNumber (obj, start, end, duration) {

		  let startTimestamp = null;
		  const step = (timestamp) => {
		    if (!startTimestamp) startTimestamp = timestamp;
		    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
		    obj.innerHTML = Math.floor(progress * (end - start) + start);
		    if (progress < 1) {
		      window.requestAnimationFrame(step);
		    }
		  };
		  window.requestAnimationFrame(step);

	}

	portalConversionsTodayLR() {

		this.fetchURL('https://reports.lr-digital.de/api/orders-today').then(orders => {

			let orderCount = orders.length;
			let countContainer = document.querySelector('.js-today-portal-conversions-lr');
			countContainer.innerHTML = orderCount;

		});

	}

	portalConversionsTodayMOZ() {

		this.fetchURL('https://reports-moz.lr-digital.de/api/orders-today').then(orders => {

			let orderCount = orders.length;
			let countContainer = document.querySelector('.js-today-portal-conversions-moz');
			countContainer.innerHTML = orderCount;

		});

	}

	portalConversionsTodaySWP() {

		this.fetchURL('https://reports-swp.lr-digital.de/api/orders-today').then(orders => {

			let orderCount = orders.length;
			let countContainer = document.querySelector('.js-today-portal-conversions-swp');
			countContainer.innerHTML = orderCount;

		});

	}	


	ordersToday() {

		this.fetchURL('/api/orders-today').then(orders => {

			let orderCount = orders.length;
			let countContainer = document.querySelector('.js-orders-today');
			countContainer.innerHTML = orderCount;

		});

	}

	activeUsers() {

		let countContainer = document.querySelector('.js-active-users');
		this.fetchURL('/api/active-users').then(users => {

			let count = users.users;
			this.animateNumber(countContainer, this.currentUsers, count, 700);
			this.currentUsers = count;

		});

	}


	articlesToday(moz = false) {

		let apiPath = '/api/articles-today';
		let selector = '.js-articles-today'
		if (moz) {
			apiPath = 'https://reports-moz.lr-digital.de/api/articles-today';
			selector = '.js-articles-today-moz'
		}


		this.fetchURL(apiPath).then(articles => {

			let content = '';
			let counter = 0;

			articles.forEach((article) => {

				if (counter >= 4) {return;}

				let kicker = '';
				let headline = '';

				if (article.title.includes(':')) {
					kicker = article.title.split(':')[0];
					headline = article.title.split(':')[1];
				}

				else {headline = article.title;}

				if (headline.includes('/ Lausitzer Rundschau')) {
					headline = headline.substr(0,headline.length - 22);
				}

				if (headline.includes('/ MMH')) {
					headline = headline.substr(0,headline.length - 6);
				}

				headline = headline.substring(0, 32) + '...';
				
				if (kicker.length > 27) {
					kicker = kicker.substring(0, 22) + '...';
				}

				content += `<article>
					<figure class="thumbnail">
						<a target="_blank" href="https://www.lr-online.de/${article.id}"><img width="160" src="${article.image}"></a>
					</figure>
					<div>
						<h1><a target="_blank" href="https://www.lr-online.de/${article.id}">${headline}<span> - ${kicker}<span></a></h1>
						<ul class="vertical nostyle">
							<!--<li><span class="blue">${this.ucfirst(article.ressort)}</span></li>-->
							<li>Subs: &nbsp;<span class="blue">${article.subscriberviews}</span></li>
							<li>MT: &nbsp;<span class="blue">${article.avgmediatime}</span>&thinsp;s</li>
							<li>Conv: &nbsp;<span class="blue">${(article.conversions != 0) ? article.conversions : '-'}</span></li>
						</ul>
					</div>
					<div class="stats"><span>${this.numberFormat(article.pageviews)}</span> <small>PV</small>
					</div>
				</article>`;

				counter++;

			});

			let container = document.querySelector(selector);
			container.innerHTML = content

		});




	}

	ucfirst(string) {
		return string.charAt(0).toUpperCase() + string.slice(1);
	}

	statsToday() {

		this.fetchURL('https://reports.lr-digital.de/api/stats-today/6').then(stats => {

			let pageviews = document.querySelector('.js-pageviews-today');
			pageviews.innerHTML = this.numberFormat(stats.users.pageviews);

			let subscribers = document.querySelector('.js-subscribers-today');
			subscribers.innerHTML = this.numberFormat(stats.subs.subscriberviews);

		});

		this.fetchURL('https://reports-moz.lr-digital.de/api/stats-today/6').then(stats => {

			let pageviews = document.querySelector('.js-pageviews-today-moz');
			pageviews.innerHTML = this.numberFormat(stats.users.pageviews);

			let subscribers = document.querySelector('.js-subscribers-today-moz');
			subscribers.innerHTML = this.numberFormat(stats.subs.subscriberviews);

		});

	}

	livediagram (metric, dimension) {

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
			name: 'Pageviews',
			color: '#44c2ff',
			data: output
		}];

		return chartData;

	}



	statsYesterday() {

		this.fetchURL('https://reports.lr-digital.de/api/yesterday').then(stats => {

			let pageviews = document.querySelector('.js-yesterday-pageviews');
			let subscribers = document.querySelector('.js-yesterday-subscribers');
			let orders = document.querySelector('.js-yesterday-orders');
			pageviews.innerHTML = this.numberFormat(stats.pageviews);
			subscribers.innerHTML = this.numberFormat(stats.subscriberviews);
			orders.innerHTML = this.numberFormat(stats.orders);

		});

		this.fetchURL('https://reports-moz.lr-digital.de/api/yesterday').then(stats => {

			let pageviews = document.querySelector('.js-yesterday-pageviews-moz');
			let subscribers = document.querySelector('.js-yesterday-subscribers-moz');
			let orders = document.querySelector('.js-yesterday-orders-moz');
			pageviews.innerHTML = this.numberFormat(stats.pageviews);
			subscribers.innerHTML = this.numberFormat(stats.subscriberviews);
			orders.innerHTML = this.numberFormat(stats.orders);

		});

	}

	async fetchURL(url) {
		const response = await fetch(url);
		const data = await response.json();
		return data;
	}

	numberFormat (number) {
		return new Intl.NumberFormat('de-DE').format(number);
	}


}


class BMClock {

	constructor() {
		this.clock = document.querySelector('.js-clock');
		if (this.clock) {this.update();}
	}

	update() {
		let time = new Date().toLocaleTimeString();
		time = time.split(':');
		time = time.join('</span>:<span>');
		time = '<span>' + time + '</span>';
		this.clock.innerHTML = time;
		setTimeout(() => this.update(), 1000);
	}

}


class compareChartManager {

	constructor(chartID, chartOptions) {
		this.chartID = chartID;
		this.options = chartOptions;
		this.chart = this.init();

		this.cutleft = 0;
		this.cutright = 0;

	}

	init() {
		this.chartElement = document.querySelector(this.chartID);
		let aphexChart = new ApexCharts(this.chartElement, this.options);
		aphexChart.render();
		return aphexChart
	}

	loadData(url) {

		fetch(url, {method: 'GET', credentials: 'same-origin',})
		.then(response => {
			if(response.status !== 404) {return response.json();}
			console.log(response.status);

		})
		.then(response => {

			let seriesData = [];
			this.seriesConfig.forEach((set) => {
				seriesData.push(
					{
					 name: set.name,
					 color: set.color,
					 data: this.transform(response, set.portal, set.type, set.endpoint)
					},
				)
			});
			this.chart.updateSeries(seriesData);
		})
		.catch(error => {
			this.chart.updateOptions({noData: {text: 'API Connection failed... IP not Allowed'}})
			console.error(`Oops: ${error}`);
		});

	}

	errorBox(message) {

		let box = document.createElement('div');
		box.innerHTML = message;
		return box;

	}

	transform(apiData, portal, type, endpoint) {

		let data = apiData[type][portal];
		let output = [];

		data = Object.entries(data);

		if (this.cutleft > 0) {
			data = data.slice(this.cutleft);
		}

		if (this.cutright > 0) {
			data = data.slice(0,this.cutright*-1);
		}

		data.forEach(([date, content]) => {
			output.push ({
				'x': date,
				'y': content[endpoint]
			});
		});

		return output;

	}


}

//When the DOM is fully loaded - aka Document Ready
document.addEventListener("DOMContentLoaded", function(){

	const OrdersChart = new compareChartManager('#ChartOrders', LineChartOptions);
	OrdersChart.seriesConfig = [
		{name: 'LR', color: '#f7bc89', portal: 'LR' , type: 'orders', endpoint: 'orders'},
		{name: 'MOZ', color: '#5f94e6', portal: 'MOZ' , type: 'orders', endpoint: 'orders'},
		//{name: 'SWP', color: '#ff6464', portal: 'SWP' , type: 'orders', endpoint: 'orders'},
	]
	OrdersChart.cutright = 0;
	OrdersChart.loadData('/api/portals');


	const GeisterChart = new compareChartManager('#ChartGeister', LineChartOptions);
	GeisterChart.seriesConfig = [
		{name: 'LR', color: '#f7bc89', portal: 'LR' , type: 'kpis', endpoint: 'quoteGeister'},
		{name: 'MOZ', color: '#5f94e6', portal: 'MOZ' , type: 'kpis', endpoint: 'quoteGeister'},
		//{name: 'SWP', color: '#ff6464', portal: 'SWP' , type: 'kpis', endpoint: 'quoteGeister'},
	]
	GeisterChart.cutleft = 7;
	GeisterChart.loadData('/api/portals');



	const SubscriberChart = new compareChartManager('#ChartSubscribers', LineChartOptions);
	SubscriberChart.seriesConfig = [
		{name: 'LR', color: '#f7bc89', portal: 'LR' , type: 'kpis', endpoint: 'subscriberviews'},
		{name: 'MOZ', color: '#5f94e6', portal: 'MOZ' , type: 'kpis', endpoint: 'subscriberviews'},
		//{name: 'SWP', color: '#ff6464', portal: 'SWP' , type: 'kpis', endpoint: 'subscriberviews'},
	]
	SubscriberChart.cutleft = 1;
	SubscriberChart.loadData('/api/portals');


	/*
	const SubscriberChart = new compareChartManager('#ChartSubscribers', ChartSubscriberOptions);
	SubscriberChart.seriesConfig = [
		{name: 'Subscribers', color: '#2a8efe', endpoint: 'subscribers'},
		{name: 'Pageviews', color: '#2acdfe', endpoint: 'pageviews'}
	]
	SubscriberChart.loadData('https://reports.lr-digital.de/api/kpis');
	*/

	const BookmarkClock = new BMClock();
	const dmb = new dashboardManager();

});
