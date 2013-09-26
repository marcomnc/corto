
<?php

error_reporting(E_ALL);
if (mail('marco@mancinellimarco.it','OGGETTO','E TESTO','From: no-reply@corto.com'))
{
echo 'inviata';
}
else
{
echo 'non inviata';
}
echo '<br />';
if (mail('a.balletti@autel.it','OGGETTO','E TESTO','From: no-reply@corto.com'))
{
echo 'inviata';
}
else
{
echo 'non inviata';
}

?>
