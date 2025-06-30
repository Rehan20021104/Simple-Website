<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "users";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action']; // Determine if it's login or register
    $user = $conn->real_escape_string($_POST['username']);
    $pass = $_POST['password'];

    if ($action === 'register') {
        // Registration Logic
        $name = $conn->real_escape_string($_POST['name']);
        $email = $conn->real_escape_string($_POST['email']);
        $hashedPass = password_hash($pass, PASSWORD_BCRYPT); // Secure password

        $sql = "INSERT INTO users (name, email, username, password) VALUES ('$name', '$email', '$user', '$hashedPass')";
        if ($conn->query($sql) === TRUE) {
            header("Location: index.html");
            exit();
        } else {
            if ($conn->errno === 1062) {
                echo "Error: Username or email already exists!";
            } else {
                echo "Error: " . $conn->error;
            }
        }
    } elseif ($action === 'login') {
        // Login Logic
        $sql = "SELECT * FROM users WHERE username='$user'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($pass, $row['password'])) {
                header("Location: index.html");
                exit();
            } else {
                echo "Error: Invalid password!";
            }
        } else {
            echo "Error: No user found with that username!";
        }
    }
}

$conn->close();
?>
