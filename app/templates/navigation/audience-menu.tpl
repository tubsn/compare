<nav class="sub-nav" style="display:block; text-align:center">
	<ul>
		<li><b>Audiences im Zeitraum:</b></li>
		<?php foreach ($audienceList as $audience): ?>
		<li><a class="noline" href="/audience/<?=urlencode(str_replace('/', '-slash-', $audience))?>"><?=ucwords($audience)?></a></li>
		<?php endforeach; ?>
	</ul>
</nav>
