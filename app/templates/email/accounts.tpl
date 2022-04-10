<html>
<?php include tpl('email/css-styling')?>
<body>

<h1>Kilkaya Account</h1>

<p>Hallo <?=$account[1]?> anbei die Zugangsdaten für Kilkaya</p>

<a href="https://app.kilkaya.com/">https://app.kilkaya.com/</a><br>
Login: <?=$account[2]?><br>
Passwort: <?=$account[3]?>
<br>
<br>
falls es Probleme gibt Info an: <a href="mailto:support@lr-online.de">support@lr-online.de</a>
<br>
<br>
viele Grüße<br>
Sebastian

</body>
</html>
