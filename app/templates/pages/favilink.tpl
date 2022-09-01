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

<p>
<a class="button" href="javascript:(function(){let artID = parseInt(location.pathname.substr(-13, 8)); if (location.hostname.indexOf('<?=$portal?>') >= 0 && artID) {
let newLocation = '<?=PAGEURL?>/artikel/'+artID;window.location = newLocation;}else{window.location = '<?=PAGEURL?>';}})();">Artikel-Klickzahlen</a>&emsp;<span class="cancelled"><---- diesen Button hochschieben!!!</span>
</p>

<br>

<hr>

<br>
<h3>Tutorialvideo zur Installation</h3>
<a href="javascript:(function(){let artID = parseInt(location.pathname.substr(-13, 8)); if (location.hostname.indexOf('<?=$portal?>') >= 0 && artID) {
let newLocation = '<?=PAGEURL?>/artikel/'+artID;window.location = newLocation;}else{window.location = '<?=PAGEURL?>';}})();">
<img src="/styles/img/faviconani.gif"></a>



</main>
