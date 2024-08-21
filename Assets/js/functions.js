function frmLogin(e){
    e.preventDefault();
    const user = document.getElementById("document");
    const password = document.getElementById("password");
    if(user.value == ""){
        password.classList.remove("is-invalid");
        user.classList.add("is-invalid");
        user.focus();
    }else if(password.value == ""){
        user.classList.remove("is-invalid");
        password.classList.add("is-invalid");
        password.focus();
    }else{
        const url = base_url + "Users/validate";
        const frm = document.getElementById("frmLogin");
        const http = new XMLHttpRequest();
        http.open("POST", url, true);
        http.send(new FormData(frm));
        http.onreadystatechange = function(){
            if(this.readyState == 4 && this.status == 200){
                const res = JSON.parse(this.responseText);
                if(res == "Ok"){
                    window.location = base_url + "Users";
                }else{
                    document.getElementById("alerta").classList.remove("d-none");
                    document.getElementById("alerta").innerHTML = res;
                }
            }
        }
    }

}

function frmRegister(e) {
    e.preventDefault();
    const user = document.getElementById("document");
    const firstname = document.getElementById("firstname");
    const birthdate = document.getElementById("birthdate");
    const password = document.getElementById("password");
    const confirm_password = document.getElementById("confirm_password");

    if(user.value == ""){
        firstname.classList.remove("is-invalid");
        birthdate.classList.remove("is-invalid");
        password.classList.remove("is-invalid");
        confirm_password.classList.remove("is-invalid");
        user.classList.add("is-invalid");
        user.focus();
    }else if(firstname.value == ""){
        user.classList.remove("is-invalid");
        birthdate.classList.remove("is-invalid");
        password.classList.remove("is-invalid");
        confirm_password.classList.remove("is-invalid");
        firstname.classList.add("is-invalid");
        firstname.focus();
    }else if(birthdate.value == ""){
        user.classList.remove("is-invalid");
        firstname.classList.remove("is-invalid");
        password.classList.remove("is-invalid");
        confirm_password.classList.remove("is-invalid");
        birthdate.classList.add("is-invalid");
        birthdate.focus();
    }else if(password.value == ""){
        user.classList.remove("is-invalid");
        firstname.classList.remove("is-invalid");
        birthdate.classList.remove("is-invalid");
        confirm_password.classList.remove("is-invalid");
        password.classList.add("is-invalid");
        password.focus();
    }else if(confirm_password.value == ""){
        user.classList.remove("is-invalid");
        firstname.classList.remove("is-invalid");
        birthdate.classList.remove("is-invalid");
        password.classList.remove("is-invalid");
        confirm_password.classList.add("is-invalid");
        password.focus();
    }else {
        const url = base_url + "RegisterController/validate";
        const frm = document.getElementById("frmRegister");
        const formData = new FormData(frm);
        const http = new XMLHttpRequest();
        http.open("POST", url, true);
        http.send(formData);
        http.onreadystatechange = function(){
            if(this.readyState === 4 && this.status === 200){
                try {
                    const res = JSON.parse(this.responseText);
                    if(res === "Ok"){
                        window.location = base_url;
                    } else {
                        document.getElementById("alerta").classList.remove("d-none");
                        document.getElementById("alerta").innerHTML = res;
                    }
                } catch (error) {
                    console.error("Error parsing JSON:", error);
                    console.error("Response text:", this.responseText);
                }
            }
            }
    }
}
