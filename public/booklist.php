<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Books on Reserve It</title>

    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/booklists.css">

</head>
<body>
    <?php 
    include '../config/check_login.php'; //first check if the user is loged in
    $page = "booklist"; //set page variable for navbar.php
    include '../views/partials/navbar.php';

    include '../config/database.php'; //database connection

    // Get data from the forms for filtering and pagination
    $searchQuery = $_GET['search'] ?? '';
    $categoryFilter = $_GET['categories'] ?? '';
    $page_number = $_GET['page'] ?? 1;

    //set row limit per page to 5
    $limit = 5;
    //get the position of the first row in the specific page number
    $initial_page = ($page_number - 1) * $limit;

    //Initialise query to only show books that are available
    $query = "SELECT * FROM books WHERE Reserved = 0";
    $params = [];
    $types = "";

    //run the query to check if there are available books or not
    $result = mysqli_query($conn, $query);

    //No books available, display this message, but continue in the program to let the user
    //search for books that are reserved by other users
    if (mysqli_num_rows($result) < 1){
        echo '<h1 class="centered screentitle">There are no books currently available</h1>';
    }

    else{
        echo '<h1 class="centered screentitle">Here are the books you can reserve:</h1>';
    }

    // --- FILTER AND SEARCH LOGIC ---

    // Check if a search query was entered
    if (!empty($searchQuery)) {
        // Split the search query into words to check author AND/OR titles
        $words = explode(' ', $searchQuery);

        //If there is a search, show the books even if they are reserved
        $query = "SELECT * FROM books WHERE 1 = 1";
        
        //Build conditions for each word
        $conditions = [];
        foreach ($words as $word) {
            $conditions[] = "(BookTitle LIKE ? OR Author LIKE ?)";
            $likeWord = "%" . $word . "%";
            $params[] = $likeWord;
            $params[] = $likeWord;
            $types .= "ss";
        }
        
        // Append all conditions to the query
        if (!empty($conditions)) {
            $query .= " AND (" . implode(" AND ", $conditions) . ")";
        }
    }

    //Check if there is a category filter put on
    //SELECT and CLEAR options both have the value of 0, so this would clear
    //the filters
    if (!empty($categoryFilter) && $categoryFilter !== '0') {
        $query .= " AND category = ?";
        $params[] = $categoryFilter;
        $types .= "s";
    }

    // --- PAGINATION LOGIC ---

    //Default page number
    if (!isset ($_GET['page']) ) {          
        $page_number = 1;      
    } else {          
        $page_number = $_GET['page'];      
    } 

    //Replace the start of $query with SELECT COUNT(*) to count how many books
    //match the query so far to create pagination dynamically.
    $countQuery = str_replace("SELECT *", "SELECT COUNT(*) as total", $query);
    
    //Execute the counting
    $countStmt = $conn->prepare($countQuery);
    //if there were a search and/or category, bind those parameters in
    if (!empty($params)) {
        $countStmt->bind_param($types, ...$params);
    }
    $countStmt->execute();

    $countResult = $countStmt->get_result();
    $total_rows = $countResult->fetch_assoc()['total'];

    //calculate how many pages
    $total_pages = ceil($total_rows / $limit);
    $countStmt->close();
 
    // Add Limit for pagination
    $query .= " LIMIT ?, ?";
    $params[] = $initial_page;
    $params[] = $limit;
    $types .= "ii";

    // Execute the full query to get the page
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();


    echo '<form method="GET" class="centered" style="flex-direction:row;" action="booklist.php">
    <input type="text" name="search" placeholder="Type book title or author..." value="'. $searchQuery . '">
    <button type="submit">Search</button>
    </form>';

    
    echo '<form action="" class="centered" method="GET"">
    <select name="categories" id="categories" onchange="this.form.submit()">
        <option value="0">-SELECT CATEGORY-</option>
        <option value="0">Clear</option>';
         
        // Fetch categories from the database
        $stmtCategories = $conn->prepare("SELECT * FROM categories");
        $stmtCategories->execute();
        $resultCategories = $stmtCategories->get_result();
        while ($row = $resultCategories->fetch_assoc()) {
            //set $selected to 'selected' if the category filter is on the same category id
            $selected = ($row['CategoryID'] == $categoryFilter) ? 'selected' : '';
            //add the 'selected' value here to display it on the user's screen.
            echo '<option value="' . $row['CategoryID'] . '" ' . $selected . '>' . $row['CategoryDescription'] . '</option>';
        }
        $stmtCategories->close();
        
    echo'</select>
    </form>';


    
    // Check if any books were found
    if ($result->num_rows > 0) {


        echo '<div class="grid-container">';

        //simple display of data with buttons in forms to activate the reserve function
        while ($row = $result->fetch_assoc()) {
            echo '<div class="book-item">';
            echo '<h3>' . $row['BookTitle'] . '</h3>';
            echo '<p>Author: ' . $row['Author'] . '</p>';
            echo '<form method="POST" action="reserve.php">';
            echo '<input type="hidden" name="isbn" value="' . $row['ISBN'] . '">';
            if ($row['Reserved']){
                echo '<button type="button" class="reserved">Reserved</button>';
            } else {
                echo '<button type="submit">Reserve</button>';
            }
            echo '</form>';
            echo '</div>';
        }

        echo '</div>';

        //Add pagination links
        echo '<div class="pagination centered">';
        for ($page = 1; $page <= $total_pages; $page++) {  
            //give the style of bolded if the page is active.
            $active = ($page == $page_number) ? 'style="font-weight:bold;"' : '';
            //save search query and category filter while navigating through the paginated pages
            echo '<a href="booklist.php?page=' . $page . '&search=' . urlencode($searchQuery) . '&categories=' . urlencode($categoryFilter) . '" ' . $active . '>' . $page . '</a> ';
        }
        echo '</div>';
        
    } else {
        echo '<p class="centered">No books found matching your search.</p>';
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>