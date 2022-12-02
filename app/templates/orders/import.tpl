<main>

<h1>Import Status</h1>

<p>Hier lassen sich über die Plenigo API alle verfügbaren Orders Importieren.</p>

<form style="max-width:500px" method="get" action="" onsubmit="imports.run(event);">
	<fieldset class="col-2">
	<label>von:
		<input type="date" name="from">
	</label>
	<label>bis:
		<input type="date" name="to">
	</label>
</fieldset>

<fieldset>
	<label>
		<input type="checkbox" checked name="ignore-cancelled"> Gekündigte ignorieren?
	</label>
</fieldset>

<button type="submit" class="mb">Order-Daten aktualisieren</button>
</form>

<div id="output"><div>


</main>
