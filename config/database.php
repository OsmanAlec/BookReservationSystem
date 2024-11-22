
<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "assignmentdb";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    }
    echo "<script>console.log(\"Connected successfully to assignmentdb\");</script>";

?>