<?php

include '../config/check_login.php';
include '../config/database.php'; //we have to reconnect since this page's code is not included but opened seperately in the new page

//check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST'){

    $isbn = $_POST['isbn']; 

    //update table books
    $stmt1 = $conn->prepare("
    UPDATE books
    SET reserved = 0
    WHERE ISBN = ?;
    ");

    $stmt1->bind_param('s', $isbn);

    //update table reservations
    $stmt2 = $conn->prepare("
    DELETE FROM reservations
    WHERE isbn = ?;
    ");

    $stmt2->bind_param('s', $isbn);

    //Execute both statements at the same time to prevent bugs
    if($stmt1->execute() && $stmt2->execute()){
        header("Location: reservations.php"); //go back to original page
    } else {
        echo "Error in releasing the Book: \n" . $stmt->error;
    }

    $stmt1->close();
    $stmt2->close();

}

$conn->close();



?>