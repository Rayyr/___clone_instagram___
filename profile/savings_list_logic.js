
//for savings modal
const saveOpenButton = document.querySelector('.saveOpenDialog');
const saveCloseButton = document.querySelector('.saveCloseDialog');
const saveDialog = document.querySelector('.mySaveDialog');


// Add event listeners to all open buttons
saveOpenButton.addEventListener('click', () => {
    // Get the target dialog from data attribute or ID
    const dialogId = saveOpenButton.getAttribute('data-dialog');
    const dialog = document.getElementById(dialogId);
    //prevent the background(home page) from scrolling
    document.body.style.overflow='hidden';
    dialog.showModal(); // Opens the specific dialog as a modal
});




// Add event listeners to all close buttons
saveCloseButton.addEventListener('click', () => {
    // Find the parent dialog and close it
    const dialog = saveCloseButton.closest('.mySaveDialog');
    //restore the scrolling to home page
    document.body.style.overflow='auto';
    dialog.close(); // Closes the dialog
});



// Add click outside listener to all dialogs
saveDialog.addEventListener('click', (event) => {
    if (event.target === saveDialog) {
        //restore the scrolling to home page
        document.body.style.overflow='auto';
        saveDialog.close();
    }
});