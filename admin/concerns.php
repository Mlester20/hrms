<?php
include '../controllers/fetchConcerns.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Concerns - <?php include '../components/title.php'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="stylesheet" href="../css/notifications.css">
    <link rel="shortcut icon" href="../images/final.png" type="image/x-icon">
    <style>
        .concern-card {
            transition: transform 0.2s;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .concern-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .bulk-actions {
            display: none;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .bulk-actions.visible {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .concern-checkbox {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1;
        }
        .created-time {
            font-size: 0.85rem;
            color: #6c757d;
        }
        .alert {
            animation: fadeOut 5s forwards;
        }
        @keyframes fadeOut {
            0% { opacity: 1; }
            90% { opacity: 1; }
            100% { opacity: 0; }
        }
        .user-info p {
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        .user-info i {
            width: 20px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <?php include '../components/header_admin.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="card-title text-success ">Customer Concerns</h4>
            <div>
                <button id="selectAllBtn" class="btn btn-outline-primary me-2">
                    <i class="fas fa-check-square"></i> Select All
                </button>
            </div>
        </div>

        <!-- Bulk Actions Bar -->
        <div id="bulkActions" class="bulk-actions">
            <span class="selected-count">0 items selected</span>
            <button id="bulkDeleteBtn" class="btn btn-danger" disabled>
                <i class="fas fa-trash"></i> Delete Selected
            </button>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form id="bulkForm" action="../controllers/fetchConcerns.php" method="POST">
            <div class="row g-4">
                <?php if (!empty($concerns)): ?>
                    <?php foreach ($concerns as $concern): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card concern-card h-100 position-relative">
                                <div class="concern-checkbox">
                                    <input type="checkbox" name="selected_concerns[]" 
                                           value="<?php echo $concern['id']; ?>" 
                                           class="form-check-input concern-check">
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title"><?php echo htmlspecialchars($concern['subject']); ?></h5>
                                        <span class="badge bg-primary">#<?php echo $concern['id']; ?></span>
                                    </div>
                                    <p class="card-text mb-4"><?php echo htmlspecialchars($concern['message']); ?></p>
                                    <div class="user-info mt-auto">
                                        <h6 class="text-muted mb-2">Customer Information</h6>
                                        <p>
                                            <i class="fas fa-user"></i>
                                            <?php echo htmlspecialchars($concern['name']); ?>
                                        </p>
                                        <p>
                                            <i class="fas fa-envelope"></i>
                                            <a href="mailto:<?php echo htmlspecialchars($concern['email']); ?>">
                                                <?php echo htmlspecialchars($concern['email']); ?>
                                            </a>
                                        </p>
                                        <?php if (!empty($concern['address'])): ?>
                                            <p>
                                                <i class="fas fa-map-marker-alt"></i>
                                                <?php echo htmlspecialchars($concern['address']); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <small class="created-time">
                                        <i class="fas fa-clock me-1"></i>
                                        Submitted: <?php echo date('F j, Y g:i A', strtotime($concern['created_at'])); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No concerns have been submitted yet.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <input type="hidden" name="bulk_delete" value="1">
        </form>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete the selected concerns? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/notifications.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllBtn = document.getElementById('selectAllBtn');
            const bulkActions = document.getElementById('bulkActions');
            const selectedCount = bulkActions.querySelector('.selected-count');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            const checkboxes = document.querySelectorAll('.concern-check');
            const bulkForm = document.getElementById('bulkForm');
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

            let isAllSelected = false;

            function updateSelectedCount() {
                const checkedBoxes = document.querySelectorAll('.concern-check:checked');
                const count = checkedBoxes.length;
                selectedCount.textContent = `${count} item${count !== 1 ? 's' : ''} selected`;
                bulkDeleteBtn.disabled = count === 0;
                bulkActions.classList.toggle('visible', count > 0);
            }

            selectAllBtn.addEventListener('click', () => {
                isAllSelected = !isAllSelected;
                checkboxes.forEach(checkbox => {
                    checkbox.checked = isAllSelected;
                });
                selectAllBtn.innerHTML = isAllSelected ? 
                    '<i class="fas fa-square"></i> Unselect All' : 
                    '<i class="fas fa-check-square"></i> Select All';
                updateSelectedCount();
            });

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedCount);
            });

            bulkDeleteBtn.addEventListener('click', () => {
                deleteModal.show();
            });

            document.getElementById('confirmDelete').addEventListener('click', () => {
                bulkForm.submit();
            });
        });
    </script>
</body>
</html>