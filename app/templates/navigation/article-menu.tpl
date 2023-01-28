<nav class="sub-nav">
	<ul>
		<li><a class="button light" href="<?=$backlink ?? session('referer')?>"> « zurück zur Übersicht</a></li>
	</ul>

	<ul>
		<?php if (auth_rights('type')): ?>
		<li><a class="noline pointer" href="/artikel/<?=$article['id']?>/edit">Editieren <img class="icon-edit" src="/styles/img/flundr/icon-edit.svg"></a></li>
		<li style="margin-right:1em"><a id="del-artikel" class="noline pointer">Löschen <img class="icon-delete" src="/styles/img/flundr/icon-delete-black.svg"></a></li>
		<fl-dialog selector="#del-artikel" href="/artikel/<?=$article['id']?>/delete">
		<h1>Artikel: <?=$article['id']?> - löschen?</h1>
		<p>Möchten Sie den Artikel wirklich löschen?</p>
		</fl-dialog>
		<?php endif; ?>
		<li><a class="button" title="letzter Stand: <?=formatDate($article['refresh'], 'd.m.Y H:i')?> Uhr" href="<?=$article['id']?>/refresh"><img class="icon-analytics" src="/styles/img/analytics-white.svg">Daten aktualisieren</a></li>
	</ul>

</nav>
