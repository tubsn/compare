<nav class="sub-nav" style="display:block; text-align:center">
	<ul>
		<li><b>Ressorts im Zeitraum:</b></li>
		<?php foreach ($ressorts as $ressort): ?>
			<li><a class="noline" href="/ressort/<?=$ressort?>"><?=ucfirst($ressort)?></a></li>
		<?php endforeach; ?>
	</ul>
</nav
