<main>

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<?php if ($info): ?>
<p><?=$info?></p>
<?php endif; ?>


<p class="light-box" style="margin-bottom:2em;">
<?php if ($today): ?>
aktive Nutzer: <b><?=gnum($stats['subscriptionsEndDate'] - $stats['inactiveSubscriptionsEndDate'])?></b> &emsp;
<?php endif; ?>
Pushmeldungen: <b><?=count($notifications)?></b>
&emsp; ⌀-Klickrate: <b class="conversions"><?=round(array_sum(array_column($notifications, 'clickrate'))/ count($notifications),1)?> %</b>
&emsp; ⌀-erreichte Leser: <b class="blue"><?=gnum(round(array_sum(array_column($notifications, 'delivered'))/ count($notifications)))?></b>
&emsp; Maximal erreichte Leser: <b class="deepblue"><?=gnum(max(array_column($notifications, 'delivered')))?></b>
&emsp; ⌀-Abmeldungen (OptOuts): <b class="redish"><?=gnum(round(array_sum(array_column($notifications, 'optOuts'))/ count($notifications)))?></b>
&emsp; höchste OptOuts: <b class="redish"><?=gnum(max(array_column($notifications, 'optOuts')))?></b>
</p>

<?php include tpl('articles/push/push-table'); ?>


<?php if ($today): ?>
<p class="text-center" style="margin-top:2em; margin-bottom:2em;">
	<b>Aktueller Monat: </b>&emsp;
	Versand: <b><?=gnum($stats['sent'])?></b> &emsp;|&emsp;
	Zugestellt: <b><?=gnum($stats['delivered'])?></b> &emsp;|&emsp;
	Klicks: <b><?=gnum($stats['clicked'])?></b> &emsp;|&emsp;
	Neu-Anmeldungen: <b><?=gnum($stats['optIns'])?></b> &emsp;|&emsp;
	Abmeldungen: <b><?=gnum($stats['optOuts'])?></b>
</p>

<p class="text-center">
<a class="button" href="/push/archiv">zum Pushmeldungs-Archiv</a>
</p>
<style>
.calendar-timeframe, .calendar-datepicker {display:none;}
</style>
<?php endif ?>



</main>
