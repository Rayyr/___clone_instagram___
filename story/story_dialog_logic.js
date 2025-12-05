// // Add click events to all story buttons
// document.querySelectorAll('.story-handler-btn').forEach(button => {
//     button.addEventListener('click', function() {
//         const storyId = this.getAttribute('data-story-id');
//         const userId = this.getAttribute('data-user-id');
//         const username = this.getAttribute('data-username');
//         const profilePic = this.getAttribute('data-profile-pic');
//         const storyMedia = this.getAttribute('data-story-media');
//
//         // Update dialog content with the clicked story's data
//         document.getElementById('storyUsername').textContent = username;
//         document.getElementById('storyUserAvatar').src = profilePic;
//         document.getElementById('storyMedia').src = storyMedia;
//
//         //prevent the background(home page) from scrolling
//         document.body.style.overflow='hidden';
//
//         // Show the dialog
//         document.getElementById('storyDialog').showModal();
//
//
//         // Start progress animation (optional)
//         startStoryProgress();
//     });
// });
//
// // Close dialog when close button is clicked
// document.getElementById('closeStoryBtn').addEventListener('click', function() {
//     //restore the scrolling to home page
//     document.body.style.overflow='auto';
//
//     document.getElementById('storyDialog').close();
//     resetStoryProgress();
// });
//
// // Close dialog when clicking outside content
// document.getElementById('storyDialog').addEventListener('click', function(e) {
//     if (e.target === this) {
//         //restore the scrolling to home page
//         document.body.style.overflow='auto';
//
//         this.close();
//         resetStoryProgress();
//     }
// });
//
// // Close on Escape key
// document.addEventListener('keydown', function(e) {
//     if (e.key === 'Escape') {
//         //restore the scrolling to home page
//         document.body.style.overflow='auto';
//         document.getElementById('storyDialog').close();
//         resetStoryProgress();
//     }
// });
//
// // Progress bar functions (optional)
// function startStoryProgress() {
//     const progressFill = document.querySelector('.story-progress-fill');
//     progressFill.style.width = '0%';
//     setTimeout(() => {
//         progressFill.style.width = '100%';
//         progressFill.style.transition = 'width 5s linear';
//     }, 100);
// }
//
// function resetStoryProgress() {
//     const progressFill = document.querySelector('.story-progress-fill');
//     progressFill.style.width = '0%';
//     progressFill.style.transition = 'none';
// }
//























