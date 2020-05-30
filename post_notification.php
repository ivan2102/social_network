<?php 
require_once("header.php");

if(isset($_GET['id'])){
    $id = $_GET['id'];
}else {
    $id = 0;
}
?>

<div class="user_details column">

<a href="<?php echo $userLoggedIn; ?>"> <img src="<?php echo $user['profile_pic']; ?>"></a>

<div class="users_details_left_right">
    <a href="<?php echo $userLoggedIn; ?>">
    <?php 
    echo $user['firstName'] . " " . $user['lastName'];
    ?>
    </a>
    <br>
    <?php 
     echo "Posts: " . $user['num_posts'] . "<br>";
     echo "Likes: " . $user['num_likes'];
     ?>
</div>
</div>

<div class="main_column column" id="main_column">
    <div class="posts_area">
     <?php 
     
     $post = new Post($connect, $userLoggedIn);
     $post->getSinglePost($id);
     ?>
    </div>
</div>