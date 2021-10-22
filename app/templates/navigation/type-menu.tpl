<nav class="sub-nav" style="display:block; text-align:center">
	<ul>
		<li><b>Themen im Zeitraum:</b></li>
		<?php foreach ($typeList as $type): ?>
		<li><a class="noline" href="/type/<?=urlencode(str_replace('/', '-slash-', $type))?>"><?=ucwords($type)?></a></li>
		<?php endforeach; ?>
	</ul>
</nav>
