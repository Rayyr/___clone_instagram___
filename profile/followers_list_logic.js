
//for likes modal
const followerOpenButton = document.querySelector('.followerOpenDialog');
const followerCloseButton = document.querySelector('.followerCloseDialog');
const followerDialog = document.querySelector('.myFollowersDialog');


// Add event listeners to all open buttons
followerOpenButton.addEventListener('click', () => {
    // Get the target dialog from data attribute or ID

    //prevent the background(home page) from scrolling
    document.body.style.overflow='hidden';
    followerDialog.showModal(); // Opens the specific dialog as a modal
});




// Add event listener close buttons
followerCloseButton.addEventListener('click', () => {
    // Find the parent dialog and close it
    const dialog = followerCloseButton.closest('.myFollowersDialog');
    //restore the scrolling to home page
    document.body.style.overflow='auto';
    dialog.close(); // Closes the dialog
});



// Add click outside listener
followerDialog.addEventListener('click', (event) => {
    if (event.target === followerDialog) {
        //restore the scrolling to home page
        document.body.style.overflow='auto';
        followerDialog.close();
    }
});

