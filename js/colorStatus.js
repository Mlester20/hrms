$(document).ready(function() {
    $('#reservationsTable').DataTable({
        order: [[8, 'desc']]
    });
});

function getStatusColor(status) {
    switch(status) {
        case 'pending': return '#ffc107';
        case 'confirmed': return '#28a745';
        case 'canceled': return '#dc3545';
        case 'complete': return '#0dcaf0';
        default: return '#ffffff';
    }
}

function getPaymentStatusColor(status) {
    switch(status) {
        case 'unpaid': return '#dc3545';
        case 'partially_paid': return '#ffc107';
        case 'paid': return '#28a745';
        default: return '#ffffff';
    }
}