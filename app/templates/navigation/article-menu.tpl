<nav class="sub-nav">
	<ul>
		<li><a class="noline" href="<?=session('referer')?>">zurück zur Übersicht</a></li>
	</ul>

	<?php if (logged_in()): ?>
	<ul>
		<li><a id="del-artikel" class="noline pointer">Artikel Löschen <img class="icon-delete" src="/styles/img/flundr/icon-delete-black.svg"></a></li>
		<fl-dialog selector="#del-artikel" href="/artikel/<?=$article['id']?>/delete">
		<h1>Artikel: <?=$article['id']?> - löschen?</h1>
		<p>Möchten Sie den Artikel wirklich löschen?</p>
		</fl-dialog>
	</ul>
	<?php endif; ?>

</nav>