//
// // Story management variables
// let progressInterval;
// let isPaused = false;
// let progressStartTime = 0;
// let progressDuration = 5000; // 5 seconds
// let remainingTime = 5000;
//
// // Add click events to all story buttons
// document.querySelectorAll('.story-handler-btn').forEach(button => {
//     button.addEventListener('click', function() {
//         const storyId = this.getAttribute('data-story-id');
//         const userId = this.getAttribute('data-user-id');
//         const username = this.getAttribute('data-username');
//         const profilePic = this.getAttribute('data-profile-pic');
//         const storyMedia = this.getAttribute('data-story-media');
//
//         // Update dialog content with the clicked story's data
//         document.getElementById('storyUsername').textContent = username;
//         document.getElementById('storyUserAvatar').src = profilePic;
//         document.getElementById('storyMedia').src = storyMedia;
//
//         // Prevent the background (home page) from scrolling
//         document.body.style.overflow = 'hidden';
//
//         // Show the dialog
//         document.getElementById('storyDialog').showModal();
//
//         // Start progress animation
//         startStoryProgress();
//     });
// });
//
// // Close dialog when close button is clicked
// document.getElementById('closeStoryBtn').addEventListener('click', function() {
//     // Restore the scrolling to home page
//     document.body.style.overflow = 'auto';
//     document.getElementById('storyDialog').close();
//     resetStoryProgress();
// });
//
// // Close dialog when clicking outside content
// document.getElementById('storyDialog').addEventListener('click', function(e) {
//     if (e.target === this) {
//         // Restore the scrolling to home page
//         document.body.style.overflow = 'auto';
//         this.close();
//         resetStoryProgress();
//     }
// });
//
// // Close on Escape key
// document.addEventListener('keydown', function(e) {
//     if (e.key === 'Escape') {
//         // Restore the scrolling to home page
//         document.body.style.overflow = 'auto';
//         document.getElementById('storyDialog').close();
//         resetStoryProgress();
//     }
// });
//
// // Pause/Resume on story media click
// document.getElementById('storyMedia').addEventListener('click', function() {
//     if (isPaused) {
//         resumeStoryProgress();
//     } else {
//         pauseStoryProgress();
//     }
// });
//
// // Pause progress
// function pauseStoryProgress() {
//     if (progressInterval && !isPaused) {
//         clearInterval(progressInterval);
//         isPaused = true;
//
//         // Calculate remaining time
//         const elapsedTime = Date.now() - progressStartTime;
//         remainingTime = progressDuration - elapsedTime;
//
//         // Remove the smooth transition to freeze the progress bar
//         const progressFill = document.querySelector('.story-progress-fill');
//         progressFill.style.transition = 'none';
//
//      }
// }
//
// // Resume progress
// function resumeStoryProgress() {
//     if (isPaused) {
//         isPaused = false;
//         progressStartTime = Date.now() - (progressDuration - remainingTime);
//
//         // Add smooth transition back
//         const progressFill = document.querySelector('.story-progress-fill');
//         progressFill.style.transition = `width ${remainingTime}ms linear`;
//         progressFill.style.width = '100%';
//
//         // Set timeout to close when remaining time completes
//         progressInterval = setTimeout(() => {
//             closeStoryAndReset();
//         }, remainingTime);
//
//
//     }
// }
//
// // Progress bar functions
// function startStoryProgress() {
//     const progressFill = document.querySelector('.story-progress-fill');
//
//     // Reset progress bar
//     progressFill.style.width = '0%';
//     progressFill.style.transition = 'none';
//
//     // Reset variables
//     isPaused = false;
//     remainingTime = progressDuration;
//     progressStartTime = Date.now();
//
//     // Clear any existing interval
//     if (progressInterval) {
//         clearInterval(progressInterval);
//     }
//
//     // Small delay to ensure DOM is updated
//     setTimeout(() => {
//         // Start smooth animation
//         progressFill.style.transition = `width ${progressDuration}ms linear`;
//         progressFill.style.width = '100%';
//
//         // Set timeout to close when progress completes
//         progressInterval = setTimeout(() => {
//             closeStoryAndReset();
//         }, progressDuration);
//     }, 50);
// }
//
// function resetStoryProgress() {
//     const progressFill = document.querySelector('.story-progress-fill');
//     progressFill.style.width = '0%';
//     progressFill.style.transition = 'none';
//
//     isPaused = false;
//     remainingTime = progressDuration;
//
//     if (progressInterval) {
//         clearTimeout(progressInterval);
//         progressInterval = null;
//     }
// }
//
// function closeStoryAndReset() {
//     document.body.style.overflow = 'auto';
//     document.getElementById('storyDialog').close();
//     resetStoryProgress();
//
// }


























// Story management variables
let progressInterval;
let isPaused = false;
let progressStartTime = 0;
let progressDuration = 5000; // 5 seconds
let currentProgress = 0;
let currentStoryId = null;
let viewerId = null;


// Add click events to all story buttons
document.querySelectorAll('.story-handler-btn').forEach(button => {
    button.addEventListener('click', function(e) {
        // Check if the click was on the username span
        if(button.classList.contains('current-user-story')===false && e.target.classList.contains('current-user-story')===false) {//to gurntee that if the current user click on his story-createtion or his username to not open them .... so this only will be for story creation process
            if (e.target.classList.contains('story-username')) {
                // Redirect to profile
                const storyUserId = this.getAttribute('data-user-id');
                const viewerUserId = this.getAttribute('data-viewer-id');
                window.location.href = `../profile/profile.php?user_id=${viewerUserId}&user_we_will_visit=${storyUserId}`;
            } else {


                currentStoryId = this.getAttribute('data-story-id');
                viewerId = this.getAttribute('data-viewer-id');

                const username = this.getAttribute('data-username');
                const profilePic = this.getAttribute('data-profile-pic');
                const storyMedia = this.getAttribute('data-story-media');

                // Update dialog content with the clicked story's data
                document.getElementById('storyUsername').textContent = username;
                document.getElementById('storyUserAvatar').src = profilePic;
                document.getElementById('storyMedia').src = storyMedia;

                // Prevent the background (home page) from scrolling
                document.body.style.overflow = 'hidden';

                // Show the dialog
                document.getElementById('storyDialog').showModal();

                // Start progress animation
                startStoryProgress();
            }
        }
        //means the current user clicks on his username to create stry
        else {
            handleStoryCreation();
        }
    });
});



