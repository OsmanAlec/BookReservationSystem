<?php
//A block of code to make sure the user is logged in to 
//access files such as reservations or booklist.
session_start();

//If not set
if (!isset($_SESSION['username'])){
    //redirect to welcome page
    header("Location: index.html");
    exit();
}

?>