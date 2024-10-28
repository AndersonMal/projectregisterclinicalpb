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
