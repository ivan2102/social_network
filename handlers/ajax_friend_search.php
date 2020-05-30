<?php require_once("../config.php");
require_once("../classes/User.php"); 

$query = $_POST['query'];
$userLoggedIn = $_POST['userLoggedIn'];

$names = explode(" ", $query);//Split the words

if(strpos($query, "_") !== false){
    $usersReturned = mysqli_query($connect, "SELECT * FROM users WHERE username LIKE $query% AND user_closed='no' LIMIT 8");
}
elseif(count($names) == 2){
    $usersReturned = mysqli_query($connect, "SELECT * FROM users WHERE (firstName LIKE '%$names[0]%' AND lastName LIKE '%$names[1]%') AND user_closed='no' LIMIT 8");
    }
    else{
        $usersReturned = mysqli_query($connect, "SELECT * FROM users WHERE (firstName LIKE '%$names[0]%' OR lastName LIKE '%$names[0]%') AND user_closed='no' LIMIT 8 ");

    }

    if($query != ""){
        while($row = mysqli_fetch_array($usersReturned)){

            $user = new User($connect, $userLoggedIn);

            if($row['username'] != $userLoggedIn){
                $mutual_friends = $user->getMutualFriends($row['username']) . " friends in common";
            }else {
                $mutual_friends = "";
            }

            if($user->isFriend($row['username'])){

                echo "<div class='resultDisplay'>
                <a href='messages.php?username=" . $row['username'] . "' style='color: #000'>
                <div class='liveSearchProfilePic'>
                <img src='" . $row['profile_pic'] . "'>
                </div>

                <div class='liveSearchText'>
                " . $row['firstName'] . " " . $row['lastName'] . "
                <p style='margin=0;'>". $row['username'] . "</p>
                <p id='grey'>". $mutual_friends . "</p>
                </div>
                </a>
                </div>";
            }
        }
    }



?>