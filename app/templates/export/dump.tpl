<main>

<h1><?=$info?></h1>

<style>
table td.narrow {max-width:100%;}
</style>

	<div style="display:flex; align-items:start; gap:2em;">
	<?=dump_table($data);?>
	<?=dump_table($grouped);?>
</div>



</main>