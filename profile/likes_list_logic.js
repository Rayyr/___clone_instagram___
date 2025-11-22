
//for likes modal
const likeOpenButton = document.querySelector('.likeOpenDialog');
const likeCloseButton = document.querySelector('.likeCloseDialog');
const likeDialog = document.querySelector('.myLikeDialog');


// Add event listeners to all open buttons
likeOpenButton.addEventListener('click', () => {
    // Get the target dialog from data attribute or ID
    const dialogId = likeOpenButton.getAttribute('data-dialog');
    const dialog = document.getElementById(dialogId);
    //prevent the background(home page) from scrolling
    document.body.style.overflow='hidden';
    dialog.showModal(); // Opens the specific dialog as a modal
});




// Add event listeners to all close buttons
likeCloseButton.addEventListener('click', () => {
    // Find the parent dialog and close it
    const dialog = likeCloseButton.closest('.myLikeDialog');
    //restore the scrolling to home page
    document.body.style.overflow='auto';
    dialog.close(); // Closes the dialog
});



// Add click outside listener to all dialogs
likeDialog.addEventListener('click', (event) => {
    if (event.target === likeDialog) {
        //restore the scrolling to home page
        document.body.style.overflow='auto';
        likeDialog.close();
    }
});
