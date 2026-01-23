document.addEventListener('DOMContentLoaded', function () {
    const tablesContainer = document.querySelector('.tables-container');
    const reservationForm = document.getElementById('tableReservationForm');
    const selectedTableIdInput = document.getElementById('selectedTableId');
    const reserveButton = document.getElementById('reserveButton');

    // Fetch tables and render them
    function fetchTables() {
        // Add a timestamp to prevent caching
        fetch('../api/tableBooking.php?t=' + new Date().getTime())
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    console.log('Tables data:', data.tables); // Debug: Log the tables data
                    
                    tablesContainer.innerHTML = '';
                    data.tables.forEach(table => {
                        const tableDiv = document.createElement('div');
                        tableDiv.className = `table-item ${table.capacity}-seater ${table.status}`;
                        tableDiv.dataset.tableId = table.table_id;
                        tableDiv.dataset.capacity = table.capacity;
                        tableDiv.dataset.location = table.location;
                        tableDiv.dataset.status = table.status; // Store status in dataset
                        tableDiv.style.top = `${table.position_y}px`;
                        tableDiv.style.left = `${table.position_x}px`;

                        // Modified display to make table numbers more visible
                        tableDiv.innerHTML = `
                            <div class="table-top">
                                <span class="table-number">${table.table_number}</span>
                            </div>
                        `;

                        // Only add click event for available tables (and cancelled tables which are treated as available)
                        if (table.status === 'available' || table.status === 'cancelled') {
                            tableDiv.addEventListener('click', () => selectTable(table));
                        }

                        tablesContainer.appendChild(tableDiv);
                    });
                    
                    // Debug: Check if we have tables with reserved status
                    const reservedTables = data.tables.filter(t => t.status === 'reserved');
                    console.log('Reserved tables count:', reservedTables.length);
                    
                } else {
                    console.error('Error fetching tables:', data.error);
                    alert('Error fetching tables: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Error connecting to server. Please try again later.');
            });
    }

    // Select a table
    function selectTable(table) {
        console.log('Selected table:', table); // Debug: Log the selected table
        
        selectedTableIdInput.value = table.table_id;
        document.getElementById('displayTableId').textContent = table.table_number;
        document.getElementById('displayTableCapacity').textContent = table.capacity;
        document.getElementById('displayTableLocation').textContent = table.location;

        document.querySelector('.selected-table-info').classList.remove('d-none');
        reserveButton.disabled = false;

        // Highlight selected table
        document.querySelectorAll('.table-item').forEach(item => item.classList.remove('selected'));
        document.querySelector(`[data-table-id="${table.table_id}"]`).classList.add('selected');
    }

    // Handle reservation form submission
    reservationForm.addEventListener('submit', function (e) {
        e.preventDefault();

        // Show loading state
        reserveButton.disabled = true;
        reserveButton.textContent = 'Processing...';

        const formData = new FormData(reservationForm);
        const data = Object.fromEntries(formData.entries());

        fetch('../api/tableBooking.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Reservation successful!');
                    fetchTables(); // Refresh tables
                    reservationForm.reset();
                    document.querySelector('.selected-table-info').classList.add('d-none');
                    reserveButton.textContent = 'Reserve Table';
                    reserveButton.disabled = true;
                } else {
                    alert('Error making reservation: ' + data.error);
                    reserveButton.textContent = 'Reserve Table';
                    reserveButton.disabled = false;
                }
            })
            .catch(error => {
                console.error('Submission error:', error);
                alert('Error submitting form. Please try again.');
                reserveButton.textContent = 'Reserve Table';
                reserveButton.disabled = false;
            });
    });

    // Initial fetch
    fetchTables();
    
    // Refresh tables every 30 seconds
    setInterval(fetchTables, 30000);
});