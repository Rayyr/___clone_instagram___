
//for likes modal
const followingOpenButton = document.querySelector('.followingOpenDialog');
const followingCloseButton = document.querySelector('.followingCloseDialog');
const followingDialog = document.querySelector('.myFollowingsDialog');


// Add event listeners to all open buttons
followingOpenButton.addEventListener('click', () => {
    // Get the target dialog from data attribute or ID
     //prevent the background(home page) from scrolling
    document.body.style.overflow='hidden';
    followingDialog.showModal(); // Opens the specific dialog as a modal
});




// Add event listener close buttons
followingCloseButton.addEventListener('click', () => {
    // Find the parent dialog and close it
    const dialog = followingCloseButton.closest('.myFollowingsDialog');
    //restore the scrolling to home page
    document.body.style.overflow='auto';
    dialog.close(); // Closes the dialog
});



// Add click outside listener
followingDialog.addEventListener('click', (event) => {
    if (event.target === followingDialog) {
        //restore the scrolling to home page
        document.body.style.overflow='auto';
        followingDialog.close();
    }
});

