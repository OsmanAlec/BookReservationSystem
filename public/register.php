<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Reserve It Account</title>

    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/forms.css">

</head>
<body>

    <div class="container centered screentitle">
        <h1>Registration</strong></h1>
        <p>Please enter your details bellow to register. All fields are required.
        </p>
    </div>

    <div class="centered">
    <form method="post" class="centered form-sign" id="signupform">
        <input type="text" name="username" id="username" placeholder="Username" required>
        <input type="password" name="password" id="password" placeholder="Password" required>
        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
        
        <br>
        <input type="text" name="firstname" id="firstname" placeholder="First Name" required>
        <input type="text" name="lastname" id="lastname" placeholder="Last Name" required>
        
        <br>
        <input type="tel" name="telephone" id="telephone" placeholder="Land Line / Telephone" required>
        <input type="tel" name="mobile" id="mobile" placeholder="Mobile Phone" required=10>
        
        <br>
        <label for="addressline1">Address</label>
        <input type="text" name="addressline1" id="addressline1" placeholder="Address Line 1" required>
        <input type="text" name="addressline2" id="addressline2" placeholder="Address Line 2" required>
        <input type="text" name="city" id="city" placeholder="City/Town" required>
        
        <button type="submit">Register</button>
    </form>
    <a href="login.php" class="btn">Click here to login</a>
</div>



    <?php

    if($_SERVER["REQUEST_METHOD"] == "POST"){

        // Capture user inputs directly without XSS sanitization
        $u = trim($_POST["username"]);
        $p1 = $_POST["password"];
        $p2 = $_POST["confirm_password"];
        $fn = trim($_POST["firstname"]);
        $ln = trim($_POST["lastname"]);
        $tphone = trim($_POST["telephone"]);
        $mphone = trim($_POST["mobile"]);
        $a1 = trim($_POST["addressline1"]);
        $a2 = trim($_POST["addressline2"] ?? '');
        $c = trim($_POST["city"]);

        // Validate that all required fields are filled, despite the 'required' html tag
        if (empty($u) || empty($p1) || empty($p2) || empty($fn) || empty($ln) || empty($mphone) 
        || empty($tphone) || empty($a1) || empty($a2) || empty($c)) {
            echo "<script>alert('Please fill in all required fields.');</script>";
            die();
        }

        // Validate password length and confirmation
        if (strlen($p1) < 6) {
            echo "<script>alert('Password must be at least 6 characters long.');</script>";
            die();
        }

        if ($p1 !== $p2) {
            echo "<script>alert('Passwords do not match.');</script>";
            die();
        }

        // Validate mobile phone number (exactly 10 digits)
        if (strlen($mphone) !== 10 || strlen($tphone) !== 10) {
            echo "<script>alert('Mobile and telephone number must be exactly 10 digits long and numeric.');</script>";
            die();
        }
        

        include "../config/database.php";

        //Prepare the statement
        $stmt = $conn->prepare('INSERT INTO users VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');

        //put the parameters into the prepared statement
        $stmt->bind_param('sssssssss', $u, $p1, $fn, $ln, $a1, $a2, $c, $tphone, $mphone);

        // Execute the statement
        try {
            if ($stmt->execute())
            {   
                $stmt->close();
                $conn->close();
                header("Location: login.php"); //go to login if succesful registration
                exit;
            }
        } catch (mysqli_sql_exception $e) { //catch the error and display exception errors
           if ($e->getCode() == 1062) { // Error code for "Duplicate entry"
            echo "<script>alert('Username taken!');</script>"; //posbbile because of primary key
            } else {
                echo "Error: " . $stmt->error;
            }
        }
    }
    
    ?>
    
</body>
</html>
