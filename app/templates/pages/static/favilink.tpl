<?php
$portal = 'lr-online';
switch (PORTAL) {
	case 'LR': $portal = 'lr-online'; $page = 'lr.de'; break;
	case 'MOZ': $portal = 'moz'; $page = 'moz.de'; break;
	case 'SWP': $portal = 'swp'; $page = 'swp.de'; break;
}
?>

<main>
<h2>Lesezeichen für Artikelauswertungen:</h2>

<p>Zum Installieren des Lesezeichens einfach den Button/Link unten auf Ihre Lesezeichensymbolleiste im Browser (oben) ziehen.
<br />Dann einen <?=$page?> Artikel öffnen und auf das Lesezeichen klicken.</p>

<!--

<script>

javascript:(function(){

	let portals = ['lr-online.de','moz.de','swp.de'];
	let targetURL = 'https://reports.lr-digital.de';
	let artID = parseInt(location.pathname.substr(-13, 8));

	for (let portal of portals) {
		if (location.hostname.indexOf(portal) >= 0) {
			if (portal == 'moz.de') {targetURL = 'https://reports-moz.lr-digital.de';}
			if (portal == 'swp.de') {targetURL = 'https://reports-swp.lr-digital.de';}
			if (artID) {targetURL = targetURL + '/artikel/' + artID;}
		}
	}
	window.location = targetURL;

})();

</script>

-->

<p>
<a class="button" href="javascript:!function(){let t='https://reports.lr-digital.de',e=parseInt(location.pathname.substr(-13,8));for(let o of['lr-online.de','moz.de','swp.de'])location.hostname.indexOf(o)>=0&&('moz.de'==o&&(t='https://reports-moz.lr-digital.de'),'swp.de'==o&&(t='https://reports-swp.lr-digital.de'),e&&(t=t+'/artikel/'+e));window.location=t}();">Artikel-Klickzahlen</a>&emsp;<span class="cancelled"><---- diesen Button hochschieben!!!</span>
</p>

<hr />

<h3>Neu: Lesezeichen für Livestats als Layer auf der Webseite:</h3>
<a class="button" href="javascript:(function(){document.body.appendChild(document.createElement('script')).src='//static.lr-digital.de/styles/js/hitmarklet.js';})();">Realtime-Stats</a>

<br><br>
<hr />

<h3>Auch Neu: Pushcode für Artikel Direktverlinkung in die App vorbereiten:</h3>
<a class="button" href="javascript:(function(){let artID = parseInt(location.pathname.substr(-13, 8));let path = 'lrapp://online/article/';window.prompt('ArtikelPushCode', path + artID);})();">Pushcode für Artikel</a>



<br>
<br><br>

<h3>Tutorialvideo zur Installation</h3>
<a href="javascript:!function(){let t='https://reports.lr-digital.de',e=parseInt(location.pathname.substr(-13,8));for(let o of['lr-online.de','moz.de','swp.de'])location.hostname.indexOf(o)>=0&&('moz.de'==o&&(t='https://reports-moz.lr-digital.de'),'swp.de'==o&&(t='https://reports-swp.lr-digital.de'),e&&(t=t+'/artikel/'+e));window.location=t}();">
<img src="/styles/img/faviconani.gif"></a>


</main>
