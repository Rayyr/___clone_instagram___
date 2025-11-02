document.querySelectorAll('.comment-form').forEach(form => {
    form.addEventListener('submit', function(ev) {
        alert("The comment has been posted successfully!");
    });
});

// Enable post button when comment has text
document.querySelectorAll('.comment-input').forEach(input => {
    input.addEventListener('input', function() {
        const button = this.nextElementSibling;
        button.classList.toggle('active', this.value.trim() !== '');
    });
});






//for comments modal
const openButtons = document.querySelectorAll('.openDialog');
const closeButtons = document.querySelectorAll('.closeDialog');
const dialogs = document.querySelectorAll('.myDialog');


// Add event listeners to all open buttons
openButtons.forEach(button => {
    button.addEventListener('click', () => {
        // Get the target dialog from data attribute or ID
        const dialogId = button.getAttribute('data-dialog');
        const dialog = document.getElementById(dialogId);
        //prevent the background(home page) from scrolling
        document.body.style.overflow='hidden';
        dialog.showModal(); // Opens the specific dialog as a modal
    });
});

// Add event listeners to all close buttons
closeButtons.forEach(button => {
    button.addEventListener('click', () => {
        // Find the parent dialog and close it
        const dialog = button.closest('.myDialog');
        //restore the scrolling to home page
        document.body.style.overflow='auto';
        dialog.close(); // Closes the dialog
    });
});

// Add click outside listener to all dialogs
dialogs.forEach(dialog => {
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) {
            //restore the scrolling to home page
            document.body.style.overflow='auto';
            dialog.close();
        }
    });
});






//for likes modal
const likeOpenButtons = document.querySelectorAll('.likeOpenDialog');
const likeCloseButtons = document.querySelectorAll('.likeCloseDialog');
const likeDialogs = document.querySelectorAll('.myLikeDialog');


// Add event listeners to all open buttons
likeOpenButtons.forEach(button => {
    button.addEventListener('click', () => {
        // Get the target dialog from data attribute or ID
        const dialogId = button.getAttribute('data-dialog');
        const dialog = document.getElementById(dialogId);
        //prevent the background(home page) from scrolling
        document.body.style.overflow='hidden';
        dialog.showModal(); // Opens the specific dialog as a modal
    });
});

// Add event listeners to all close buttons
likeCloseButtons.forEach(button => {
    button.addEventListener('click', () => {
        // Find the parent dialog and close it
        const dialog = button.closest('.myLikeDialog');
        //restore the scrolling to home page
        document.body.style.overflow='auto';
        dialog.close(); // Closes the dialog
    });
});

// Add click outside listener to all dialogs
likeDialogs.forEach(dialog => {
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) {
            //restore the scrolling to home page
            document.body.style.overflow='auto';
            dialog.close();
        }
    });
});



//for displaying stories modal
//later !!!
const storyOpenButtons = document.querySelectorAll('.openStoryDialog');
const storyCloseButtons = document.querySelectorAll('.storyCloseDialog, .story-close');
const storyModals = document.querySelectorAll('.myStoryModal');

// Add event listeners to all open buttons
storyOpenButtons.forEach(button => {
    button.addEventListener('click', () => {
        const dialogId = button.getAttribute('data-dialog');
        const dialog = document.getElementById(dialogId);
        document.body.style.overflow = 'hidden';
        dialog.showModal();
    });
});

// Add event listeners to all close buttons
storyCloseButtons.forEach(button => {
    button.addEventListener('click', () => {
        const dialog = button.closest('.myStoryModal');
        document.body.style.overflow = 'auto';
        dialog.close();
    });
});

// Add click outside listener to all modals
storyModals.forEach(modal => {
    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            document.body.style.overflow = 'auto';
            modal.close();
        }
    });
});





//for create btn
<!-- ✅ JavaScript -->

const createDialog = document.getElementById("createDialog");
const openCreateDialog = document.getElementById("openCreateDialog");
const closeBtn = document.querySelector(".close-btn");
const fileInput = document.getElementById("fileInput");
const dialogActions = document.getElementById("dialogActions");
const fileName=document.getElementById("fileName");
const captionInput=document.getElementById("captionInput");

openCreateDialog.addEventListener("click", (e) => {
    e.preventDefault();
    document.body.style.overflow='hidden';
    createDialog.showModal();
});

closeBtn.addEventListener("click", () => {

    captionInput.value="";
    dialogActions.style.display = 'none';
    fileName.textContent="No file chosen";
    document.body.style.overflow='auto';
    createDialog.close();
});


createDialog.addEventListener("click", (event) => {
    // event.target === dialog means the click is on the backdrop, not inside the box
    if (event.target === createDialog) {
        captionInput.value="";
        dialogActions.style.display = 'none';
        fileName.textContent="No file chosen";
        document.body.style.overflow = "auto"; // restore page scroll if you disabled it
        createDialog.close();
    }
});

fileInput.addEventListener('change', function(event) {
    if (this.files.length > 0) {
        // Simply show the Share button without preview
        dialogActions.style.display = 'flex';
        fileName.textContent=this.files[0].name;
    }
    else
    {
        captionInput.value="";
        dialogActions.style.display = 'none';
        fileName.textContent="No file chosen";
    }
});

//display an alert msg after puplishing the post
document.querySelector('.create-post-form').addEventListener('submit', function(ev) {
    alert("The post has been posted successfully!");
});