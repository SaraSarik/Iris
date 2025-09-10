<?php
include 'config.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
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

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>View Entries</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
#entriesTable tbody tr:hover {
    background-color: #f2f2f2 !important;
}
th {
    cursor: pointer;
}
</style>
</head>
<body class="bg-light">

<!-- Mobile-Friendly Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Iris</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="form.php">Form</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container-fluid mt-4">
  <div class="row justify-content-center">
    <div class="col-12 col-lg-12">

      <!-- Search + Limit Form -->
      <form method="GET" class="mb-3 d-flex flex-column flex-sm-row gap-2">
        <input type="text" name="search" class="form-control" placeholder="Search by Name or Email" value="<?php echo htmlspecialchars($search); ?>">
        <select name="limit" class="form-select" onchange="this.form.submit()">
            <option value="5" <?php if($limit==5) echo 'selected'; ?>>5</option>
            <option value="10" <?php if($limit==10) echo 'selected'; ?>>10</option>
            <option value="20" <?php if($limit==20) echo 'selected'; ?>>20</option>
        </select>
      </form>

      <!-- Responsive Table -->
      <div class="table-responsive">
        <table class="table table-striped table-hover" id="entriesTable">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th class="sortable" data-type="string">Name <span class="sort-indicator"> ▲▼</span></th>
                    <th class="sortable" data-type="string">Email <span class="sort-indicator"> ▲▼</span></th>
                    <th class="sortable" data-type="numeric">Phone <span class="sort-indicator"> ▲▼</span></th>
                    <th>Created At</th>
                    <th>Actions</th>
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
                        <td><?php echo date("d-M-Y H:i", strtotime($row['created_at'])); ?></td>
                        <td>
                            <a href="update.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm mb-1">Edit</a>
                            <button class="btn btn-danger btn-sm mb-1" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo $row['id']; ?>">Delete</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center">No records found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <nav>
          <ul class="pagination justify-content-center">
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
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        Are you sure you want to delete this entry?
      </div>

      <div class="modal-footer">
        <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Yes, Delete</a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>

    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Sorting Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const table = document.getElementById('entriesTable');
  if (!table) return;

  const headers = Array.from(table.querySelectorAll('th.sortable'));

  headers.forEach(header => {
    const colIndex = Array.prototype.indexOf.call(header.parentElement.children, header);
    header.addEventListener('click', function () {
      const dir = header.getAttribute('data-sort-dir') === 'asc' ? 'desc' : 'asc';
      headers.forEach(h => h.removeAttribute('data-sort-dir'));
      header.setAttribute('data-sort-dir', dir);
      sortTableByColumn(table, colIndex, dir);
      updateIndicators();
    });
  });

  function sortTableByColumn(table, colIndex, dir = 'asc') {
    const tbody = table.tBodies[0];
    const rows = Array.from(tbody.querySelectorAll('tr'));

    const type = table.querySelectorAll('th')[colIndex].getAttribute('data-type') || 'string';

    rows.sort((a, b) => {
      let aVal = a.children[colIndex].textContent.trim();
      let bVal = b.children[colIndex].textContent.trim();

      if (type === 'numeric') {
        aVal = parseFloat(aVal.replace(/[^0-9.\-]/g,'')) || 0;
        bVal = parseFloat(bVal.replace(/[^0-9.\-]/g,'')) || 0;
      } else if (type === 'date') {
        aVal = Date.parse(aVal) || 0;
        bVal = Date.parse(bVal) || 0;
      } else {
        aVal = aVal.toLowerCase();
        bVal = bVal.toLowerCase();
      }

      if (aVal > bVal) return dir === 'asc' ? 1 : -1;
      if (aVal < bVal) return dir === 'asc' ? -1 : 1;
      return 0;
    });

    rows.forEach(r => tbody.appendChild(r));
  }

  function updateIndicators() {
    headers.forEach(h => {
      const span = h.querySelector('.sort-indicator');
      if (!span) return;
      const dir = h.getAttribute('data-sort-dir');
      span.textContent = dir === 'asc' ? ' ▲' : dir === 'desc' ? ' ▼' : ' ▲▼';
    });
  }
});

// Delete Modal JS
document.addEventListener("DOMContentLoaded", function() {
  const deleteModal = document.getElementById("deleteModal");
  const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");

  deleteModal.addEventListener("show.bs.modal", function(event) {
    const button = event.relatedTarget;
    const id = button.getAttribute("data-id");
    confirmDeleteBtn.setAttribute("href", "delete.php?id=" + id);
  });
});
</script>
</body>
</html>
