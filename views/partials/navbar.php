<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/navbar.css">
</head>

<ul class="navbar">
    <li
        <?php if ($page == 'reservations') echo ' class="active"';?>
    >
        <a href="reservations.php">Your Reserved Books</a>
    </li>

    <li
        <?php if ($page == 'booklist') echo ' class="active"';?>
    >
        <a href="booklist.php">Available Books</a>
    </li>

    <li>
        <a href="logout.php">Log Out</a>
    </li>
</ul>