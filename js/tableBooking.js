document.addEventListener('DOMContentLoaded', function () {
    const tablesContainer = document.querySelector('.tables-container');
    const reservationForm = document.getElementById('tableReservationForm');
    const selectedTableIdInput = document.getElementById('selectedTableId');
    const reserveButton = document.getElementById('reserveButton');
    const dateInput = document.getElementById('reservationDate');
    const timeSlotInput = document.getElementById('timeSlot');
    const availabilityTableBody = document.getElementById('availabilityTable');

    let selectedTable = null;
    let allTables = [];

    // Define standard time slots for the restaurant
    const timeSlots = [
        { value: '07:00', label: '7:00 AM' },
        { value: '08:00', label: '8:00 AM' },
        { value: '09:00', label: '9:00 AM' },
        { value: '10:00', label: '10:00 AM' },
        { value: '11:00', label: '11:00 AM' },
        { value: '12:00', label: '12:00 PM' },
        { value: '13:00', label: '1:00 PM' },
        { value: '14:00', label: '2:00 PM' },
        { value: '15:00', label: '3:00 PM' },
        { value: '16:00', label: '4:00 PM' },
        { value: '17:00', label: '5:00 PM' },
        { value: '18:00', label: '6:00 PM' },
        { value: '19:00', label: '7:00 PM' },
        { value: '20:00', label: '8:00 PM' },
        { value: '21:00', label: '9:00 PM' }
    ];

    // Fetch all tables from the database
    function fetchTables() {
        fetch('../api/tableBooking.php?t=' + new Date().getTime())
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    allTables = data.tables;
                    renderTables(data.tables);
                } else {
                    console.error('Error fetching tables:', data.error);
                    showAlert('Error fetching tables: ' + data.error, 'danger');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                showAlert('Error connecting to server. Please try again later.', 'danger');
            });
    }

    // Render tables in the layout
    function renderTables(tables) {
        tablesContainer.innerHTML = '';
        
        tables.forEach(table => {
            const tableDiv = document.createElement('div');
            tableDiv.className = `table-item ${table.capacity}-seater available`;
            tableDiv.dataset.tableId = table.table_id;
            tableDiv.dataset.capacity = table.capacity;
            tableDiv.dataset.location = table.location;
            tableDiv.style.top = `${table.position_y}px`;
            tableDiv.style.left = `${table.position_x}px`;

            tableDiv.innerHTML = `
                <div class="table-top">
                    <span class="table-number">${table.table_number || table.table_id}</span>
                </div>
                <div class="table-info">
                    <span>${table.capacity} seats</span>
                </div>
            `;

            tableDiv.addEventListener('click', () => selectTable(table));

            tablesContainer.appendChild(tableDiv);
        });
    }

    // Check if a specific table is available for the selected date and time
    function checkTableAvailability(tableId, date, timeSlot) {
        if (!date || !timeSlot) {
            return Promise.resolve(true);
        }

        return fetch(`../api/tableBooking.php?check_availability=1&table_id=${tableId}&date=${date}&t=` + new Date().getTime())
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Check if the selected time slot is in the reserved slots
                    return !data.reserved_slots.includes(timeSlot);
                }
                return true;
            })
            .catch(error => {
                console.error('Error checking availability:', error);
                return true;
            });
    }

    // Update table visual status based on date and time selection
    function updateTableStatus() {
        const date = dateInput.value;
        const timeSlot = timeSlotInput.value;

        if (!date || !timeSlot) {
            // Reset all tables to available
            document.querySelectorAll('.table-item').forEach(item => {
                item.classList.remove('reserved', 'selected');
                item.classList.add('available');
            });
            return;
        }

        // Check each table's availability for the selected date and time
        allTables.forEach(table => {
            checkTableAvailability(table.table_id, date, timeSlot)
                .then(isAvailable => {
                    const tableElement = document.querySelector(`[data-table-id="${table.table_id}"]`);
                    if (tableElement) {
                        if (isAvailable) {
                            tableElement.classList.remove('reserved');
                            tableElement.classList.add('available');
                        } else {
                            tableElement.classList.remove('available', 'selected');
                            tableElement.classList.add('reserved');
                            
                            // If this was the selected table, deselect it
                            if (selectedTable && selectedTable.table_id === table.table_id) {
                                deselectTable();
                            }
                        }
                    }
                });
        });
    }

    // Fetch and display availability summary for a specific date
    function fetchAvailability(date = null) {
        if (!date) {
            date = dateInput.value || new Date().toISOString().split('T')[0];
        }

        fetch(`../api/tableBooking.php?check_availability=1&date=${date}&t=` + new Date().getTime())
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderAvailabilityCalendar(data.availability);
                } else {
                    console.error('Error fetching availability:', data.error);
                }
            })
            .catch(error => {
                console.error('Fetch availability error:', error);
            });
    }

    // Render the availability calendar
    function renderAvailabilityCalendar(availability) {
        availabilityTableBody.innerHTML = '';

        // Create a map of reserved slots for quick lookup
        const availabilityMap = {};
        availability.forEach(slot => {
            availabilityMap[slot.time_slot] = slot;
        });

        timeSlots.forEach(slot => {
            const tr = document.createElement('tr');
            const slotData = availabilityMap[slot.value];
            
            let statusText = 'Available';
            let statusClass = 'text-success';
            
            if (slotData) {
                if (slotData.status === 'full') {
                    statusText = 'Fully Booked';
                    statusClass = 'text-danger';
                } else if (slotData.status === 'few_left') {
                    statusText = `Few Tables Left (${slotData.available_tables} available)`;
                    statusClass = 'text-warning';
                } else {
                    statusText = `Available (${slotData.available_tables} tables)`;
                    statusClass = 'text-success';
                }
            }

            tr.innerHTML = `
                <td>${slot.label}</td>
                <td class="${statusClass}"><strong>${statusText}</strong></td>
            `;

            availabilityTableBody.appendChild(tr);
        });
    }

    // Select a table
    function selectTable(table) {
        const date = dateInput.value;
        const timeSlot = timeSlotInput.value;

        // Check if date and time are selected
        if (!date || !timeSlot) {
            showAlert('Please select a date and time first', 'warning');
            return;
        }

        // Check if table is available for selected time
        checkTableAvailability(table.table_id, date, timeSlot)
            .then(isAvailable => {
                if (!isAvailable) {
                    showAlert('This table is already reserved for the selected time. Please choose another table or time.', 'warning');
                    return;
                }

                console.log('Selected table:', table);
                selectedTable = table;
                
                selectedTableIdInput.value = table.table_id;
                document.getElementById('displayTableId').textContent = table.table_number || table.table_id;
                document.getElementById('displayTableCapacity').textContent = table.capacity;
                document.getElementById('displayTableLocation').textContent = table.location;

                document.querySelector('.selected-table-info').classList.remove('d-none');
                reserveButton.disabled = false;

                // Highlight selected table
                document.querySelectorAll('.table-item').forEach(item => item.classList.remove('selected'));
                const selectedElement = document.querySelector(`[data-table-id="${table.table_id}"]`);
                if (selectedElement) {
                    selectedElement.classList.add('selected');
                }
            });
    }

    // Deselect table
    function deselectTable() {
        selectedTable = null;
        selectedTableIdInput.value = '';
        document.querySelector('.selected-table-info').classList.add('d-none');
        reserveButton.disabled = true;
        document.querySelectorAll('.table-item').forEach(item => item.classList.remove('selected'));
    }

    // Show alert message
    function showAlert(message, type = 'info') {
        // Create alert element
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(alertDiv);

        // Auto remove after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    // Handle reservation form submission
    reservationForm.addEventListener('submit', function (e) {
        e.preventDefault();

        if (!selectedTable) {
            showAlert('Please select a table first', 'warning');
            return;
        }

        // Show loading state
        reserveButton.disabled = true;
        const originalText = reserveButton.textContent;
        reserveButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

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
                    showAlert('Reservation successful! Your table has been reserved.', 'success');
                    
                    // Reset form and state
                    reservationForm.reset();
                    deselectTable();
                    
                    // Refresh data
                    fetchTables();
                    fetchAvailability(dateInput.value);
                    
                    reserveButton.textContent = originalText;
                } else {
                    showAlert('Error: ' + data.error, 'danger');
                    reserveButton.textContent = originalText;
                    reserveButton.disabled = false;
                }
            })
            .catch(error => {
                console.error('Submission error:', error);
                showAlert('Error submitting reservation. Please try again.', 'danger');
                reserveButton.textContent = originalText;
                reserveButton.disabled = false;
            });
    });

    // Event listeners for date and time changes
    dateInput.addEventListener('change', function() {
        fetchAvailability(this.value);
        updateTableStatus();
        if (selectedTable) {
            // Revalidate selected table
            const timeSlot = timeSlotInput.value;
            if (timeSlot) {
                checkTableAvailability(selectedTable.table_id, this.value, timeSlot)
                    .then(isAvailable => {
                        if (!isAvailable) {
                            deselectTable();
                            showAlert('Your selected table is not available for this date/time. Please select another.', 'info');
                        }
                    });
            }
        }
    });

    timeSlotInput.addEventListener('change', function() {
        updateTableStatus();
        if (selectedTable) {
            const date = dateInput.value;
            if (date) {
                checkTableAvailability(selectedTable.table_id, date, this.value)
                    .then(isAvailable => {
                        if (!isAvailable) {
                            deselectTable();
                            showAlert('Your selected table is not available for this time. Please select another.', 'info');
                        }
                    });
            }
        }
    });

    // Initial fetch
    fetchTables();
    fetchAvailability();
    
    // Set min date to today
    dateInput.min = new Date().toISOString().split('T')[0];
    
    // Refresh availability every 30 seconds
    setInterval(() => {
        const currentDate = dateInput.value || new Date().toISOString().split('T')[0];
        fetchAvailability(currentDate);
    }, 30000);
});