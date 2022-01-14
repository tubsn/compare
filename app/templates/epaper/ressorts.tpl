<main class="">

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<p><a href="/epaper/ressorts">Ressortliste</a> | <a href="/epaper">Artikelliste</a></p>

<!--
<p class="light-box" style="margin-bottom:2em;">
Gesamtbestellungen: <b class="conversions"><?=$numberOfOrders?></b>
&emsp; davon Plusseite: <b class="blue"><?=$plusOnly?></b>
&emsp; davon Aboshop: <b class="blue"><?=$aboshopOnly?></b>
&emsp; davon Gekündigt: <b class="redish"><?=$numberOfCancelled?></b>
&emsp; Kündigerquote: <b class="orange"><?=round(($numberOfCancelled / $numberOfOrders) * 100)?>&thinsp;%</b>
&emsp; ⌀-Haltedauer: <b class="blue"><?=number_format($averageRetention,2,',','.')?> Tage</b>
</p>-->

<?php foreach ($ressorts as $ressort => $pageviews): ?>
	<p><a href="/epaper/ressort/<?=$ressort?>"><?=$ressort?></a>: <?=$pageviews?></p>
<?php endforeach ?>


</main>
