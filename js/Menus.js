document.addEventListener('DOMContentLoaded', function() {
    // Fetch menu items on page load
    fetchMenuItems();
    
    // Search functionality
    const searchInput = document.getElementById('menuSearch');
    searchInput.addEventListener('input', function() {
        filterMenuItems();
    });
    
    // Filter buttons
    const filterButtons = document.querySelectorAll('.btn-group button');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            // Filter items
            filterMenuItems();
        });
    });
});

// Global variable to store all menu items
let allMenuItems = [];

// Fetch menu items from API
function fetchMenuItems() {
    fetch('../api/get_menu.php')
        .then(response => response.json())
        .then(data => {
            allMenuItems = data;
            displayMenuItems(data);
        })
        .catch(error => {
            console.error('Error fetching menu items:', error);
            document.getElementById('menuContainer').innerHTML = 
                '<div class="col-12"><div class="alert alert-danger">Error loading menu items. Please try again later.</div></div>';
        });
}

// Display menu items
function displayMenuItems(items) {
    const menuContainer = document.getElementById('menuContainer');
    
    // Clear the container
    menuContainer.innerHTML = '';
    
    if (items.length === 0) {
        menuContainer.innerHTML = '<div class="col-12 no-items-found"><i class="fas fa-utensils fa-3x mb-3"></i><h3>No menu items found</h3><p>Try a different search term</p></div>';
        return;
    }
    
    // Loop through items and create cards
    items.forEach(item => {
        const card = createMenuCard(item);
        menuContainer.appendChild(card);
    });
}

// Create a menu card element
function createMenuCard(item) {
    const col = document.createElement('div');
    col.className = 'col-md-6 col-lg-4';
    
    const imageUrl = item.image ? '../uploads' + item.image : '../images/default-food.jpg';
    
    col.innerHTML = `
        <div class="card menu-card">
            <img src="${imageUrl}" class="card-img-top menu-image" alt="${item.menu_name}">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title">${item.menu_name}</h5>
                <p class="card-text flex-grow-1">${item.menu_description}</p>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <span class="menu-price">₱${parseFloat(item.price).toFixed(2)}</span>
                    <a href="../public/restaurantTableBooking.php" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-book me-1"></i> Book a Table now
                    </a>
                </div>
            </div>
        </div>
    `;
    
    return col;
}

// Filter menu items based on search input and filter selection
function filterMenuItems() {
    const searchTerm = document.getElementById('menuSearch').value.toLowerCase();
    const activeFilter = document.querySelector('.btn-group button.active').getAttribute('data-filter');
    
    // Filter by search term
    let filteredItems = allMenuItems.filter(item => 
        item.menu_name.toLowerCase().includes(searchTerm) || 
        item.menu_description.toLowerCase().includes(searchTerm)
    );
    
    // Apply sorting based on filter
    switch(activeFilter) {
        case 'price-asc':
            filteredItems.sort((a, b) => parseFloat(a.price) - parseFloat(b.price));
            break;
        case 'price-desc':
            filteredItems.sort((a, b) => parseFloat(b.price) - parseFloat(a.price));
            break;
        // Add more filter options as needed
    }
    
    // Display filtered items
    displayMenuItems(filteredItems);
}