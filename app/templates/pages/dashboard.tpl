<main>

<?php include tpl('navigation/date-picker');?>

<h1>Gesamt Conversions: <span class="conversions"><?=$numberOfOrders?></span></h1>
<hr>


<p class="light-box" style="margin-bottom:2em; margin-top:1em; width:100%; text-align:center; box-sizing:border-box">
Produzierte Artikel: <b><?=$articles?></b>
&emsp; Subscriber Views: <b class="blue"><?=number_format($subscribers,0,',','.')?></b>
&emsp; Conversions Plusseite: <b class="blue"><?=$plusOnly?></b>
&emsp; Conversions Extern: <b class="blue"><?=$externalOnly?></b>
&emsp; Gekündigt: <b class="redish"><?=$numberOfCancelled?></b>
&emsp; Kündigerquote: <b class="orange"><?=$cancelQuote?>&thinsp;%</b>
&emsp; ⌀-Haltedauer bei Kündigern: <b class="blue"><?=number_format($averageRetention,2,',','.')?> Tage</b>
</p>

<hr/>



<div class="col-2" style="grid-template-columns: 2fr 1fr;">

	<figure class="mb">
		<h3 class="text-center">Subscriber nach Ressort</h3>
		<?php include tpl('charts/bar_chart');?>
	</figure>

	<figure class="mb">
		<h3 class="text-center">Subscriber im Zeitverlauf</h3>
		<?php include tpl('charts/single_stat_line');?>
	</figure>

</div>

<?php $barChart = $barChart2; ?>
<?php $singleChart = $singleChart2; ?>

<div class="col-2" style="grid-template-columns: 1fr 2fr;">

	<figure class="mb">
		<h3 class="text-center">Conversions im Zeitverlauf</h3>
		<?php include tpl('charts/single_stat_line');?>
	</figure>


	<figure class="mb">
		<h3 class="text-center">Conversions nach Ressort</h3>
		<?php include tpl('charts/bar_chart');?>
	</figure>

</div>

</main>
