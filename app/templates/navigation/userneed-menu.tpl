<nav class="sub-nav" style="display:block; text-align:center">
	<ul>
		<li><b>Bedürfniskategorien im Zeitraum:</b></li>
		<?php foreach ($userneedList as $userneed): ?>
		<li><a class="noline" href="/userneed/<?=urlencode(str_replace('/', '-slash-', $userneed))?>"><?=ucwords($userneed)?></a></li>
		<?php endforeach; ?>
	</ul>
</nav>
