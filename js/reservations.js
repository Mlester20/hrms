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
                        
                        // Get current date and time for receipt
                        const now = new Date();
                        const receiptDate = now.toLocaleDateString();
                        const receiptTime = now.toLocaleTimeString();
                        const receiptNumber = 'R' + Math.floor(Math.random() * 10000).toString().padStart(4, '0');
                        
                        // CSS for elegant receipt styling
                        const styles = `
                            body {
                                font-family: 'Arial', sans-serif;
                                line-height: 1.6;
                                color: #333;
                                max-width: 800px;
                                margin: 0 auto;
                                padding: 20px;
                            }
                            .receipt-container {
                                border: 1px solid #ccc;
                                padding: 30px;
                                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                                background-color: #fff;
                            }
                            .receipt-header {
                                text-align: center;
                                padding-bottom: 20px;
                                border-bottom: 2px solid #8c8c8c;
                                margin-bottom: 20px;
                            }
                            .hotel-name {
                                font-size: 26px;
                                font-weight: bold;
                                color: #0f3460;
                                margin: 0;
                                text-transform: uppercase;
                                letter-spacing: 1px;
                            }
                            .receipt-title {
                                font-size: 20px;
                                font-weight: bold;
                                margin: 10px 0;
                                color: #666;
                            }
                            .receipt-meta {
                                display: flex;
                                justify-content: space-between;
                                margin-bottom: 25px;
                                font-size: 15px;
                            }
                            .receipt-meta div {
                                padding: 5px 0;
                            }
                            .reservation-details {
                                margin-bottom: 30px;
                            }
                            .section-title {
                                font-weight: bold;
                                margin-bottom: 10px;
                                border-bottom: 1px solid #eee;
                                padding-bottom: 5px;
                                color: #0f3460;
                            }
                            .detail-row {
                                display: flex;
                                margin-bottom: 10px;
                            }
                            .detail-label {
                                flex: 1;
                                font-weight: 500;
                                color: #555;
                            }
                            .detail-value {
                                flex: 2;
                            }
                            .guest-info {
                                margin-bottom: 30px;
                            }
                            .footer {
                                text-align: center;
                                margin-top: 30px;
                                font-size: 14px;
                                color: #666;
                                border-top: 1px solid #eee;
                                padding-top: 20px;
                            }
                            .logo {
                                font-size: 40px;
                                margin-bottom: 10px;
                                color: #0f3460;
                            }
                            .special-requests {
                                background-color: #f9f9f9;
                                border-left: 3px solid #0f3460;
                                padding: 10px;
                                margin-top: 10px;
                                font-style: italic;
                            }
                            .print-button {
                                text-align: center;
                                margin-top: 30px;
                            }
                            .print-button button {
                                background-color: #0f3460;
                                color: white;
                                border: none;
                                padding: 10px 20px;
                                border-radius: 4px;
                                cursor: pointer;
                                font-size: 16px;
                            }
                            .print-button button:hover {
                                background-color: #0a2647;
                            }
                            @media print {
                                .print-button {
                                    display: none;
                                }
                                body {
                                    padding: 0;
                                    margin: 0;
                                }
                                .receipt-container {
                                    box-shadow: none;
                                    border: none;
                                }
                            }
                        `;
                        
                        // HTML Content for the receipt
                        receiptWindow.document.write(`
                            <!DOCTYPE html>
                            <html lang="en">
                            <head>
                                <meta charset="UTF-8">
                                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                <title>Reservation Receipt</title>
                                <style>${styles}</style>
                                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
                            </head>
                            <body>
                                <div class="receipt-container">
                                    <div class="receipt-header">
                                        <div class="logo"><i class="fas fa-hotel"></i></div>
                                        <h1 class="hotel-name">Mark's Hotel & Restaurant</h1>
                                        <p>123 Luxury Avenue, Cityville • +1 (555) 123-4567 • www.markshotel.com</p>
                                        <h2 class="receipt-title">Reservation Confirmation</h2>
                                    </div>
                                    
                                    <div class="receipt-meta">
                                        <div>
                                            <strong>Receipt #:</strong> ${receiptNumber}<br>
                                            <strong>Date Issued:</strong> ${receiptDate}<br>
                                            <strong>Time:</strong> ${receiptTime}
                                        </div>
                                        <div>
                                            <strong>Reservation ID:</strong> ${reservation.reservation_id}<br>
                                            <strong>Status:</strong> <span style="color: green;">Confirmed</span>
                                        </div>
                                    </div>
                                    
                                    <div class="reservation-details">
                                        <h3 class="section-title">Reservation Details</h3>
                                        <div class="detail-row">
                                            <div class="detail-label">Table Number:</div>
                                            <div class="detail-value">${reservation.table_number}</div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-label">Table Capacity:</div>
                                            <div class="detail-value">${reservation.capacity} persons</div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-label">Date:</div>
                                            <div class="detail-value">${reservation.reservation_date}</div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-label">Time Slot:</div>
                                            <div class="detail-value">${reservation.time_slot}</div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-label">Guest Count:</div>
                                            <div class="detail-value">${reservation.guest_count} ${reservation.guest_count > 1 ? 'guests' : 'guest'}</div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-label">Special Requests:</div>
                                            <div class="detail-value">
                                                ${reservation.special_requests ? 
                                                    `<div class="special-requests">${reservation.special_requests}</div>` : 
                                                    'None'}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="guest-info">
                                        <h3 class="section-title">Guest Information</h3>
                                        <div class="detail-row">
                                            <div class="detail-label">Name:</div>
                                            <div class="detail-value">${reservation.name}</div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-label">Email:</div>
                                            <div class="detail-value">${reservation.email}</div>
                                        </div>
                                    </div>
                                    
                                    <div class="footer">
                                        <p>Thank you for choosing Mark's Hotel & Restaurant.</p>
                                        <p>We look forward to welcoming you on ${reservation.reservation_date} at ${reservation.time_slot}.</p>
                                        <p><small>For modifications or cancellations, please call us at least 4 hours before your reservation time.</small></p>
                                    </div>
                                    
                                    <div class="print-button">
                                        <button onclick="window.print()"><i class="fas fa-print"></i> Print Receipt</button>
                                    </div>
                                </div>
                                
                                <script>
                                    // Auto-print when page loads (optional)
                                    // window.onload = function() {
                                    //     window.print();
                                    // };
                                </script>
                            </body>
                            </html>
                        `);
                        receiptWindow.document.close();
                        // User can manually print now with the print button
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