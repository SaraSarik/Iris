<!DOCTYPE html> 
<html lang="en"> 
<head> 
<meta charset="UTF-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1"> 
<title>Form - Your Project Title</title> 
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" 
rel="stylesheet"> 
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
<form onsubmit="return validateForm()"> 
<div class="mb-3"> 
<label class="form-label">Name</label> 
<input type="text" id="name" class="form-control" placeholder="Enter Name"> 
</div> 
<div class="mb-3"> 
<label class="form-label">Email</label> 
<input type="email" id="email" class="form-control" placeholder="Enter Email"> 
</div> 
<div class="mb-3"> 
<label class="form-label">Phone</label> 
<input type="text" id="phone" class="form-control" placeholder="Enter Phone"> 
</div> 
<button type="submit" class="btn btn-success">Submit</button> 
<button type="reset" class="btn btn-secondary">Clear Form</button> 
</form> 
</div> 
<script> 
function validateForm() { 
let name = document.getElementById("name").value; 
let email = document.getElementById("email").value; 
let phone = document.getElementById("phone").value; 


if(empty($name) || empty($email) || empty($phone)){ 
die("All fields are required!"); 
} 
if(!filter_var($email, FILTER_VALIDATE_EMAIL)){ 
die("Invalid email format!"); 
} 
if(!is_numeric($phone) || strlen($phone) != 10){ 
die("Phone must be 10 digits!"); 
} 
<?php 
$sql_check = "SELECT * FROM entries WHERE email='$email'"; 
$result = $conn->query($sql_check); 
if($result->num_rows > 0){ 
die("Email already exists!"); 
} ?>
} 
</script> 
</body> 
</html>

<?php 
include 'config.php'; 
if ($_SERVER["REQUEST_METHOD"] == "POST") { 
$name = $_POST['name']; 
$email = $_POST['email']; 
$phone = $_POST['phone']; 
$sql = "INSERT INTO entries (name, email, phone) VALUES ('$name','$email','$phone')"; 
if ($conn->query($sql) === TRUE) { 
echo "<p>Entry added successfully!</p>"; 
} else { 
echo "Error: " . $sql . "<br>" . $conn->error; 
} 
$conn->close(); 
} 
?> 
