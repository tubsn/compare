<main class="detail-layout">



<div>

	<h1>Artikeldaten Editieren: </h1>

	<p>Hier können Sie nachträglich Artikeldaten anpassen. Achtung, diese werden möglicherweise beim nächsten Update wieder überschrieben.</p>

	<form class="mb" action="" method="post">
		<label>Dachzeile: 
			<input type="text" name="kicker" value="<?=$article['kicker']?>">
		</label>
		<button type="submit">Dachzeile Speichern</button>
	</form>

	<hr />

	<form action="" method="post">
		<label>Pubdate (Englisches Format nutzen!): 
			<input type="text" name="pubdate" value="<?=$article['pubdate']?>">
		</label>
		<button type="submit">Publikationsdatum Speichern</button>
	</form>

</div>


<article>
	<h3><?=$article['kicker'] ?? 'Dachzeile'?>&nbsp;<?php if ($article['plus']): ?><span class="plus-artikel bluebg">+</span><?php endif; ?></h3>
	<h1><?=$article['title']?></h1>
	<a class="noline" target="_blank" href="<?=$article['link']?>"><img style="border-radius:.2em;" src="<?=$article['image']?>"></a>
	<p class="condensed">Ressort: <b><?=ucwords($article['ressort'])?></b> | Artikel-ID: <b><?=$article['id']?></b> | Publiziert: <b><?=formatDate($article['pubdate'],"d.m.Y</b> H:i")?>&thinsp;Uhr</p>
	<p><?=$article['description']?></p>



</article>


</main>
