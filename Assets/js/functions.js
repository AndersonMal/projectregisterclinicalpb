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
    const password = document.getElementById("password");
    const lastname = document.getElementById("lastname");
    const birthdate = document.getElementById("birthdate");

    if(user.value == ""){
        password.classList.remove("is-invalid");
        lastname.classList.remove("is-invalid");
        birthdate.classList.remove("is-invalid");
        user.classList.add("is-invalid");
        user.focus();
    }else if(password.value == ""){
        user.classList.remove("is-invalid");
        lastname.classList.remove("is-invalid");
        birthdate.classList.remove("is-invalid");
        password.classList.add("is-invalid");
        password.focus();
    }else if(lastname.value == ""){
        user.classList.remove("is-invalid");
        password.classList.remove("is-invalid");
        birthdate.classList.remove("is-invalid");
        lastname.classList.add("is-invalid");
        lastname.focus();
    }else if(birthdate.value == ""){
        user.classList.remove("is-invalid");
        lastname.classList.remove("is-invalid");
        password.classList.remove("is-invalid");
        birthdate.classList.add("is-invalid");
        birthdate.focus();
    }else{
        const url = base_url + "RegisterController/validate";
        const frm = document.getElementById("frmRegister");
        const http = new XMLHttpRequest();
        http.open("POST", url, true);
        http.send(new FormData(frm));
        http.onreadystatechange = function(){
            if(this.readyState == 4 && this.status == 200){
                const res = JSON.parse(this.responseText);
                if(res == "Ok"){
                    window.location = base_url ;
                }else{
                    document.getElementById("alerta").classList.remove("d-none");
                    document.getElementById("alerta").innerHTML = res;
                }
            }
        }
    }

}
