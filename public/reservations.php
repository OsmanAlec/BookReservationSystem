<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserved Books on Reserve it</title>
    
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/booklists.css">
</head>
<body>
    <?php 
    include '../config/check_login.php'; //first check if the user is loged in
    $page = "reservations"; //set page variable for navbar.php
    include '../views/partials/navbar.php';

    include '../config/database.php';

    //get the username from the session
    //the session starts in check_login.php
    $username = $_SESSION['username'];

    $limit = 5; //page limit

    //Count total reserved books for the user
    $countQuery = "SELECT COUNT(*) as total FROM books b JOIN reservations r ON r.ISBN = b.ISBN WHERE r.Username = ?";
    $countStmt = $conn->prepare($countQuery);
    $countStmt->bind_param("s", $username);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $total_rows = $countResult->fetch_assoc()['total'];
    //get how many pages there will be
    $total_pages = ceil($total_rows / $limit);
    $countStmt->close();

    //Gets what page the user is on and calculates the start row
    $page_number = isset($_GET['page']) ? (int)$_GET['page'] : 1; //if it is set, we put the page number, if not its the first page
    $initial_row = ($page_number - 1) * $limit;

    //Retrieve all books with pagination
    $query = "SELECT * FROM books b JOIN reservations r ON r.ISBN = b.ISBN WHERE r.Username = ? LIMIT ?, ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sii", $username, $initial_row, $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows < 1){
        echo '<h1 class="centered screentitle">You have no books reserved</h1>';
        echo '<p class="centered">Reserve Books in "Available Books" tab</p>';
        $stmt->close();
        $conn->close();
        exit(); //no need to continue the displays since there is no books
    }

    echo '<h1 class="centered screentitle">You have reserved the following books:</h1>';
    echo '<div class="grid-container">';

    //display the books
    while ($row = $result->fetch_assoc()) {
        echo '<div class="book-item">';
            echo '<h3>' . $row['BookTitle'] . '</h3>';
            echo '<p>Author: ' . $row['Author'] . '</p>';
            echo '<form method="POST" action="letgo.php">';
            echo '<input type="hidden" name="isbn" value="' . $row['ISBN'] . '">';
            echo '<button type="submit">Return</button>';
            echo '</form>';
            echo '</div>';
    }

    echo '</div>';

    //Add pagination links
    echo '<div class="pagination centered">';
    for ($page = 1; $page <= $total_pages; $page++) {  
        $active = ($page == $page_number) ? 'style="font-weight:bold;"' : '';
        echo '<a href="reservations.php?page=' . $page . '" ' . $active . '>' . $page . '</a> ';    }
    echo '</div>';

    $stmt->close();
    $conn->close();

    ?>



</body>
</html>