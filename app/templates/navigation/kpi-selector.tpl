<section style="margin:0 auto; width:90%;">

<h1>Einstellungen:</h1>
<p>Bitte in die Felder jeweils einen Operanden z.B. >,  <,  =, >= und einen Wert eintragen.</p>

<form method="post" action="" class="mb box">

	<div style="display:flex; gap:1em; margin-bottom:1em; ">

		<label>Conversions: <input name="conversions" type="text" value="<?=$kpis['conversions'] ?? ''?>" placeholder="z.B. > 5"></label>
		<label>Pageviews: <input name="pageviews" type="text" value="<?=$kpis['pageviews'] ?? ''?>" placeholder="z.B. > 2500"></label>
		<label>AVGMediatime: <input name="avgmediatime" type="text" value="<?=$kpis['avgmediatime'] ?? ''?>" placeholder="z.B. > 120"></label>
		<label>Subscriberviews: <input name="subscriberviews" type="text" value="<?=$kpis['subscriberviews'] ?? ''?>" placeholder="z.B. > 1000"></label>
		<label>Cancellations: <input name="cancelled" type="text" value="<?=$kpis['cancelled'] ?? ''?>" placeholder="z.B. = 0"></label>

	</div>

	<button type="submit">KPI schwelle Setzen</button>

</form>

<hr />


</section>
