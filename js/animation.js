document.addEventListener('DOMContentLoaded', () => {
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.2
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                if (entry.target.classList.contains('restaurant-content')) {
                    const features = entry.target.querySelectorAll('.feature-item');
                    features.forEach((feature, index) => {
                        setTimeout(() => {
                            feature.classList.add('visible');
                        }, index * 200);
                    });
                }
            }
        });
    }, observerOptions);

    // Hero Section Animation
    const heroContent = document.querySelector('.hero-content');
    if (heroContent) {
        heroContent.classList.add('fade-in');
        observer.observe(heroContent);
    }

    // About Section Animations
    const aboutContent = document.querySelector('.about-content');
    const aboutImages = document.querySelector('.about-images');
    if (aboutContent) {
        aboutContent.classList.add('slide-in-left');
        observer.observe(aboutContent);
    }
    if (aboutImages) {
        aboutImages.classList.add('slide-in-right');
        observer.observe(aboutImages);
    }

    // Restaurant Section Animations
    const restaurantContent = document.querySelector('.restaurant-content');
    const restaurantImages = document.querySelectorAll('.image-container');
    if (restaurantContent) {
        restaurantContent.classList.add('slide-in-left');
        observer.observe(restaurantContent);
    }
    restaurantImages.forEach((img, index) => {
        img.classList.add('scale-in', `delay-${index + 1}`);
        observer.observe(img);
    });

    // Gallery Section Animation
    const galleryItems = document.querySelectorAll('#room-gallery-container > div');
    galleryItems.forEach((item, index) => {
        item.classList.add('scale-in', `delay-${index % 3 + 1}`);
        observer.observe(item);
    });
});