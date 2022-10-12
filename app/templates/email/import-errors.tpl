<html>
<?php include tpl('email/css-styling')?>
<body>

<h1>Import Errors | <?=PORTAL?></h1>

<?php if (!empty($errors)): ?>
<p>Achtung es sind folgende Importfehler aufgetreten:</p>

<?php foreach ($errors as $error): ?>
<hr>
<p><?=dump($error)?></p>
<?php endforeach ?>

<?php else: ?>
<p>Keine Fehler beim Import</p>
<?php endif ?>

<hr>

<p>Script Runtime: <?=$runtime?></p>

</body>
</html>
