<?php 
require_once("../config.php");
require_once("../classes/User.php");

$query = $_POST['query'];
$userLoggedIn = $_POST['userLoggedIn'];

$names = explode(" ", $query);//Splits names on first and last name in array

//If query contains an underscore, assume user is searching for username
if(strpos($query, '_') !== false)
$usersReturnedQuery = mysqli_query($connect, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");

//If there are two words, assume they are first and last names respectively
else if(count($names) == 2)
$usersReturnedQuery = mysqli_query($connect, "SELECT * FROM users WHERE (firstName LIKE '$names[0]%' AND lastName LIKE '$names[1]%') AND user_closed='no' LIMIT 8");
//If query has one word only, search first names or last names
else
$usersReturnedQuery = mysqli_query($connect, "SELECT * FROM users WHERE (firstName LIKE '$names[0]%' OR lastName LIKE '$names[0]%') AND user_closed='no' LIMIT 8");

if($query != ""){

    while($row = mysqli_fetch_array($usersReturnedQuery)){

        $user = new User($connect, $userLoggedIn);

        if($row['username'] != $userLoggedIn)
        $mutual_friends = $user->getMutualFriends($row['username']) . " friends in common";
        else
        $mutual_friends = "";

        echo "<div class='resultDisplay'>
        <a href='" . $row['username'] . "' style='color: #1485bd'>
        <div class='liveSearchProfilePic'>
        <img src='". $row['profile_pic'] . "'>
        </div>
        <div class='liveSearchText'>
        " . $row['firstName'] . " " . $row['lastName'] . "
        <p>" . $row['username'] . "</p>
        <p id='grey'>". $mutual_friends . "</p>
        </div>
        </a>
        </div>";
    }
}
?>