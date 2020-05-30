<?php require_once("config.php"); ?>

<?php 
$firstName = "";
$lastName = "";
$email = "";
$email2 = "";
$password = "";
$password2 = "";
$date = "";
$error_array = array();

if(isset($_POST['reg_button'])){

    $firstName = strip_tags($_POST['reg_firstName']);
    $firstName = str_replace(' ', '', $firstName);
    $firstName = ucfirst(strtolower($firstName));
    $_SESSION['reg_firstName'] = $firstName;

    $lastName = strip_tags($_POST['reg_lastName']);
    $lastName = str_replace(' ', '', $lastName);
    $lastName = ucfirst(strtolower($lastName));
    $_SESSION['reg_lastName'] = $lastName;

    $email = strip_tags($_POST['reg_email']);
    $email = str_replace(' ', '', $email);
    $email = ucfirst(strtolower($email));
    $_SESSION['reg_email'] = $email;

    $email2 = strip_tags($_POST['reg_email2']);
    $email2= str_replace(' ', '', $email2);
    $email2 = ucfirst(strtolower($email2));
    $_SESSION['reg_email2'] = $email2;

    $password = strip_tags($_POST['reg_password']);
    $password2 = strip_tags($_POST['reg_password2']);

    $date = date("Y-m -d");

    if($email == $email2){

        if(filter_var($email, FILTER_VALIDATE_EMAIL)){

            $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        

        $email_check = mysqli_query($connect, "SELECT email FROM users WHERE email='$email'");

        $num_rows = mysqli_num_rows($email_check);

        if($num_rows > 0){

            array_push($error_array, "Email already in use<br>") ;
        }


    }
         else {
            array_push($error_array, "Invalid email format<br>");
        }

    }else {
        array_push($error_array, "Emails don't match<br>");
    }


    if(strlen($firstName) > 25 || strlen($firstName) < 2){

        array_push($error_array, "Your first name must be between 2 and 25 characters<br>");
    }

    if(strlen($lastName) > 25 || strlen($lastName) < 2){

        array_push($error_array, "Your last name must be between 2 and 25 characters<br>");
    }

    if($password != $password2){

        array_push($error_array, "Your passwords do not match<br>");
    }
    else{

        if(preg_match('/[^A-Za-z0-9]/', $password)){

            array_push($error_array, "Your password can only contain english  characters or numbers<br>");
        }
    }

    if(strlen($password) > 30 || strlen($password) < 5){

      array_push($error_array, "Your password must be between 5 and 30 characters<br>");
    }


    if(empty($error_array)){

        $password = md5($password); //Encrypt password before sending to database

        //Generate username by concatenating first name and last name
        $username = strtolower($firstName . "_" . $lastName);
        $check_username_query = mysqli_query($connect, "SELECT username FROM users WHERE username='$username'");

        $i = 0;
        //if username exists add number to username
        while(mysqli_num_rows($check_username_query) != 0){

            $i++; //Add 1 to i
            $username = $username . "_" . $i;
            $check_username_query = mysqli_query($connect, "SELECT username FROM users WHERE username='$username'");
        }

        //Profile picture assignment
        $rand = rand(1, 2); //Random number between 1 and 2

        if($rand == 1)

            $profile_pic = "assets/images/profile_pics/defaults/head_deep_blue.png";
            elseif($rand == 2)
            $profile_pic = "assets/images/profile_pics/defaults/head_emerald.png";
        
            $query = mysqli_query($connect, "INSERT INTO users VALUES('','{$firstName}', '{$lastName}', '{$username}','{$email}','{$password}','{$date}','{$profile_pic}','0','0','no',',')");

            array_push($error_array, "<span style='color: #14c800;'>You're all set! Go ahead and login!</span><br>");

            //Clear session variables
            $_SESSION['reg_firstName'] = "";
            $_SESSION['reg_lastName'] = "";
            $_SESSION['reg_email'] = "";
            $_SESSION['reg_email2'] = "";

    }
    
}

?>