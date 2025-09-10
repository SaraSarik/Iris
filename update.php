<?php 
include 'config.php'; 
if(isset($_GET['id'])){ 
$id = $_GET['id']; 
$sql = "SELECT * FROM entries WHERE id=$id"; 
$result = $conn->query($sql); 
$row = $result->fetch_assoc(); 
} 
if(isset($_POST['update'])){ 
$name = $_POST['name']; 
$email = $_POST['email']; 
$phone = $_POST['phone']; 
$sql = "UPDATE entries SET name='$name', email='$email', phone='$phone' WHERE 
id=$id"; 
if($conn->query($sql) === TRUE){ 
header("Location: view.php"); 
} else { 
echo "Error updating record: " . $conn->error; 
} 
} 
?> 
<!-- Bootstrap Update Form -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0">Update Entry</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Name</label>
                            <input type="text" name="name" value="<?php echo $row['name']; ?>" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" value="<?php echo $row['email']; ?>" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Phone</label>
                            <input type="text" name="phone" value="<?php echo $row['phone']; ?>" class="form-control" required>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="view.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" name="update" class="btn btn-success">Update</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
