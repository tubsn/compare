<main>

<?php include tpl('navigation/date-picker');?>

<h1><?=$info?></h1>

<style>
table td.narrow {max-width:100%;}
</style>


<?php if (isset($clicks) && (!empty($clicks))): ?>
	<h3>Pageviews Agentur Kampagne - 3 Für 3 </h3>
<div style="display:flex; align-items:start; gap:2em;">
	<?=dump_table($clicks);?>
	<?=dump_table($clicksgrouped);?>
</div>
<?php endif; ?>



<div style="display:flex; align-items:start; gap:2em;">
	<?=dump_table($conversions);?>
	<?=dump_table($conversionsGrouped);?>
</div>



</main>
