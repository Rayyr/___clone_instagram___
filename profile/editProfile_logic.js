const editProfile=document.getElementById('editProfile');
function showOrNotEditProfile(){
    const urlParams = new URLSearchParams(window.location.search);
    const user_id=urlParams.get('user_id');
    const userWeWillVisitId = urlParams.get('user_we_will_visit');

    if((user_id==userWeWillVisitId) || (userWeWillVisitId==-1)) {//in case the user click on post then close it then they will be = , or if 1st time he open his profile then userwill..=-1
       editProfile.style.display='flex';
    }
    else
        editProfile.style.display='none';


}


showOrNotEditProfile();