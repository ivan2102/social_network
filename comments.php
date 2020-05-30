 <?php 
 require_once("config.php");
 require_once("classes/User.php");
 require_once("classes/Post.php");
 require_once("classes/Notification.php");
  
if(isset($_SESSION['username'])){
$userLoggedIn = $_SESSION['username'];
$user_details_query = mysqli_query($connect, "SELECT * FROM users WHERE username='$userLoggedIn'");
$user = mysqli_fetch_array($user_details_query);

}else {

    header("Location: register.php");
}

?>
   
   <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<style type="text/css">

* {
    font-size: 12px;
    font-family: 'Arial','Helvetica',sans-serif;
}
</style>


<script>

function toggle(){

    var element = document.getElementById("comment_section");

    if(element.style.display == "block")//If it shown it 
    element.style.display = "none"; // Hide it
     else
     element.style.display = "block"; //Show it
}

</script>

<?php 
//Get id of post
if(isset($_GET['post_id'])){
    $post_id = $_GET['post_id'];
}

$user_query = mysqli_query($connect, "SELECT added_by, user_to FROM posts WHERE id='$post_id'");
$row = mysqli_fetch_array($user_query);
$posted_to = $row['added_by'];
$user_to = $row['user_to'];
if(isset($_POST['postComment' . $post_id])){
    $post_body = $_POST['post_body'];
    $post_body = mysqli_escape_string($connect, $post_body);
    $date_time_now = date("Y-m-d H:i:s");
    $insert_post = mysqli_query($connect, "INSERT into comments VALUES('', '{$post_body}','{$userLoggedIn}','{$posted_to}','{$date_time_now}','no','{$post_id}')");

    if($posted_to != $userLoggedIn){
        $notification = new Notification($connect, $userLoggedIn);
        $notification->insertNotification($post_id, $user_to, "comment");
    }
    if($user_to != 'none' && $user_to != $userLoggedIn){
        $notification = new Notification($connect, $userLoggedIn);
        $notification->insertNotification($post_id, $user_to, "profile_comment");
    }

    $get_commenters = mysqli_query($connect, "SELECT * FROM comments WHERE post_id='$post_id'");
    $notifed_users = array();
    while($row = mysqli_fetch_array($get_commenters)){

        if($row['posted_by'] != $posted_to && $row['posted_by'] !=  $user_to && $row['posted_by'] != $userLoggedIn && !in_array($row['posted_by'], $notifed_users)){

            $notification = new Notification($connect, $userLoggedIn);
            $notification->insertNotification($post_id, $row['posted_by'], "comment_non_owner");

            array_push($notifed_users, $row['posted_by']);
        }
    }
    echo "<p>Comment Posted!</p>";
}

?>

<form action="comments.php?post_id=<?php  echo $post_id; ?>" id="comment_form" name="postComment<?php echo $post_id; ?>" method="POST">
  <textarea name="post_body" ></textarea> 
  <input type="submit" name="postComment<?php echo $post_id; ?>" value="Post">
  </form>

  <!-- Load comments -->
<?php 
$query_comments = mysqli_query($connect, "SELECT * FROM comments WHERE post_id='$post_id'");
$count = mysqli_num_rows($query_comments);

if($count != 0) {

    while($row = mysqli_fetch_array($query_comments)){

        $comment_body = $row['post_body'];
        $posted_to = $row['posted_to'];
        $posted_by = $row['posted_by'];
        $date_added = $row['date_added'];
        $removed = $row['removed'];

             //Timestamp
             $date_time_now = date("Y-m-d H:i:s");
             $start_date = new DateTime($date_added);//Time of post
             $end_date = new DateTime($date_time_now);//Current time
             $interval = $start_date->diff($end_date);//Difference between dates
             if($interval->y >= 1){
                 if($interval == 1)
                 $time_message = $interval->y . " year ago";//1year ago
                 else
                 $time_message = $interval->y . " years ago";//1+ year ago
             }
    
             elseif($interval->m >= 1){
    
                 if($interval->d == 0){
                    $days = " ago";
                  }
                  elseif($interval->d == 1){
                      $days = $interval->d . " day ago";
                  }
                  else {
                      $days = $interval->d ." days ago";
                  }
    
                  if($interval->m == 1){
    
                    $time_message = $interval->m . " month" . $days;
                  }
                  else{
                      $time_message = $interval->m . " months" .$days;
                  }
             }
    
             elseif($interval->d >= 1){
    
                if($interval->d == 1){
    
                    $time_message = "Yesterday";
                }
                else{
                    $time_message = $interval->d . " days ago";
                }
             }
    
             elseif($interval->h >= 1){
    
                if($interval->h == 1){
    
                    $time_message = $interval->h . " hour ago";
    
                }
                else {
                    $time_message = $interval->h . " hours ago";
                }
             }
    
             elseif($interval->i >= 1){
    
                if($interval->i == 1){
    
                    $time_message = $interval->i . " minute ago";
                }
                else {
                    $time_message = $interval->i . " minutes ago";
             }
    
        }
    
        else{
            if($interval->s < 30){
    
                $time_message = "Just now";
            }
            else{
                $time_message = $interval->s . " seconds ago";
            }
        }

        $user_obj = new User($connect, $posted_by);

        ?>
    <div class="comment_section">
    <a href="<?php echo $posted_by; ?>" target="_parent"><img src="<?php echo $user_obj->getProfilePic(); ?>" title="<?php echo $posted_by; ?>" style="float:left;" height="30"></a>
    <a href="<?php echo $posted_by; ?>" target="_parent"><b> <?php echo $user_obj->getFirstAndLastName(); ?> </b></a>
     &nbsp;&nbsp;&nbsp;&nbsp; <?php echo $time_message . "<br>" . $comment_body; ?>
      <hr>
    </div>

     <?php

 


}

}

else{
      echo "<center><br><br>No comments to show!</center>";
}


?>




</body>
</html>