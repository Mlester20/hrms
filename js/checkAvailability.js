(function () {
    const form = document.getElementById('availabilityForm');
    const checkIn = document.getElementById('check_in');
    const checkOut = document.getElementById('check_out');
    const resultsContainer = document.getElementById('availabilityResults'); 

    if (!checkIn || !checkOut) return;

    // --- Existing logic for min-date validation ---
    checkIn.addEventListener('change', function () {
        if (!this.value) return;
        const nextDay = new Date(this.value);
        nextDay.setDate(nextDay.getDate() + 1);
        const minOut = nextDay.toISOString().split('T')[0];
        checkOut.min = minOut;
        if (checkOut.value && checkOut.value <= this.value) {
            checkOut.value = minOut;
        }
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault(); 

        const formData = new FormData(form);
        const params = new URLSearchParams(formData).toString();

        // Show a loading state (optional)
        resultsContainer.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';

        fetch(`../api/check_availability.php?${params}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    resultsContainer.innerHTML = `
                        <div class="alert alert-danger rounded-3 mb-4">
                            <i class="fas fa-exclamation-circle me-2"></i>${data.error}
                        </div>`;
                    return;
                }
                renderRooms(data);
            })
            .catch(err => {
                console.error('Error:', err);
                resultsContainer.innerHTML = '<div class="alert alert-danger">An error occurred while fetching rooms.</div>';
            });
    });

    function renderRooms(data) {
        if (data.rooms.length === 0) {
            resultsContainer.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-bed fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No rooms available for these dates.</h4>
                </div>`;
            return;
        }

        let html = `
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h4 class="mb-0 fw-semibold">
                    <span class="badge bg-success me-2">${data.rooms.length}</span> Rooms Available
                </h4>
                <span class="text-muted small">
                    <i class="fas fa-moon me-1"></i> ${data.nights} nights
                </span>
            </div>
            <div class="row g-4">`;

        data.rooms.forEach(room => {
            const total = (room.price * data.nights).toLocaleString();
            const price = parseFloat(room.price).toLocaleString();
            const includes = room.includes ? room.includes.split(',') : [];
            
            html += `
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 room-card overflow-hidden">
                        <div class="room-img-wrapper position-relative" style="height:220px;">
                            <img 
                            src="${room.image ? '../uploads/' + room.image : '../images/loginbg.jpg'}" 
                            class="w-100 h-100"
                            style="object-fit:cover;"
                            onerror="this.src='../images/loginbg.jpg'"
                            >
                            <span class="position-absolute top-0 end-0 m-2 badge bg-primary rounded-pill">₱${price}/night</span>
                        </div>
                        <div class="card-body d-flex flex-column gap-2">
                            <h5 class="card-title fw-bold mb-1">${room.title}</h5>
                            <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="mb-0 text-muted small">Total for ${data.nights} nights</p>
                                    <p class="mb-0 fw-bold fs-5 text-primary">₱${total}</p>
                                </div>
                                <a href="roomBookings.php?room_id=${room.id}&check_in=${data.check_in}&check_out=${data.check_out}" class="btn btn-primary rounded-pill px-4">Book Now</a>
                            </div>
                        </div>
                    </div>
                </div>`;
        });

        html += `</div>`;
        resultsContainer.innerHTML = html;
    }

    if (checkIn.value) checkIn.dispatchEvent(new Event('change'));
})();