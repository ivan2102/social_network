<?php require_once("../config.php");
require_once("../classes/User.php");
require_once("../classes/Post.php");
require_once("../classes/Notification.php");

if(isset($_POST['post_body'])){

    $post = new Post($connect, $_POST['user_from']);
    $post->submitPost(($_POST['post_body']), $_POST['user_to']);
}



?>