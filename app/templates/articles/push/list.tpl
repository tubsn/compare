<main>

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<?php if ($info): ?>
<p><?=$info?></p>
<?php endif; ?>


<p class="light-box" style="margin-bottom:2em;">
Anzahl Pushmeldungen: <b><?=count($notifications)?></b>
&emsp; ⌀-Klickrate: <b class="conversions"><?=round(array_sum(array_column($notifications, 'clickrate'))/ count($notifications),1)?> %</b>
&emsp; im Schnitt erreichte Leser: <b class="blue"><?=gnum(round(array_sum(array_column($notifications, 'delivered'))/ count($notifications)))?></b>
&emsp; Maximal erreichte Leser: <b class="deepblue"><?=gnum(max(array_column($notifications, 'delivered')))?></b>
&emsp; ⌀-Abmeldungen (OptOuts): <b class="redish"><?=gnum(round(array_sum(array_column($notifications, 'optOuts'))/ count($notifications)))?></b>
&emsp; höchste OptOuts: <b class="redish"><?=gnum(max(array_column($notifications, 'optOuts')))?></b>
</p>

<?php include tpl('articles/push/push-table'); ?>


<?php if ($today): ?>
<a class="button" href="/push/archiv">zum Pushmeldungs-Archiv</a>
<style>
.calendar-timeframe, .calendar-datepicker {display:none;}
</style>
<?php endif ?>

</main>
