
    // Simple tab switching functionality
    document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
    });
});

    // Share button functionality
    document.querySelector('.share-btn').addEventListener('click', function() {
    alert('This would open the photo upload dialog in a real application.');
});


    // Enable clicking on Instagram logo to go back to home page
    document.querySelector('.logo').addEventListener('click', function(e) {
        window.location.href = '../home/home.php';
    });


    document.querySelector('.fa-home').addEventListener('click', function(e) {
        window.location.href = '../home/home.php';
    });