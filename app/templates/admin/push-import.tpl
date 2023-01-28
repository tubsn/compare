<main>

<h1>Push-Import</h1>

<p>Hier lassen sich über die Cleverpush API Pushmeldungen importieren.</p>

<form style="max-width:500px" method="post" action="/push/import">
	<fieldset class="col-2">
	<label>von:
		<input type="date" name="from">
	</label>
	<label>bis:
		<input type="date" name="to">
	</label>
</fieldset>

<fieldset>
	<input type="hidden" value="web" name="channel">
	<label>
		<input type="checkbox" value="app" name="channel"> App importieren?
	</label>
</fieldset>

<button type="submit" class="mb">Push-Datenbank aktualisieren</button>
</form>

<div id="output"><div>


</main>
