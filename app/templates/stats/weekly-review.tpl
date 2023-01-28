<main>

<?php include tpl('navigation/kw-picker');?>


<h1 class="text-center"><?=$page['title']?></h1>

<style>
	h3 {margin-bottom: 0;}
	th {width: 180px;}
	figure .mb {margin-bottom: 0;}
	.pos, .neg {color:#33a023;font-size: 0.7em;}
	.neg {color: #a02323;}
	.lr {color: #df886d;}
	.moz {color: #0967a8;}
	.backdrop {opacity: 0.8;}
	.backdrop:hover {opacity: 1;}

	.calendar-picker {justify-content: center; background-color: transparent; position:static}

</style>


<table class="fancy neutral" style="table-layout: fixed; border: 2px solid #404040; max-width:800px; width:100%; margin:0 auto; margin-top:2em; margin-bottom:1em;">

	<tr>
		<th style="text-align:right"></th>
		<td style="text-align:center; background-color:#ffb9a4"><i>LR+</i></td>
		<td style="text-align:center; background-color:#9bccee"><i>MOZplus</i></td>
		<td style="text-align:center; background-color:#e1e1e1"><b><i>BB gesamt</i></b></td>

	</tr>

	<tr>
		<th style="text-align:right">Pageviews*</th>

		<td class="text-center"><?=gnum($LR['pageviews'])?></td>
		<td class="text-center"><?=gnum($MOZ['pageviews'])?></td>
		<td class="text-center"><?=gnum($LR['pageviews'] + $MOZ['pageviews'])?></td>
	</tr>		

	<tr>
		<th style="text-align:right">Subscriberviews*</th>
		<td class="text-center"><?=gnum($LR['subscriberviews'])?></td>
		<td class="text-center"><?=gnum($MOZ['subscriberviews'])?></td>
		<td class="text-center"><?=gnum($LR['subscriberviews'] + $MOZ['subscriberviews'])?></td>
	</tr>

	<tr>
		<th style="text-align:right">Mediatime*</th>
		<td class="text-center"><?=gnum($LR['mediatime'])?>&thinsp;s</td>
		<td class="text-center"><?=gnum($MOZ['mediatime'])?>&thinsp;s</td>
		<td class="text-center"><?=gnum(($LR['mediatime'] + $MOZ['mediatime']) / 2)?>&thinsp;s</td>
	</tr>


	<tr><td colspan="4" style="background:#686868; padding:5px;"></td></tr>

	<tr>
		<th style="text-align:right">Conversions</th>
		<td class="text-center"><b><?=gnum($LR['orders'])?></b></td>
		<td class="text-center"><b><?=gnum($MOZ['orders'])?></b></td>
		<td class="text-center"><b><?=gnum($LR['orders'] + $MOZ['orders'])?></b></td>
	</tr>

	<tr>
		<th style="text-align:right">davon Monatsabos</th>
		<td class="text-center"><?=gnum($LR['ordersMonthly'])?></td>
		<td class="text-center"><?=gnum($MOZ['ordersMonthly'])?></td>
		<td class="text-center"><?=gnum($LR['ordersMonthly'] + $MOZ['ordersMonthly'])?></td>
	</tr>

	<tr>
		<th style="text-align:right">davon Jahresabos</th>
		<td class="text-center"><?=gnum($LR['ordersYearly'])?></td>
		<td class="text-center"><?=gnum($MOZ['ordersYearly'])?></td>
		<td class="text-center"><?=gnum($LR['ordersYearly'] + $MOZ['ordersYearly'])?></td>
	</tr>

	<tr>
		<th style="text-align:right">über Plus</th>
		<td class="text-center"><?=gnum($LR['ordersExternal'])?></td>
		<td class="text-center"><?=gnum($MOZ['ordersExternal'])?></td>
		<td class="text-center"><?=gnum($LR['ordersExternal'] + $MOZ['ordersExternal'])?></td>
	</tr>

	<tr><td colspan="4" style="background:#686868; padding:5px;"></td></tr>

	<tr>
		<th style="text-align:right">erstellte Artikel</th>
		<td class="text-center"><?=gnum($LR['articles'])?></td>
		<td class="text-center"><?=gnum($MOZ['articles'])?></td>
		<td class="text-center"><?=gnum($LR['articles'] + $MOZ['articles'])?></td>
	</tr>

	<tr>
		<th style="text-align:right">Audience-Artikel</th>
		<td class="text-center"><?=gnum($LR['audienceArticles'])?></td>
		<td class="text-center"><?=gnum($MOZ['audienceArticles'])?></td>
		<td class="text-center"><?=gnum($LR['audienceArticles'] + $MOZ['audienceArticles'])?></td>
	</tr>

	<tr>
		<th style="text-align:right">Geisterquote</th>
		<td class="text-center"><?=gnum($LR['ghostQuote'])?>&thinsp;%</td>
		<td class="text-center"><?=gnum($MOZ['ghostQuote'])?>&thinsp;%</td>
		<td class="text-center"><?=gnum(($LR['ghostQuote'] + $MOZ['ghostQuote']) / 2)?>&thinsp;%</td>
	</tr>

</table>

<p class="text-center"><small>*Daten aus Kilkaya (restliche Angaben Compare/Plenigo)</small></p>


</main>
