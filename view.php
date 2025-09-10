<?php 
include 'config.php'; 
$sql = "SELECT * FROM entries ORDER BY id DESC"; 
$result = $conn->query($sql); 
?> 

<!DOCTYPE html> 
<html lang="en"> 
<head> 
<meta charset="UTF-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1"> 
<title>View Entries - Your Project Title</title> 
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" 
rel="stylesheet"> 
</head> 
<body> 

<div class="container mt-5"> 
<h2>All Entries</h2> 

<?php


$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5; // entries per page
$offset = ($page - 1) * $limit;

// Count total entries for pagination
$count_sql = "SELECT COUNT(*) as total FROM entries WHERE name LIKE ? OR email LIKE ?";
$stmt_count = $conn->prepare($count_sql);
$search_param = "%$search%";
$stmt_count->bind_param("ss", $search_param, $search_param);
$stmt_count->execute();
$count_result = $stmt_count->get_result();
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Fetch entries for current page
$sql = "SELECT * FROM entries WHERE name LIKE ? OR email LIKE ? ORDER BY id DESC LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssii", $search_param, $search_param, $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();
?>

<!-- Search Form -->
<form method="GET" class="mb-3 d-flex align-items-center gap-2">
    <input type="text" name="search" class="form-control" placeholder="Search by Name or Email" value="<?php echo htmlspecialchars($search); ?>">

    <!-- Entries per page dropdown -->
    <select name="limit" class="form-select" onchange="this.form.submit()">
        <option value="5" <?php if($limit==5) echo 'selected'; ?>>5</option>
        <option value="10" <?php if($limit==10) echo 'selected'; ?>>10</option>
        <option value="20" <?php if($limit==20) echo 'selected'; ?>>20</option>
    </select>
</form>

<!-- Table -->
<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        <?php if($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td>
                        <?php 
                        // Format created_at: 09-Sep-2025 16:00
                        echo date("d-M-Y H:i", strtotime($row['created_at'])); 
                        ?>
                    </td>
                    <td> 
                    <a href="update.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Edit</a> 
                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" 
                    onclick="return confirm('Are you sure?');">Delete</a> 
                    </td> 
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-center">No records found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>


<!-- Pagination -->
<nav>
    <ul class="pagination">
        <li class="page-item <?php if($page <= 1) echo 'disabled'; ?>">
            <a class="page-link" href="?search=<?php echo urlencode($search); ?>&limit=<?php echo $limit; ?>&page=<?php echo $page-1; ?>">Previous</a>
        </li>
        <?php for($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?php if($i == $page) echo 'active'; ?>">
                <a class="page-link" href="?search=<?php echo urlencode($search); ?>&limit=<?php echo $limit; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?php if($page >= $total_pages) echo 'disabled'; ?>">
            <a class="page-link" href="?search=<?php echo urlencode($search); ?>&limit=<?php echo $limit; ?>&page=<?php echo $page+1; ?>">Next</a>
        </li>
    </ul>
</nav>

</div> 
</body> 
</html>