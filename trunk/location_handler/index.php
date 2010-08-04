<?php
include(dirname(__FILE__).'/../bootstrap.php');
$url = explode('?',SERVER_URL,2);
$s = new Server(SERVER_HOST,$url[0]);
echo $s->serve();
