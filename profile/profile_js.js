


    // Share button functionality  later ////////////////////////
    document.querySelector('.share-btn').addEventListener('click', function() {
    alert('This would open the photo upload dialog in a real application.');
});


    // Enable clicking on Instagram logo to go back to home page
    document.querySelector('.logo').addEventListener('click', function(e) {

      //i add the url param that in case we have multi users logged in then each one wil be redircted to his own home page ( logically)
      // Get user ID from current URL
        const urlParams = new URLSearchParams(window.location.search);
        const userId = urlParams.get('user_id');
        window.location.href = `../home/home.php?user_id=${userId}`;
//to gp to the original home page for the loggedin user
        // window.alert(userId);
        // window.location.href = '../home/home.php';
    });


    // Enable clicking on home icon to go back to home page
    document.querySelector('.fa-home').addEventListener('click', function(e) {

        //i add the url param that in case we have multi users logged in then each one wil be redircted to his own home page ( logically)
        // Get user ID from current URL
        const urlParams = new URLSearchParams(window.location.search);
        const userId = urlParams.get('user_id');
        window.location.href = `../home/home.php?user_id=${userId}`;
//to gp to the original home page for the loggedin user
        // window.location.href = '../home/home.php';

    });




    //////////////////////
    // Tab switching functionality
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');


// console.log(document.querySelector('.share-btn'));
            // Remove active class from all tabs and content
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

            // Add active class to clicked tab and corresponding content
            this.classList.add('active');
            document.querySelector(`.${tabName}-tab`).classList.add('active');
        });
    });

    // Share button functionality later////////////
    document.querySelectorAll('.share-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Redirect to create post page or open post creation modal

            window.location.href = 'create-post.php';
        });
    });

