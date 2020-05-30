<?php 
require_once("../config.php");
require_once("../classes/User.php");
require_once("../classes/Message.php");

$limit = 7;

$message = new Message($connect, $_REQUEST['userLoggedIn']);
echo $message->getConversationDropdown($_REQUEST, $limit);
?>