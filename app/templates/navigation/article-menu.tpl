<nav class="sub-nav">
	<ul>
		<li><a class="button light" href="<?=$backlink ?? session('referer')?>"> « zurück zur Übersicht</a></li>
	</ul>

	<?php if (auth_rights('type')): ?>
	<ul>
		<li><a class="noline pointer" href="/artikel/<?=$article['id']?>/edit">editieren <img class="icon-edit" src="/styles/img/flundr/icon-edit.svg"></a></li>
		<li><a id="del-artikel" class="noline pointer">löschen <img class="icon-delete" src="/styles/img/flundr/icon-delete-black.svg"></a></li>
		<fl-dialog selector="#del-artikel" href="/artikel/<?=$article['id']?>/delete">
		<h1>Artikel: <?=$article['id']?> - löschen?</h1>
		<p>Möchten Sie den Artikel wirklich löschen?</p>
		</fl-dialog>
	</ul>
	<?php endif; ?>

</nav>
