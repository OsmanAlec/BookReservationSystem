<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login to Reserve It</title>

    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/forms.css">

</head>
<body>
    <div class="container centered screentitle">
        <h1>Welcome Back</h1>
        <p>Please enter your Login details bellow</p>
    </div>

    <div class="centered">
        <form method="post" class="centered form-sign">
            <input type="text" name="username" id="username" placeholder="Username" required>
            <input type="password" name="password" id="password" placeholder="Password" required>
            <br>
            <button type="submit">Sign-In</button>
        </form>
        <a href="register.php" class="btn">Click here to register</a>
    </div>

    <?php

        if ($_SERVER["REQUEST_METHOD"] == "POST"){

            $u = $_POST["username"];
            $p = $_POST["password"];

            include '../config/database.php';

            //Get the input from the user
            $username = $_POST['username'];  // assuming this is coming from a login form

            //Prepare the SQL statement with a placeholder
            $stmt = $conn->prepare('SELECT Username, Password FROM users WHERE username = ?');

            //Bind the input parameter to the prepared statement
            $stmt->bind_param('s', $username);

            //Execute the statement
            $stmt->execute();

            //Get the result from the executed statement
            //The reason we can do this is because of the restriction in the database on
            //the Usernames, they have to be unique and no two usernames will be the same.
            $result = $stmt->get_result();


            //Check if user and/or password exists
            if ($result->num_rows == 1){
                //Get the user data
                $user = $result->fetch_assoc();

                //Verify the password
                if ($p == $user['Password']){
                    $stmt->close();
                    $conn->close();

                    //Start the session and go to reserved books for user
                    session_start();
                    $_SESSION['username'] = $u;
                    header("Location: reservations.php");
                    exit();
                }
                else {
                    echo '<script>alert("Wrong Password")</script>';
                }
            }
            else {
                echo '<script>alert("Username Not Found")</script>';
            }


            $stmt->close();
            $conn->close();
        }


    ?>
    
</body>
</html>
