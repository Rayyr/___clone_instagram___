document.addEventListener('DOMContentLoaded', function() {
    // Open modal when saved post item is clicked
    document.querySelectorAll('.saved-post-item').forEach(post => {
        post.addEventListener('click', function(e) {
            e.preventDefault();
            const postId = this.getAttribute('data-post-id');
            const modal = document.getElementById(`saved-modal-${postId}`);

            if (modal) {
                modal.showModal();
                document.body.style.overflow = 'hidden';
            }
        });
    });

    // Close modal when close button is clicked
    document.querySelectorAll('.saved-modal-close').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const postId = this.getAttribute('data-post-id');
            const modal = document.getElementById(`saved-modal-${postId}`);

            if (modal) {
                modal.close();
                document.body.style.overflow = 'auto';
            }
        });
    });

    // Close modal when clicking outside
    document.querySelectorAll('.saved-modal-dialog').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.close();
                document.body.style.overflow = 'auto';
            }
        });
    });


});