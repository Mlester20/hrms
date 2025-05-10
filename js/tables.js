document.querySelectorAll('.edit-table').forEach(button => {
        button.addEventListener('click', function() {
        const table = JSON.parse(this.dataset.table);
        document.getElementById('edit_table_id').value = table.table_id;
        document.getElementById('edit_table_number').value = table.table_number;
        document.getElementById('edit_capacity').value = table.capacity;
        document.getElementById('edit_location').value = table.location;
        document.getElementById('edit_position_x').value = table.position_x;
        document.getElementById('edit_position_y').value = table.position_y;
    });
});

// Delete table functionality
document.querySelectorAll('.delete-table').forEach(button => {
        button.addEventListener('click', function() {
        const tableId = this.dataset.tableId;
        const tableNumber = this.dataset.tableNumber;
        document.getElementById('delete_table_id').value = tableId;
        document.getElementById('delete_table_number').textContent = tableNumber;
        new bootstrap.Modal(document.getElementById('deleteTableModal')).show();
    });
});