document.addEventListener('DOMContentLoaded', function() {
    fetchTasks();
    
    // Add event listener for the filter dropdown if it exists
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        statusFilter.addEventListener('change', fetchTasks);
    }
});

function fetchTasks() {
    const tasksList = document.getElementById('tasksList');
    const statusFilter = document.getElementById('statusFilter');
    const filterValue = statusFilter ? statusFilter.value : 'all';
    
    // Show loading indicator
    tasksList.innerHTML = `
        <tr>
            <td colspan="5" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading tasks...</p>
            </td>
        </tr>
    `;
    
    fetch('../api/fetchTasks.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                if (data.data.length === 0) {
                    tasksList.innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                                    <p>No tasks assigned yet.</p>
                                </div>
                            </td>
                        </tr>
                    `;
                    return;
                }

                // Filter tasks if filter is applied
                let filteredTasks = data.data;
                if (filterValue !== 'all') {
                    filteredTasks = data.data.filter(task => task.status === filterValue);
                }

                // Show no results message if filtered results are empty
                if (filteredTasks.length === 0) {
                    tasksList.innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-filter fa-2x mb-3"></i>
                                    <p>No tasks match the selected filter.</p>
                                </div>
                            </td>
                        </tr>
                    `;
                    return;
                }

                // Render filtered tasks
                tasksList.innerHTML = filteredTasks.map((task, index) => `
                    <tr class="task-row ${index % 2 === 0 ? 'even-row' : 'odd-row'}">
                        <td class="task-title">
                            <div class="text-muted">${escapeHTML(task.title)}</div>
                        </td>
                        <td class="task-description">
                            <div class="description-text">${escapeHTML(task.description)}</div>
                        </td>
                        <td class="text-center align-middle">
                            <div class="deadline-badge">
                                <i class="far fa-calendar-alt me-1"></i> 
                                ${escapeHTML(task.deadline)}
                            </div>
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge bg-${task.status_class} status-badge">
                                <i class="fas ${getStatusIcon(task.status)} me-1"></i>
                                ${escapeHTML(task.status)}
                            </span>
                        </td>
                        <td class="text-center align-middle text-muted created-date">
                            <small>
                                <i class="fas fa-clock me-1"></i>
                                ${escapeHTML(task.created_at)}
                            </small>
                        </td>
                    </tr>
                `).join('');
                
                // Apply hover effect after rendering
                const rows = document.querySelectorAll('.task-row');
                rows.forEach(row => {
                    row.addEventListener('mouseover', function() {
                        this.classList.add('hover-effect');
                    });
                    row.addEventListener('mouseout', function() {
                        this.classList.remove('hover-effect');
                    });
                });
                
            } else {
                console.error('Error fetching tasks:', data.message);
                showError('Error fetching tasks: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Error connecting to server. Please try again later.');
        });
}

// Get appropriate icon based on status
function getStatusIcon(status) {
    switch(status) {
        case 'Completed':
            return 'fa-check-circle';
        case 'In Progress':
            return 'fa-spinner fa-pulse';
        case 'Pending':
            return 'fa-clock';
        default:
            return 'fa-exclamation-circle';
    }
}

// Escape HTML to prevent XSS attacks
function escapeHTML(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function showError(message) {
    const tasksList = document.getElementById('tasksList');
    tasksList.innerHTML = `
        <tr>
            <td colspan="5" class="text-center py-4">
                <div class="alert alert-danger d-inline-block" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>${message}
                </div>
            </td>
        </tr>
    `;
}