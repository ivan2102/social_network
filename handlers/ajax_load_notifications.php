<?php 
require_once("../config.php");
require_once("../classes/User.php");
require_once("../classes/Notification.php");

$limit = 7;

$notification = new Notification($connect, $_REQUEST['userLoggedIn']);
echo $notification->getNotifications($_REQUEST, $limit);

?>