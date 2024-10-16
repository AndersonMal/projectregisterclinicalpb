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
        const formData = new FormData(frm);
        const http = new XMLHttpRequest();
        http.open("POST", url, true);
        http.send(formData);
        http.onreadystatechange = function(){
            if (this.readyState == 4) {
                if (this.status == 200) {
                    try {
                        const res = JSON.parse(this.responseText);
                        if (res === "Admin") {
                            window.location = base_url + "Users/panelAdmin";
                        } else if (res === "Ok") {
                            window.location = base_url + "Users/listRegistersClinical";
                        } else {
                            document.getElementById("alerta").classList.remove("d-none");
                            document.getElementById("alerta").innerHTML = res;
                        }
                    } catch (error) {
                        console.error("Error parsing JSON:", error);
                        console.error("Response text:", this.responseText);
                    }
                } else {
                    console.error("HTTP Error:", this.status);
                    document.getElementById("alerta").classList.remove("d-none");
                    document.getElementById("alerta").innerHTML = "Error en la solicitud: " + this.status;
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
            if(this.readyState == 4 && this.status == 200){
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

(function(document){
    'search';

    var LightTableFilter = (function(Arr){
        var _input;

        function _onInputEvent(e){
            _input = e.target;
            var tables = document.getElementsByClassName(_input.getAttribute('data-table'));
            Arr.forEach.call(tables, function(table){
                Arr.forEach.call(table.tBodies, function(tbody) {
                    Arr.forEach.call(tbody.rows, _filter);
                });
            });
        }

        function _filter(row) {
            var text = row.textContent.normalize("NFD").replace(/[\u0300-\u036f]/g, ""),
            val = _input.value.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
         
            row.style.display = text.indexOf(val) === -1 ? 'none' : 'table-row';
        }

        return {
            init: function() {
                var inputs = document.getElementsByClassName('light-table-filter');
                Arr.forEach.call(inputs, function(input) {
                    input.oninput = _onInputEvent;
                });
            }
        };

    })(Array.prototype);

    document.addEventListener('readystatechange', function() {
        if (document.readyState === 'complete') {
            LightTableFilter.init();
        }
    });

})(document);

function modalHelp1(){
    $("#modalHelp").modal("show");
}

function modalClose(){
    $("#modalHelp").modal("hide");
}
let tblUser;
document.addEventListener("DOMContentLoaded", function(){
    tblUser = $('#tblUser').DataTable({
        ajax: {
            url: base_url + "Users/userList",
            dataSrc: ''
        },
        columns: [  
            { 'data' : 'Id' },               
            { 'data' : 'numeroDocumento' }, 
            { 'data' : 'primerApellido' },            
            { 'data' : 'fecharegistro' }
        ],
        language: {
            "sEmptyTable": "No hay datos disponibles en la tabla",
            "sInfo": "Mostrando de _START_ a _END_ de _TOTAL_ entradas",
            "sInfoEmpty": "Mostrando 0 a 0 de 0 entradas",
            "sLengthMenu": "Mostrar _MENU_ entradas",
            "sLoadingRecords": "Cargando...",
            "sProcessing": "Procesando...",
            "sSearch": "Buscar:",
            "sZeroRecords": "No se encontraron resultados",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": activar para ordenar la columna de manera descendente"
            }
        }
    });   
});

reportUsers();

function reportUsers() {
    const url = base_url + "Users/countUsersRegisters";
    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send();
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            try {
                const res = JSON.parse(this.responseText);
                let months = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];
                let amount = new Array(12).fill(0); 

                for (let i = 0; i < res.length; i++) {
                    let monthIndex = res[i]['mes'] - 1;
                    amount[monthIndex] = res[i]['cantidad_usuarios']; 
                }

                const maxUsers = Math.max(...amount) + 2;
                var ctx = document.getElementById("usersRegisters");

                var myBarChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: months, 
                        datasets: [{
                            label: "Usuarios registrados",
                            backgroundColor: "rgba(2,117,216,1)",
                            borderColor: "rgba(2,117,216,1)",
                            data: amount, 
                        }],
                    },
                    options: {
                        scales: {
                            xAxes: [{
                                gridLines: {
                                    display: false
                                },
                                ticks: {
                                    maxTicksLimit: 12  
                                }
                            }],
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true, 
                                    max: maxUsers,
                                    stepSize: 1,
                                    callback: function(value) {
                                        return Number.isInteger(value) ? value : ''; 
                                    }
                                },
                                gridLines: {
                                    display: true
                                }
                            }],
                        },
                        legend: {
                            display: false
                        }
                    }
                });
            } catch (error) {
                console.error("Error parsing JSON:", error);
                console.error("Response text:", this.responseText);
            }
        }
    };
}



