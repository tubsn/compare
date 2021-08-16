<div class="floating-settings">


<div>
<form method="post" action="/orders/set_paid_filter">
	<label style="cursor:pointer;"><input type="checkbox" onchange="this.form.submit()" <?php if (session('paid_filter')): ?>checked<?php endif ?> name="paid_filter"> App-Produkte zeigen</label>
</form>
</div>


<div>
 <a href="/orders/today">Heute</a> | <a href="/orders/yesterday">Gestern</a>
</div>

<div>
	<form method="post" action="/orders/set_date">
		<input onchange="this.form.submit()" name="date" type="date" <?php if (isset($date)): ?>value="<?=$date?>"<?php endif ?>>
	</form>
</div>

<div>
<form method="post" action="/orders/set_client">
<select onchange="this.form.submit()" name="client">
	<option<?php if (session('client') == 'LR'): ?> selected<?php endif ?>>LR</option>
	<option<?php if (session('client') == 'SWP'): ?> selected<?php endif ?>>SWP</option>
	<option<?php if (session('client') == 'MOZ'): ?> selected<?php endif ?>>MOZ</option>
</select>
</form>
</div>

<!--
<br />
<div>
	<form method="get" action="/order" onsubmit="preventDefault(); alert('muh');">
		<input onchange="this.form.submit()" name="id" type="text">
	</form>
</div>
-->

</div>
