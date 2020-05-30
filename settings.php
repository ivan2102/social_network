<?php 
require_once("header.php");
require_once("settings_handler.php");

?>

<div class="main_column column">
<h4>Account Settings</h4>

<?php 
 echo "<img src='" . $user['profile_pic'] . "' id='small_profile_pic'>";
?>
<br>
<a href="upload.php">Upload new profile picture</a><br><br><br>



<?php 

$user_data_query = mysqli_query($connect, "SELECT firstName, lastName, email FROM users WHERE username='$userLoggedIn'");
$row = mysqli_fetch_array($user_data_query);

$firstName = $row['firstName'];
$lastName = $row['lastName'];
$email = $row['email'];

?>

<form action="settings.php" method="POST">
First Name: <input type="text" name="firstName" value="<?php echo $firstName; ?>" id="settings_input"><br>
Last Name: <input type="text" name="lastName" value="<?php echo $lastName; ?>" id="settings_input"><br>
Email: <input type="email" name="email" value="<?php echo $email; ?>" id="settings_input"><br>

<?php echo $message; ?>

<input type="submit" name="update_details" id="save_details" value="Update Details" class="success settings_submit"><br>
</form>

<h4>Change Password</h4>
<form action="settings.php" method="POST">
  Old Password: <input type="password" name="old_password" id="settings_input"><br>
  New Password: <input type="password" name="new_password_1" id="settings_input"><br> 
  New Password Again: <input type="password" name="new_password_2" id="settings_input"><br>

  <?php echo $password_message; ?>

  <input type="submit" name="update_password" id="save_details" value="Update Password" class="success settings_submit">
</form>

<h4>Close Account</h4>
<form action="settings.php" method="POST">
  <input type="submit" name="close_account" id="close_account" value="Close Account" class="danger settings_submit">  
</form>
</div>