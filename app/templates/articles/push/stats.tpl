<main>

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<?php if ($info): ?>
<p><?=$info?></p>
<?php endif; ?>

<p class="light-box" style="margin-bottom:2em;">
Anzahl Pushmeldungen: <b><?=count($notifications)?></b>
</p>


</main>
