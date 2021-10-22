<main class="type-manager">

<?php include tpl('navigation/date-picker');?>

	<h1><?=$page['title']?></h1>

	<p>Hier lassen sich <b>Kategorien, Audiences und Tags verwalten</b> und Artikel entsprechend neu Zuweisen.<br> Die in der Datenbank vorhandenen Cluster werden per <b>Type-Ahead-Feld</b> gelistet (einfach im Eingabefeld losschreiben oder Doppelklicken). <br>Es werden nur Artikel <b>in dem ausgewählten Datumszeitraum</b> (oben rechts) angezeigt bzw. verändert.</br>
	</p>
	<p>Konvertieren entfernt die ursprüngliche Themenzuweisung am Artikel! - die Artikel sind dann nicht mehr Zugeordnet.</p>

	<div class="box" style="background-color:#b33d3d; color:white"><b>BITTE ULTRA VORSICHTIG SEIN!</b> - Eingaben genau prüfen! Eine Zuweisung lässt sich nicht mehr Rückgängig machen!</div>

	<hr>

	<h3>Auflistung aller Themen</h3>
	<table class="fancy js-sortable wide">
	<thead>
		<tr>
			<th style="text-align:right">Artikel</th>
			<th>Themen-Cluster</th>
			<th>Thema umbenennen</th>
			<th>in Audience konvertieren</th>
			<th>zu #-Tag konvertieren</th>
		</tr>
	</thead>

	<tbody>
	<?php foreach ($types as $cluster => $count): ?>
		<tr>
			<td class="text-right"><?=$count?></td>
			<td><a href="/type/<?=urlencode(str_replace('/', '-slash-', $cluster))?>"><?=htmlentities($cluster)?></a></td>
			<td>
				<form method="post" action="" class="flex-form">
					<input type="hidden" name="cluster" value="type">
					<input type="hidden" name="clusterValue" value="<?=htmlentities($cluster)?>">
					<input list="types" placeholder="neues Thema zuweisen" name="type">
					<button onclick="let newValue = this.parentElement.querySelector('input:nth-of-type(3)').value; if(newValue == '') {newValue = 'Leer'}; return confirm('Bitte umbenennen von Thema bestätigen\n<?=htmlentities($cluster)?> wird zu ' + newValue);" class="light">Umbenennen</button>
				</form>
			</td>
			<td>
				<form method="post" action="" class="flex-form">
					<input type="hidden" name="cluster" value="type">
					<input type="hidden" name="clusterValue" value="<?=htmlentities($cluster)?>">
					<input list="audiences" placeholder="in Audience konvertieren" name="audience">
					<button onclick="let newValue = this.parentElement.querySelector('input:nth-of-type(3)').value; if(newValue == '') {newValue = 'Leer'}; return confirm('Konvertieren von Thema zu Audience bestätigen\n<?=htmlentities($cluster)?> wird zu ' + newValue);" class="light">Konvertieren</button>
				</form>
			</td>
			<td>
				<form method="post" action="" class="flex-form">
					<input type="hidden" name="cluster" value="type">
					<input type="hidden" name="clusterValue" value="<?=htmlentities($cluster)?>">
					<input list="tags" placeholder="zu #-Tag konvertieren" name="tag">
					<button onclick="let newValue = this.parentElement.querySelector('input:nth-of-type(3)').value; if(newValue == '') {newValue = 'Leer'}; return confirm('Konvertieren von Thema zu Tag bestätigen\n<?=htmlentities($cluster)?> wird zu ' + newValue);" class="light">Konvertieren</button>
				</form>
			</td>
		</tr>

	<?php endforeach; ?>
	</tbody>
	</table>


	<hr>


	<h3>Auflistung aller Audiences</h3>
	<table class="fancy js-sortable wide">
	<thead>
		<tr>
			<th style="text-align:right">Artikel</th>
			<th>Audience-Cluster</th>
			<th>Audience umbenennen</th>
			<th>in Thema konvertieren</th>
			<th>zu #-Tag konvertieren</th>
		</tr>
	</thead>

	<tbody>
	<?php foreach ($audiences as $cluster => $count): ?>
		<tr>
			<td class="text-right"><?=$count?></td>
			<td><a href="/audience/<?=urlencode(str_replace('/', '-slash-', $cluster))?>"><?=htmlentities($cluster)?></a></td>
			<td>
				<form method="post" action="" class="flex-form">
					<input type="hidden" name="cluster" value="audience">
					<input type="hidden" name="clusterValue" value="<?=htmlentities($cluster)?>">
					<input list="audiences" placeholder="neue Audience zuweisen" name="audience">
					<button onclick="let newValue = this.parentElement.querySelector('input:nth-of-type(3)').value; if(newValue == '') {newValue = 'Leer'}; return confirm('Bitte umbenennen von Audience bestätigen\n<?=htmlentities($cluster)?> wird zu ' + newValue);" class="light">Umbenennen</button>
				</form>
			</td>
			<td>
				<form method="post" action="" class="flex-form">
					<input type="hidden" name="cluster" value="audience">
					<input type="hidden" name="clusterValue" value="<?=htmlentities($cluster)?>">
					<input list="types" placeholder="in Thema konvertieren" name="type">
					<button onclick="let newValue = this.parentElement.querySelector('input:nth-of-type(3)').value; if(newValue == '') {newValue = 'Leer'}; return confirm('Konvertieren Audience zu Thema bestätigen\n<?=htmlentities($cluster)?> wird zu ' + newValue);" class="light">Konvertieren</button>
				</form>
			</td>
			<td>
				<form method="post" action="" class="flex-form">
					<input type="hidden" name="cluster" value="audience">
					<input type="hidden" name="clusterValue" value="<?=htmlentities($cluster)?>">
					<input list="tags" placeholder="zu #-Tag konvertieren" name="tag">
					<button onclick="let newValue = this.parentElement.querySelector('input:nth-of-type(3)').value; if(newValue == '') {newValue = 'Leer'}; return confirm('Konvertieren Audience zu Tag bestätigen\n<?=htmlentities($cluster)?> wird zu ' + newValue);" class="light">Konvertieren</button>
				</form>
			</td>
		</tr>

	<?php endforeach; ?>
	</tbody>
	</table>


	<hr>


	<h3>Auflistung aller #-Tags</h3>
	<table class="fancy js-sortable wide">
	<thead>
		<tr>
			<th style="text-align:right">Artikel</th>
			<th>#-Tag-Cluster</th>
			<th>#-Tag umbenennen</th>
			<th>in Thema konvertieren</th>
			<th>in Audience konvertieren</th>
		</tr>
	</thead>

	<tbody>
	<?php foreach ($tags as $cluster => $count): ?>
		<tr>
			<td class="text-right"><?=$count?></td>
			<td><a href="/tag/<?=urlencode(str_replace('/', '-slash-', $cluster))?>"><?=htmlentities($cluster)?></a></td>
			<td>
				<form method="post" action="" class="flex-form">
					<input type="hidden" name="cluster" value="tag">
					<input type="hidden" name="clusterValue" value="<?=htmlentities($cluster)?>">
					<input list="tags" placeholder="neuen Tag-Name zuweisen" name="tag">
					<button onclick="let newValue = this.parentElement.querySelector('input:nth-of-type(3)').value; if(newValue == '') {newValue = 'Leer'}; return confirm('Umbenennen von Tag bestätigen\n<?=htmlentities($cluster)?> wird zu ' + newValue);" class="light">Umbenennen</button>
				</form>
			</td>
			<td>
				<form method="post" action="" class="flex-form">
					<input type="hidden" name="cluster" value="tag">
					<input type="hidden" name="clusterValue" value="<?=htmlentities($cluster)?>">
					<input list="types" placeholder="in Thema konvertieren" name="type">
					<button onclick="let newValue = this.parentElement.querySelector('input:nth-of-type(3)').value; if(newValue == '') {newValue = 'Leer'}; return confirm('Konvertieren von Tag zu Thema bestätigen\n<?=htmlentities($cluster)?> wird zu ' + newValue);" class="light">Konvertieren</button>
				</form>
			</td>
			<td>
				<form method="post" action="" class="flex-form">
					<input type="hidden" name="cluster" value="tag">
					<input type="hidden" name="clusterValue" value="<?=htmlentities($cluster)?>">
					<input list="audiences" placeholder="in Audience konvertieren" name="audience">
					<button onclick="let newValue = this.parentElement.querySelector('input:nth-of-type(3)').value; if(newValue == '') {newValue = 'Leer'}; return confirm('Konvertieren Tag zu Audience bestätigen\n<?=htmlentities($cluster)?> wird zu ' + newValue);" class="light">Konvertieren</button>
				</form>
			</td>
		</tr>

	<?php endforeach; ?>
	</tbody>
	</table>





<datalist id="types">
	<?php foreach ($availableTypes as $type): ?>
	<option value="<?=htmlentities($type)?>">
	<?php endforeach; ?>
</datalist>

<datalist id="audiences">
	<?php foreach ($availableAudiences as $audience): ?>
	<option value="<?=htmlentities($audience)?>">
	<?php endforeach; ?>
</datalist>

<datalist id="tags">
	<?php foreach ($availableTags as $tag): ?>
	<option value="<?=htmlentities($tag)?>">
	<?php endforeach; ?>
</datalist>


<!--
	<form class="" action="" method="post">
	<input type="hidden" name="CSRFToken" value="<?=$CSRFToken;?>">
		<label>Rolle:
			<select name="level">
				<option<?= $user['level'] == 'User' ? ' selected' : '' ?>>User</option>
				<option<?= $user['level'] == 'Admin' ? ' selected' : '' ?>>Admin</option>
			</select>
		</label>
		<label>E-Mail: <input name="email" value="<?=$user['email']?>"></label>
		<label>Passwort: <input placeholder="******" type="password" name="password"></label>

	<button type="submit">Daten speichern</button>&ensp;
	<a class="button light" href="/admin/users">zurück zur Übersicht</a>

	</form>
-->


</main>
