<?php

//Create connection
$mysqli = new mysqli('localhost', 'username', 'password', 'videogamedatabase');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];
    $email = $_POST["email"];
    $phoneNumber = $_POST["phoneNumber"];
    $userName = $_POST["userName"];
    $password = $_POST["password"];
    $password2 = $_POST["password2"];

    // Validate input
    $errors = [];
    if (empty($firstname)) {
        $errors[] = "First name is required";
    }
    if (empty($lastname)) {
        $errors[] = "Last name is required";
    }
    if (empty($email)) {
        $errors[] = "Email address is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    if (empty($phoneNumber)) {
        $errors[] = "Phone number is required";
    }
    if (empty($userName)) {
        $errors[] = "Username is required";
    }
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif ($password !== $password2) {
        $errors[] = "Passwords do not match";
    }

    // Check connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    if (count($errors) === 0) {
        // Check if the username already exists in the database
        $stmt = $mysqli->prepare("SELECT * FROM users WHERE userName = ?");
        $stmt->bind_param("s", $userName);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $errors[] = "Username already exists";
        } else {
            // Hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Store user information in database
            $stmt = $mysqli->prepare("INSERT INTO users (firstname, lastname, email, phoneNumber, userName, password_hash) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $firstname, $lastname, $email, $phoneNumber, $userName, $password_hash);
            if ($stmt->execute()) {
                echo "Registration successful";
            } else {
                echo "Error: " . $mysqli->error;
            }
        }
    }

    if (count($errors) > 0) {
        // Output error messages
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
    }
}