<nav class="sub-nav" style="display:block; text-align:center">
	<ul>
		<li><b>Tags im Zeitraum:</b></li>
		<?php foreach ($tagList as $tag): ?>
		<li><a class="noline" href="/tag/<?=urlencode(str_replace('/', '-slash-', $tag))?>"><?=ucwords($tag)?></a></li>
		<?php endforeach; ?>
	</ul>
</nav>
