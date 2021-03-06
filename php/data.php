<?php
    while($row = mysqli_fetch_assoc($query)){
        include_once'config.php';
        $select = mysqli_query($con,"SELECT * FROM ticketinfo WHERE ticket_assignee = {$outgoing_id}");
        if(mysqli_num_rows($select) > 0){
            $selectedrow = mysqli_fetch_assoc($select);
            $sql2 = "SELECT * FROM messages WHERE (incoming_msg_id = {$selectedrow['ticket_owner']}
                OR outgoing_msg_id = {$selectedrow['ticket_owner']}) AND (outgoing_msg_id = {$outgoing_id} 
                OR incoming_msg_id = {$outgoing_id}) ORDER BY msg_id DESC LIMIT 1";
            $query2 = mysqli_query($con, $sql2);
            $row2 = mysqli_fetch_assoc($query2);
    
            
            $timestamp =  '';
            if(mysqli_num_rows($query2) > 0){
                $timestamp =  $row2['timestamp'];
    
                date_default_timezone_set('Asia/Singapore');
                $timestamp = strtotime($timestamp);
                $timediff = $_SERVER['REQUEST_TIME'] - $timestamp; 
    
                $result = $row2['msg'];
                if(date('Y',$_SERVER['REQUEST_TIME'])!=date('Y',$timestamp)){
                    $timestamp = date("M d Y", $timestamp);
                }else if(($timediff > 86400)&&($timediff < 604800)){
                    $timestamp = date("D", $timestamp);
                }elseif(($timediff > 604800)){
                    $timestamp = date("M d", $timestamp);
                }else{
                    if(date('d.m.Y',$_SERVER['REQUEST_TIME'])!=date('d.m.Y',$timestamp)){
                        $timestamp = 'Yesterday';
                    }elseif($timediff < 60){
                        $timestamp = 'Just now' ;
                    }
                    else{
                        $timestamp = date("g:i A", $timestamp);
                    }
                }
             }else{
                 $result ="No message available";
             }
            (strlen($result) > 28) ? $msg =  substr($result, 0, 28) . '...' : $msg = $result;
            if(isset($row2['outgoing_msg_id'])){
                ($outgoing_id == $row2['outgoing_msg_id']) ? $you = "You: " : $you = "";
            }else{
                $you = "";
            }
            ($outgoing_id == $row['unique_id']) ? $hid_me = "hide" : $hid_me = "";
    
            $selectedConvo = "";
            if(!isset($_SESSION['convo_user_id'])){
                $_SESSION["convo_user_id"] = $row["unique_id"];
            }
            if($row["unique_id"] == $_SESSION['convo_user_id']){
                $selectedConvo = "#671e1e";
            }
            $output .= '
                    <a  href="javascript:showConversation('.$row['unique_id'].')" class="text-decoration-none">
                        <div class="chat-convo-list-item p-2 rounded m-0 mb-2 min-vw-100" Style="background-color: '.$selectedConvo.'" >
                            <div class="d-flex flex-row">
                                <img class="rounded-circle p-0 align-self-xl-center my-auto" src="images/'. $row['img'] .'" width="42px" height="42px">
                                <div class="flex-grow-1 align-self-stretch px-2 text-truncate">
                                    <div class="pe-2 overflow-visible"><p class="text-truncate lh-1 mt-1 fs-6 fw-600 mb-0 overflow-visible text-black">'. $row['firstname']. " " . $row['lastname'] .'</p></div>
                                    <div class="d-flex">
                                        <span class="text-truncate fs-6 mt-0 flex-grow-1 pe-2 text-black">'. $you . $msg .'</span>
                                        <span class="text-muted mt-0 text-end">'.$timestamp.'</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>';
        }
    }
?>