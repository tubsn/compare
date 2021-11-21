<main class="">

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<p>Retention: <?=$retention?></p>
<p>Produkt: <?=$product?></p>
<p>Orders: <?=$orders?></p>
<p>Kündiger: <?=$cancelled?></p>

<p>Quote: <?=$quote?></p>

<?=dump($products)?>


</main>
