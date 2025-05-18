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