<?php 
require_once("../config.php");
require_once("../classes/User.php");
require_once("../classes/Post.php");

$limit = 10;//Number of posts to be loaded per call

$posts = new Post($connect, $_REQUEST['userLoggedIn']);
$posts->loadPostsFriends($_REQUEST, $limit);
?>