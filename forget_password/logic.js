let form=document.querySelector('form')


function validatePassword(password){
    if(password.length < 6) {
        window.alert("The Password is too short (less than 6 chars ) !")
        return false;
    }

    else if(password.includes(' ')==true) {
        window.alert("The Password cant contains spaces !")
        return false;
    }

    else if(password.match(/\d/)==null) {
        window.alert("The Password at least must contain one digit !")
        return false;
    }

    return true;
}

form.addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent form submission

    const password = document.querySelector('input').value
    const isValid = validatePassword(password);

    if (isValid) {
        // Password is valid, submit the form
        window.alert("The Password was changed successfully !")
        this.submit();//this==form

        //!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        //later change it in PHP!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        //once the password is changed we will redirect into login page by php
        window.location.href='../login/login.html'
    }
    else
        this.reset(); // to clear the entered password ( wrong one )
});


