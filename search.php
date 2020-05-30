<?php 
require_once("header.php");

if(isset($_GET['query'])){
    $query = $_GET['query'];
}else {
    $query= "";
}

if(isset($_GET['type'])){
    $type = $_GET['type'];
}else {
    $type = "name";
}
?>

<div class="main_column column" id="main_column">
<?php 
if($query == "")
    echo "You must enter something in the search box.";
else {

    //If query contains underscore, assume user is searching for usernames
    if($type == "username")
    $usersReturnedQuery = mysqli_query($connect, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");
    else {

        $names = explode(" ", $query);

        if(count($names) == 3)
        $usersReturnedQuery = mysqli_query($connect, "SELECT * FROM users WHERE (firstName LIKE '$names[0]%' AND lastName LIKE '$names[2]%') AND user_closed='no' LIMIT 8");

        else if(count($names)== 2)
        $usersReturnedQuery = mysqli_query($connect, "SELECT * FROM users WHERE (firstName LIKE '$names[0]%' AND lastName LIKE '$name[1]%') AND user_closed='no' LIMIT 8");

        else
        $usersReturnedQuery = mysqli_query($connect, "SELECT * FROM users WHERE (firstName LIKE '$names[0]%' OR lastName LIKE '$names[0]%') AND user_closed='no' LIMIT 8");

      }

      //Check if results were found
      if(mysqli_num_rows($usersReturnedQuery) == 0)

        echo "We can't find anyone with a " . $type . " like: " . $query;
        else
        echo mysqli_num_rows($usersReturnedQuery) . " results found: <br><br>";

        echo "<p id='grey'>Try searching for:</p>";
        echo "<a href='search.php?query=" .$query ."&type=name'>Names</a>,<a href='search.php?query=" . $query ."&type=username'>Usernames</a><br><br><hr id='search_hr'>";

        while($row = mysqli_fetch_array($usersReturnedQuery)){

            $user_obj = new User($connect, $user['username']);

            $button = "";
            $mutual_friends ="";

            if($user['username'] != $row['username']){

                //Generate button depends on friendship status
                if($user_obj->isFriend($row['username']))
                $button = "<input type='submit' name='" . $row['username'] . "' class='danger' value='Remove Friend'>";

                else if($user_obj->didReceiveRequest($row['username']))
                $button = "<input type='submit' name='" . $row['username'] . "' class='warning' value='Respond to Request'>";

                else if($user_obj->didSendRequest($row['username']))
                $button = "<input type='submit'  class='info' value='Request Send'>";

                else
                $button = "<input type='submit' name='" . $row['username'] . "' class='success' value='Add Friend'>";

                $mutual_friends = $user_obj->getMutualFriends($row['username']) . " friends in common";

                //Button forms

                if(isset($_POST[$row['username']])){

                    if($user_obj->isFriend($row['username'])){
                        $user_obj->removeFriend($row['username']);
                        header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                    }
                    elseif($user_obj->didReceiveRequest($row['username'])){
                        header("Location: requests.php");
                    }
                    elseif($user_obj->didSendRequest($row['username'])){

                    }
                    else {
                        $user_obj->sendRequest($row['username']);
                        header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                    }
                }

                
            }

            echo "<div class='search_result'>
                 <div class='searchPageFriendButtons'>
                 <form action='' method='POST'>
                 " . $button . "
                 <br>
                 </form>
                 </div>

                 <div class='result_profile_pic'>
                 <a href='" . $row['username'] . "'><img src='" . $row['profile_pic'] . "' style='height= 150px;'></a>
                 </div>

                 <a href='" . $row['username'] . "'> " . $row['firstName'] . " " . $row['lastName'] . "
                 <p id='grey'>" . $row['username'] . "</p>
                 </a>
                <br>
                " . $mutual_friends . "<br>
            </div>
            <hr id='search_hr'>";
        }//End while loop


      
}
?>

</div>