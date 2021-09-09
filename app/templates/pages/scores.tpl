<main>

<?php include tpl('navigation/date-picker');?>

<h1>Artikel Scores</h1>

<?php if ($articles): ?>

<style>
table {width:100%;}
table td:first-of-type {min-width:60%;}
</style>

<?=table_dump($articles);?>

<?php endif; ?>

</main>
