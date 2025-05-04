document.addEventListener('DOMContentLoaded', function () {
    const table = document.querySelector('table');

    table.addEventListener('click', function (e) {
        if (e.target.classList.contains('mark-done')) {
            const reservationId = e.target.dataset.id;
            handleAction('mark_done', reservationId);
        } else if (e.target.classList.contains('delete-reservation')) {
            const reservationId = e.target.dataset.id;
            if (confirm('Are you sure you want to delete this reservation?')) {
                handleAction('delete', reservationId);
            }
        } else if (e.target.classList.contains('print-receipt')) {
            const reservationId = e.target.dataset.id;
            handleAction('print_receipt', reservationId, true);
        }
    });

    function handleAction(action, reservationId, isPrint = false) {
        fetch('../controllers/reservationFunctions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action, reservation_id: reservationId })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (isPrint) {
                        const reservation = data.reservation;
                        const receiptWindow = window.open('', '_blank');
                        receiptWindow.document.write(`
                            <h1>Reservation Receipt</h1>
                            <p>Reservation ID: ${reservation.reservation_id}</p>
                            <p>Table Number: ${reservation.table_number}</p>
                            <p>Capacity: ${reservation.capacity} persons</p>
                            <p>Reservation Date: ${reservation.reservation_date}</p>
                            <p>Time Slot: ${reservation.time_slot}</p>
                            <p>Guest Count: ${reservation.guest_count}</p>
                            <p>Special Requests: ${reservation.special_requests || 'None'}</p>
                            <p>Reserved By: ${reservation.name}</p>
                            <p>Email: ${reservation.email}</p>
                        `);
                        receiptWindow.document.close();
                        receiptWindow.print();
                    } else {
                        alert(data.message);
                        location.reload();
                    }
                } else {
                    alert(data.message);
                }
            });
    }
});