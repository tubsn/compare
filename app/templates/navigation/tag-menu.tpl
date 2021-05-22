<nav class="sub-nav" style="display:block; text-align:center">
	<ul>

<?php foreach (ARTICLE_TAGS as $tag): ?>
		<li><a class="noline" href="/tag/<?=urlencode(str_replace('/', '-slash-', $tag))?>"><?=ucwords($tag)?></a></li>

<?php endforeach; ?>
	</ul>
</nav>