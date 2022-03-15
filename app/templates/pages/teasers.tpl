<script src="https://unpkg.com/vue@3"></script>

<script>
document.addEventListener("DOMContentLoaded", function(){

Vue.createApp({
	data() {
		return {
			positions: '',
			date: '<?=$date?>',
			hour : 12
		}
	},

	created() {
		fetch("/api/teaser/"+this.date+"/" + this.hour)
		.then(response => response.json())
		.then(positions => (this.positions = positions));
	},

	methods: {
		changeTime() {
			fetch("/api/teaser/"+this.date+"/" + this.hour)
			.then(response => response.json())
			.then(positions => (this.positions = positions));

			let url = '/teasers/' + this.date;

			window.history.pushState({}, '', url)

		},
	}


}).mount('#app')

});
</script>




<main id="app" class="teaser-list">


<div class="calendar-container">

	<div class="calendar-picker">

		<form method="post" action="">
			<fieldset class="calendar-datepicker">
				Tag wählen: <input @change="changeTime" v-model="date" type="date" name="date">
			</fieldset>
		</form>
	</div>
</div>


<div class="range-slider-box">
	<h3 class="text-center">Betrachtungszeitraum anpassen: {{ hour }} Uhr</h3>
	<input v-model="hour" @input="changeTime" class="range-slider" type="range" min="0" max="23" name="fontsize"/>
</div>

<h1 class="text-center">Teaser Performance für {{ date }}</h1>

<div class="text-center" style="margin-bottom:4em"><b>Hinweis:</b> Aktuell stehen nur Daten vom 16 - 21.02.2022 zur Verfügung. <br>Es werden nur Artikel angezeigt, wenn dieser mindestens 120 mal eingeblendet wurde. <br>Links befinden sich die <b>Klickdaten in dieser Stunde</b> - Rechts die Gesamtklicks des Artikels.</div>


<h1 class="text-center" v-if="!positions">Sorry für diesen Zeitraum stehen keine Daten zur Verfügung</h1>


<section class="teaser-section" v-for="(position,index) in positions">
	<h3>Teaser-Position:&nbsp;{{ index }}</h3>

	<article class="teaser-article" v-for="article in position" :class="article.CTR > 0.20 ? 'performer' : 'neutral'">

		<img :src="article.image || '/styles/img/flundr/no-thumb.svg'">
		<h1>{{ article.title || 'Keine Daten verfügbar' }}</h1>
		<div v-if="article.length != 0">

			<div class="teaser-ressort">{{ article.ressort }}</div>

			<table class="blank">
				<tr>
					<td>Gesehen: <b>{{ article.exposures }}</b></td>
					<td>Subs: {{ article.subscribers }}</td>
				</tr>
				<tr>
					<td>Geklickt: <b>{{ article.clicks }}</b></td>
					<td>PV: {{ article.pageviews }}</td>
				</tr>
				<tr>
					<td><span class="inverted">CTR: <b>{{ article.CTR }}%</b></span></td>
					<td v-if="article.conversions > 0">Conv: <span class="conversions">{{ article.conversions }}</span></td>
					<td v-else>Conv: -</td>

				</tr>
			</table>

		</div>

		<a class="block-link" :href="'/artikel/'+article.id"></a>

	</article>

</section>

</main>


<style>
.teaser-section {margin-bottom:2em; padding:0 0 2em 0; break-inside: avoid-column;
position:relative; width:100%;
display:flex; gap:1em;
border-bottom: 1px dashed black;
justify-content: center;
flex-wrap:wrap;
}

.teaser-section:last-of-type {border-bottom:none; margin-bottom:6em;}

.teaser-section h3 {position:absolute; top:-2.5em; padding: 0.3em .5em; border-radius:.3em; background:#fdf1e3;color:black;}

.teaser-article {background-color:#e8e8e8; align-self: stretch; max-width:450px; width:100%; position:relative; flex-grow:1;}
.teaser-article h1 {margin:0; padding:0 .85em; margin-bottom:0.4em; margin-top:0.3em}
.teaser-article div {padding:0 1em 1em 1em;}
.teaser-article p {margin:0;}
.teaser-article img {width:100%; max-height:250px;}

.teaser-ressort {text-transform: capitalize; position:absolute; top:0;right:0; color:white; font-size:.7em; background-color:rgba(0,0,0,0.7); padding:0 .4em !important;}


.teaser-section .performer {background-color:#c7f5c6;}
.teaser-section .negative {background-color:#f5c6c6;}

.inverted {background-color: #365679; color: #fff; padding: 0 0.4em; border-radius: 0.2em;}

table.blank {width:100%;}
table.blank td {border:0; padding:0; margin:0;}
table.blank td:last-of-type {text-align:right;}

.range-slider-box {position:fixed; bottom: 0; z-index:99999; background:white; width:90%; padding:.5em; padding-top:1em;
box-shadow: 0 -1em 1em -14px rgba(0,0,0,0.2); border-radius:1em;
}
.range-slider-box h3 {margin:0; font-size:1.1em;}

input[type=range].range-slider {width: 100%; margin: 5.5px 0; border:0; margin-bottom:1em; background-color: transparent; -webkit-appearance: none; margin-bottom:0;}
input[type=range].range-slider:focus {outline: none;}
input[type=range].range-slider::-webkit-slider-runnable-track {background: rgb(181, 181, 181); width: 100%; height: 7px; cursor: pointer; border-radius: 50px;}
input[type=range].range-slider::-webkit-slider-thumb {margin-top: -5.6px; width: 25px; height: 25px; background: #213e5e; border: 0; border-radius: 50px; cursor: pointer; -webkit-appearance: none;}
input[type=range].range-slider:focus::-webkit-slider-runnable-track {background: #45241f;}
input[type=range].range-slider::-moz-range-track {background: rgb(181, 181, 181); width: 100%; height: 7px; cursor: pointer; border-radius: 50px;}
input[type=range].range-slider::-moz-range-thumb {width: 25px; height: 25px; background: #213e5e; border: 0; border-radius: 50px; cursor: pointer;}

</style>
