<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Form - Your Project Title</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Iris</a>
        <div>
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="form.php">Form</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
            </ul>
        </div>
    </div>
</nav>  

<div class="container mt-5">
    <h2 class="mb-4">Add Entry</h2>
    <form method="POST" action="form.php" onsubmit="return validateForm()">
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" id="name" name="name" class="form-control" oninput="this.value=this.value.toUpperCase()">
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="Enter Email">
        </div>
        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" id="phone" name="phone" class="form-control" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
        </div>
        <button type="submit" class="btn btn-success">Submit</button>
        <button type="reset" class="btn btn-secondary">Clear Form</button>
        <a href="view.php" class="btn btn-success text-white text-decoration-none">View Entries</a>
</form>
</div>

<script>
// Client-side validation
function validateForm() {
    let name = document.getElementById("name").value.trim();
    let email = document.getElementById("email").value.trim();
    let phone = document.getElementById("phone").value.trim();

    if(name === "" || email === "" || phone === "") {
        alert("All fields are required!");
        return false;
    }
    let emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,}$/;
    if(!email.match(emailPattern)) {
        alert("Invalid email format!");
        return false;
    }
    if(isNaN(phone) || phone.length !== 10) {
        alert("Phone must be 10 digits!");
        return false;
    }
    return true;
}

// Name length feedback
document.getElementById("name").addEventListener("input", function() {
    if(this.value.length < 3){
        this.style.borderColor = "red";
    } else {
        this.style.borderColor = "green";
    }
});
</script>
</body>
</html>

<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    // Server-side validation
    if(empty($name) || empty($email) || empty($phone)) {
        die("All fields are required!");
    }
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format!");
    }
    if(!is_numeric($phone) || strlen($phone) != 10) {
        die("Phone must be 10 digits!");
    }

    // Check if email already exists
    $sql_check = "SELECT * FROM entries WHERE email = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if($result->num_rows > 0){
        die("Email already exists!");
    }

    // Insert data safely
    $sql = "INSERT INTO entries (name, email, phone) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $email, $phone);

    if ($stmt->execute()) {
        echo "<p class='text-success text-center mt-3'>Entry added successfully!</p>";
    } else {
        echo "<p class='text-danger text-center mt-3'>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
    $conn->close();
}
?>
