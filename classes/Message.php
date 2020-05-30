<?php 

class Message {

    private $user_obj;
    private $connect;

    public function __construct($connect, $user){
        $this->connect = $connect;
        $this->user_obj = new User($connect, $user);
    }

    public function getMostRecentUser(){
        $userLoggedIn = $this->user_obj->getUsername();

        $query = mysqli_query($this->connect, "SELECT user_to, user_from FROM messages WHERE user_to='$userLoggedIn' AND user_from='$userLoggedIn' ORDER BY id DESC LIMIT 1");

        if(mysqli_num_rows($query) == 0)
            return false;

            $row = mysqli_fetch_array($query);
            $user_to = $row['user_to'];
            $user_from = $row['user_from'];

            if($user_to != $userLoggedIn)
            return $user_to;
            else
            return $user_from; 

        
    }


    public function sendMessage($user_to, $body, $date){

        if($body != ""){
            $userLoggedIn = $this->user_obj->getUsername();
            $query = mysqli_query($this->connect, "INSERT INTO messages VALUES('', '{$user_to}','{$userLoggedIn}','{$body}','{$date}','no','no','no')");
        }
    }

    public function getMessages($otherUser){
      $userLoggedIn = $this->user_obj->getUserName();
      $data = "";

      $query = mysqli_query($this->connect, "UPDATE messages SET opened='yes' WHERE user_to='$userLoggedIn' AND user_from='$otherUser'");
      $get_messages_query = mysqli_query($this->connect, "SELECT * FROM messages WHERE (user_to='$userLoggedIn' AND user_from='$otherUser') 
      OR (user_from='$userLoggedIn' AND user_to='$otherUser')");

      while($row = mysqli_fetch_array($get_messages_query)){
          $user_to = $row['user_to'];
          $user_from = $row['user_from'];
          $body = $row['body'];

          $div_top = ($user_to == $userLoggedIn) ? "<div class='message' id='green'>" : "<div class='message' id='blue'>";
          $data = $data . $div_top . $body . "</div><br><br>";
      }

      return $data;
    }


    public function getLatestMessage($userLoggedIn, $user2){

     $details_array = array();

     $query = mysqli_query($this->connect, "SELECT body, user_to, date FROM messages WHERE (user_to='$userLoggedIn' AND user_from='$user2') OR ( user_to='$user2' AND user_from='$userLoggedIn') ORDER BY id LIMIT 1");
     $row = mysqli_fetch_array($query);
     $sent_by = ($row['user_to'] == $userLoggedIn) ? "They said: " : "You said: ";


      //Timestamp
      $date_time_now = date("Y-m-d H:i:s");
      $start_date = new DateTime($row['date']);//Time of post
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

 array_push($details_array, $sent_by);// 0
 array_push($details_array, $row['body']);// 1
 array_push($details_array, $time_message);// 2

 return $details_array;

}


    public function getConversation(){
        $userLoggedIn = $this->user_obj->getUsername();
        $return_string = "";
        $conversation = array();

        $query = mysqli_query($this->connect, "SELECT user_to, user_from FROM messages WHERE user_to='$userLoggedIn' OR user_from='$userLoggedIn' ORDER BY id DESC");

        while($row = mysqli_fetch_array($query)){
            $user_to_push = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];

            if(!in_array($user_to_push, $conversation)){
                array_push($conversation, $user_to_push);
            }
        }

        foreach($conversation as $username){
            $user_found_obj = new User($this->connect, $username);
            $latest_messages_details = $this->getLatestMessage($userLoggedIn, $username);

            $dots = (strlen($latest_messages_details[1]) >= 12) ? "..." : "";//if it more than 12 characters return dots 12 characters in body body is position[1]
            $split = str_split($latest_messages_details[1], 12);//12characters
            $split = $split[0] . $dots;// nnajaja kkaka mmm...

            $return_string .= "<a href='messages.php?username=$username'> <div class='user_found_messages'>
                              <img src='" . $user_found_obj->getProfilePic() . "' style='border-radius: 3px; margin-right: 5px;'>
                              " . $user_found_obj->getFirstAndLastName() . "
                              <span class='timestamp_smaller' id='grey'> " . $latest_messages_details[2] . "</span>
                              <p id='grey' style='margin: 0;'>" . $latest_messages_details[0] . $split . " </p>
                              </div>
                              </a>";
            
            
            
        }

        return $return_string;
    }


    public function getConversationDropdown($data, $limit){

        $page = $data['page'];
        $userLoggedIn = $this->user_obj->getUsername();
        $return_string = "";
        $conversation = array();

        if($page == 1)
        $start = 0;
        else 
            $start = ($page -1) * $limit;

        $set_viewed_query = mysqli_query($this->connect, "UPDATE messages SET viewed='yes' WHERE user_to='$userLoggedIn'");    
        

        $query = mysqli_query($this->connect, "SELECT user_to, user_from FROM messages WHERE user_to='$userLoggedIn' OR user_from='$userLoggedIn' ORDER BY id DESC");
        while($row = mysqli_fetch_array($query)){

            $user_to_push = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];

            if(!in_array($user_to_push, $conversation)){
                array_push($conversation, $user_to_push);
            }
        }

        $num_iterations = 0;//Number of messages checked, number of messages seen
        $count = 1;//Number of messages posted 

        foreach($conversation as $username){

            if($num_iterations + 1 < $start)
            continue;

            if($count > $limit)
            break;
            else 
            $count++;

            $is_unread_query = mysqli_query($this->connect, "SELECT opened FROM messages WHERE user_to='$userLoggedIn' AND user_from='$username' ORDER BY id DESC");
            $row = mysqli_fetch_array($is_unread_query);
            $style = ($row['opened'] == 'no') ? "background-color: #ddedff;" : "";

            $user_found_obj = new User($this->connect, $username);
            $latest_messages_details = $this->getLatestMessage($userLoggedIn, $username);

            $dots = (strlen($latest_messages_details[1]) >= 12) ? "..." : "";//if it more than 12 characters return dots 12 characters in body body is position[1]
            $split = str_split($latest_messages_details[1], 12);//12characters
            $split = $split[0] . $dots;// nnajaja kkaka mmm...

            $return_string .= "<a href='messages.php?username=$username'> <div class='user_found_messages' style='" . $style . "'>
            <img src='"  . $user_found_obj->getProfilePic() . "' style='border-radius: 3px; margin-right: 5px;'>
            " . $user_found_obj->getFirstAndLastName() . "
            <span class='timestapm_smaller' id='grey'>" . $latest_messages_details[2] . "</span>
            <p id='grey' style='margin: 0;'>" . $latest_messages_details[0] . $split . "</p>
            </div>
            </a>";
        }

        //If posts were loaded
        if($count > $limit)
        $return_string .= "<input type='hidden' class='nextPageDropdownData' value='" . ($page + 1) . "'><input type='hidden' class='noMoreDropdownData' value='false'>";
        else
        $return_string .= "<input type='hidden' class='noMoreDropdownData' value='true'><p style='text-align: center;'>No more messages to load!</p>";
        return $return_string;
    }


    public function getUnreadNumber(){
        $userLoggedIn = $this->user_obj->getUsername();

        $query = mysqli_query($this->connect, "SELECT * FROM messages WHERE viewed='no' AND user_to='$userLoggedIn'");
        return mysqli_num_rows($query);
    }
}

?>