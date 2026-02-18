(function () {
    const checkIn  = document.getElementById('check_in');
    const checkOut = document.getElementById('check_out');

    if (!checkIn || !checkOut) return;

    // When check-in changes, push check-out minimum forward by 1 day
    checkIn.addEventListener('change', function () {
        if (!this.value) return;

        const nextDay = new Date(this.value);
        nextDay.setDate(nextDay.getDate() + 1);
        const minOut = nextDay.toISOString().split('T')[0];

        checkOut.min = minOut;

        // If current check-out is now invalid, reset it
        if (checkOut.value && checkOut.value <= this.value) {
            checkOut.value = minOut;
        }
    });

    // Trigger on page load in case values are pre-filled (back-button / GET)
    if (checkIn.value) {
        checkIn.dispatchEvent(new Event('change'));
    }
})();
