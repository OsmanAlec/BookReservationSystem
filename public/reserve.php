<?php

include '../config/check_login.php';
include '../config/database.php';



if ($_SERVER["REQUEST_METHOD"] == "POST"){
    

    $isbn = $_POST['isbn'];

    //Update the books table to make sure the book is reserved
    $stmt1 = $conn->prepare("
    UPDATE books
    SET reserved = 1
    WHERE isbn = ?;
    ");

    $stmt1->bind_param('s', $isbn);
    
    //Now update reservations table
    $stmt2 = $conn->prepare("
    INSERT INTO reservations
    VALUES
    (?, ?, CURDATE());
    ");

    $stmt2->bind_param('ss', $isbn, $_SESSION['username']);

    //Execute both at the same time to prevent bugs
    if($stmt1->execute() && $stmt2->execute()){
        header("Location: booklist.php");
    } else {
        echo "Error Reserving the Book: \n" . $stmt->error;
    }

    $stmt1->close();
    $stmt2->close();


}

$conn->close();
exit();




?>