function handleStoryCreation(){

//for create btn
    <!-- ✅ JavaScript -->

    const openCreateDialog=document.getElementsByClassName('current-user-story')[0];
    const createStoryDialog = document.getElementById("createStoryDialog");
    const closeStoryBtn = document.querySelector('.close-story-btn');
    const fileInput = document.getElementById("fileStoryInput");
    const dialogActions = document.getElementById("storyDialogActions");
    const fileName=document.getElementById("fileStoryName");

    openCreateDialog.addEventListener("click", (e) => {
        e.preventDefault();
        document.body.style.overflow='hidden';
        createStoryDialog.showModal();
    });

    closeStoryBtn.addEventListener("click", () => {

        dialogActions.style.display = 'none';
        fileName.textContent="No file chosen";
        document.body.style.overflow='auto';
        createStoryDialog.close();
    });


    createStoryDialog.addEventListener("click", (event) => {
        // event.target === dialog means the click is on the backdrop, not inside the box
        if (event.target === createStoryDialog) {

            dialogActions.style.display = 'none';
            fileName.textContent="No file chosen";
            document.body.style.overflow = "auto"; // restore page scroll if you disabled it
            createStoryDialog.close();
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
            dialogActions.style.display = 'none';
            fileName.textContent="No image chosen";
        }
    });

//display an alert msg after puplishing the story
    document.querySelector('.create-story-form').addEventListener('submit', function(ev) {
         alert('The story has been shared successfully!');
    });
}










// Close dialog when close button is clicked
document.getElementById('closeStoryBtn').addEventListener('click', function() {
    // Restore the scrolling to home page
    document.body.style.overflow = 'auto';
    markStoryAsViewed(currentStoryId,viewerId);
    document.getElementById('storyDialog').close();
    resetStoryProgress();
});




// Close dialog when clicking outside content
document.getElementById('storyDialog').addEventListener('click', function(e) {
    if (e.target === this) {
        markStoryAsViewed(currentStoryId,viewerId);
        // Restore the scrolling to home page
        document.body.style.overflow = 'auto';
        this.close();
        resetStoryProgress();
    }
});



// Pause/Resume on story media click
document.getElementById('storyMedia').addEventListener('click', function() {
    if (isPaused) {
        resumeStoryProgress();
    } else {
        pauseStoryProgress();
    }
});



// Pause progress
function pauseStoryProgress() {
    if (!isPaused && progressInterval) {
        isPaused = true;
        clearInterval(progressInterval);

        // Calculate current progress
        const elapsedTime = Date.now() - progressStartTime;
        currentProgress = Math.min((elapsedTime / progressDuration) * 100, 100);
    }
}



// Resume progress
function resumeStoryProgress() {
    if (isPaused) {
        isPaused = false;

        // Calculate remaining time based on current progress
        const remainingTime = progressDuration * (1 - currentProgress / 100);
        progressStartTime = Date.now() - (progressDuration - remainingTime);

        // Restart the interval
        progressInterval = setInterval(updateProgress, 50);
    }
}




// Update progress bar
function updateProgress() {
    if (!isPaused) {
        const elapsed = Date.now() - progressStartTime;
        currentProgress = Math.min((elapsed / progressDuration) * 100, 100);

        // Update progress bar width
        const progressFill = document.querySelector('.story-progress-fill');
        progressFill.style.width = currentProgress + '%';

        // Check if story is complete
        if (currentProgress >= 100) {
            clearInterval(progressInterval);
            closeStoryAndReset();
        }
    }
}





// Progress bar functions
function startStoryProgress() {
    const progressFill = document.querySelector('.story-progress-fill');

    // Reset progress bar
    progressFill.style.width = '0%';
    progressFill.style.transition = 'none';

    // Reset variables
    isPaused = false;
    currentProgress = 0;
    progressStartTime = Date.now();

    // Clear any existing interval
    if (progressInterval) {
        clearInterval(progressInterval);
    }

    // Start progress updates
    progressInterval = setInterval(updateProgress, 50);
}




function resetStoryProgress() {
    const progressFill = document.querySelector('.story-progress-fill');
    progressFill.style.width = '0%';
    progressFill.style.transition = 'none';

    isPaused = false;
    currentProgress = 0;

    if (progressInterval) {
        clearInterval(progressInterval);
        progressInterval = null;
    }
}




function closeStoryAndReset() {
    document.body.style.overflow = 'auto';
    markStoryAsViewed(currentStoryId,viewerId);
    document.getElementById('storyDialog').close();
    resetStoryProgress();
}




// Function to mark story as viewed
function markStoryAsViewed(storyId, userId) {

    const urlParams = new URLSearchParams(window.location.search);
    const currentUserId=urlParams.get('user_id');
    const userWeWillVisit=urlParams.get('user_we_will_visit');


    fetch('../story/mark_story_as_viewed.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `story_id=${storyId}&user_id=${userId}`
    })
        .then(response => response.text())
        .then(data => {

            if(!userWeWillVisit) // in case we go to the profile of him then go back to the home page so in this case i not send the user_we... parameter
                window.location.href = `../home/home.php?user_id=${currentUserId}&user_we_will_visit=-1`;

            // Redirect AFTER the fetch completes
            else window.location.href = `../home/home.php?user_id=${currentUserId}&user_we_will_visit=${userWeWillVisit}`;
        })

}