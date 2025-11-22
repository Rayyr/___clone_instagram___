//handle when i click on post to open it
    document.querySelectorAll('.post-item').forEach(post => {
        post.addEventListener('click', function(e) {
            e.preventDefault();
            const postId = this.getAttribute('data-post-id');
            const userWeWillVisitId = this.getAttribute('data-user-id');

            const urlParams = new URLSearchParams(window.location.search);
            const userId = urlParams.get('user_id');
           // window.alert(userWeWillVisitId);
            window.location.href=`../profile/profile.php?user_id=${userId}&user_we_will_visit=${userWeWillVisitId}&post_id=${postId}&scroll=0`;

        });
    });

    // Handle modal close
    document.querySelector('.modal-close').addEventListener('click', function(e) {
              e.preventDefault();
        const userWeWillVisitId = this.getAttribute('data-user-id');
        const urlParams = new URLSearchParams(window.location.search);
        const userId = urlParams.get('user_id');
        // window.alert(userWeWillVisitId);
        window.location.href=`../profile/profile.php?user_id=${userId}&user_we_will_visit=${userWeWillVisitId}`;

    });

//clicking outside the post modal then close it
const postModal=document.getElementById('postModal');
postModal.addEventListener('click',function(e){
    if(e.target===postModal){
        e.preventDefault();
         postModal.style.display='none';
        document.body.style.overflow = "auto";

        const userWeWillVisitId = this.getAttribute('data-user-id');
        const urlParams = new URLSearchParams(window.location.search);
        const userId = urlParams.get('user_id');

        window.location.href=`../profile/profile.php?user_id=${userId}&user_we_will_visit=${userWeWillVisitId}`;

     }

});
