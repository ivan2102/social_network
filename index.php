<?php require_once("header.php");
require_once("classes/User.php");
require_once("classes/Post.php");


if(isset($_POST['post'])){

    $uploadOk = 1;
    $imageName = $_FILES['fileToUpload']['name'];
    $errorMessage = "";

    if($imageName != ""){

        $targetDir = "assets/images/posts/";
        $imageName = $targetDir . uniqid() . basename($imageName);
        $imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

        if($_FILES['fileToUpload']['size'] > 1000000){
            $errorMessage = "Sorry your file is too large";
            $uploadOk = 0;
        }

        if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg"){
            $errorMessage = "Sorry, only jpeg, jpg, and png files are allowed";
            $uploadOk = 0;
        }

        if($uploadOk){
            if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $imageName)){
                //image uploaded okay
            }
            else {
                //image did not upload
                $uploadOk = 0;
            }
        }
    }

    if($uploadOk){

        $post = new Post($connect, $userLoggedIn);
        $post->submitPost($_POST['post_text'], 'none', $imageName);
      }
      else {
          echo "<div style='text-align: center;' class='alert alert-danger'>
                  $errorMessage
                </div>";
      }
 }
?>
<div class="user_details column">

<a href="<?php echo $userLoggedIn; ?>"><img src="<?php echo $user['profile_pic']; ?>"></a>

<div class="user_details_left_right">

<a href="<?php echo $userLoggedIn; ?>">

<?php 

echo $user['firstName'] . " " . $user['lastName'];
?>
</a>
<br>
<?php echo "Posts: " . $user['num_posts'] . "<br>";

echo "Likes: " . $user['num_likes'];
?>
</div>
</div>

<div class="main_column column">

<form class="post_form" action="index.php" method="POST" enctype="multipart/form-data">
<input type="file" name="fileToUpload" id="fileToUpload">
<textarea name="post_text" id="post_text" placeholder="Got something to say"></textarea>
<input type="submit" name="post" id="post_button" value="Post">
<hr>
</form>


<div class="posts_area"></div>
<img id="loading" src="assets/images/icons/loading.gif">
</div>

<script>
var userLoggedIn = '<?php echo $userLoggedIn; ?>';
$(document).ready(function(){
    $('#loading').show();

    //Original ajax request for loading first posts 
    $.ajax({
        url: "handlers/ajax_load_posts.php",
        type: "POST",
        data: "page=1&userLoggedIn=" + userLoggedIn,
        cache: false,

        success: function(data){
            $('#loading').hide();
            $('.posts_area').html(data);
        }
    });

    $(window).scroll(function(){
        var height = $('.posts_area').height();//Div containing posts
        var scroll_top = $(this).scrollTop();
        var page = $('.posts_area').find('.nextPage').val();
        var noMorePosts = $('.posts_area').find('.noMorePosts').val();

        if((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false'){
            $('#loading').show();


        var ajaxReq = $.ajax({
              url: "handlers/ajax_load_posts.php",
              type: "POST",
              data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
              cache: false,

              success: function(response){
                  $('.posts_area').find('.nextPage').remove();//Removes current .next page
                  $('.posts_area').find('.noMorePosts').remove();//Removes current .next page
                  $('#loading').hide();
                  $('.posts_area').append(response);
              }
          });

        } //End if statement 

        return false;
    });


});
</script>

</div>
   
</body>
</html>