document.addEventListener('DOMContentLoaded', function () {
    const roomGalleryContainer = document.getElementById('room-gallery-container');
    
    if (!roomGalleryContainer) {
        console.error('Room gallery container not found!');
        return;
    }

    // Fetch room data from the API
    fetch('../api/getRooms.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(rooms => {
            if (!Array.isArray(rooms)) {
                if (rooms.error) {
                    throw new Error(rooms.error);
                }
                throw new Error('Invalid data format received');
            }
            
            if (rooms.length === 0) {
                roomGalleryContainer.innerHTML = '<div class="col-12"><p>No rooms available at this time.</p></div>';
                return;
            }

            rooms.forEach(room => {
                // Create room card element
                const roomCard = document.createElement('div');
                roomCard.classList.add('col-md-4', 'mb-4');
                
                // Ensure room.images is an array
                const images = Array.isArray(room.images) ? room.images : [];
                
                // Create HTML content for the room card
                let cardHTML = `
                <div class="room-card">
                    <div class="swiper-container">
                        <div class="swiper-wrapper">`;
                
                // Add images to swiper
                if (images.length > 0) {
                    images.forEach(image => {
                        cardHTML += `
                            <div class="swiper-slide">
                                <img src="../uploads/${image}" alt="${room.title}" class="img-fluid">
                            </div>`;
                    });
                } else {
                    // No images available
                    cardHTML += `
                        <div class="swiper-slide">
                            <div class="no-image">No Image Available</div>
                        </div>`;
                }
                
                // Complete the card structure
                cardHTML += `
                        </div>
                        <!-- Add navigation controls -->
                        <div class="swiper-pagination"></div>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    </div>
                    <div class="room-info">
                        <h3>${room.title || 'Unnamed Room'}</h3>
                        <p>From ₱${room.price || '0'} per night</p>
                        <a href="roomBookings.php" class="room-details-btn">Book Now</a>
                    </div>
                </div>`;
                
                roomCard.innerHTML = cardHTML;
                roomGalleryContainer.appendChild(roomCard);
            });
            
            // Initialize Swiper for all containers
            const swiperContainers = document.querySelectorAll('.swiper-container');
            swiperContainers.forEach(container => {
                new Swiper(container, {
                    loop: true,
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false,
                    },
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    }
                });
            });
        })
        .catch(error => {
            console.error('Error fetching rooms:', error);
            roomGalleryContainer.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-danger">
                        Failed to load rooms. Please try again later.
                    </div>
                </div>`;
        });
});