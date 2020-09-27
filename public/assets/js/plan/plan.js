function validaForm(){
    //campo select
    if($("#cartera").val() == ""){
        alert("Seleccionar Cartera.");
        $("#cartera").focus();       // Esta función coloca el foco de escritura del usuario en el campo Nombre directamente.
        return false;
    }
    // Campos de texto
    if($("#plan").val() == ""){
        alert("Colocar Nombre del Plan de Trabajo.");
        $("#plan").focus();       // Esta función coloca el foco de escritura del usuario en el campo Nombre directamente.
        return false;
    }
    if($("#speech").val() == ""){
        alert("Colocar Speech.");
        $("#speech").focus();       // Esta función coloca el foco de escritura del usuario en el campo Nombre directamente.
        return false;
    }
    if($("#fec_i").val() == ""){
        alert("Colocar Fecha.");
        $("#fec_i").focus();
        return false;
    }
    if($("#fec_f").val() == ""){
        alert("Colocar Fecha.");
        $("#fec_f").focus();
        return false;
    }

    return true; // Si todo está correcto
}
function validaFormVer(){
    //campo select
    if($("#cartera").val() == ""){
        alert("Seleccionar Cartera.");
        $("#cartera").focus();       // Esta función coloca el foco de escritura del usuario en el campo Nombre directamente.
        return false;
    }
    return true; // Si todo está correcto
}

function limpiarFormulario() {

    $('#cartera').val("");
    $('#speech').val("");
    $('#plan').val("");
    $('#fec_i').val("");
    $('#fec_f').val("");
}


const botonVer = document.getElementById('ver')
const botonInsertar = document.getElementById('insertar')
const cargandoVer= document.getElementById('cargando-ver')
const cargandoGuardar= document.getElementById('cargando-guardar')
const contenedorUsuario = document.getElementById('contenedor-usuarios');
const contenedorCantidad = document.getElementById('contenedor-cantidad');
botonVer.addEventListener('click',(event)=>{event.preventDefault();selectVer()});
botonInsertar.addEventListener('click',(event)=>{event.preventDefault();selectInsertar()});

function selectVer(){
    
    const selectCartera = document.getElementById('cartera')
    const tramos = document.querySelectorAll('input[name="tramos"]')
    const deps = document.querySelectorAll('input[name="deps"]')
    const prioridades = document.querySelectorAll('input[name="prioridades"]')
    const situaciones = document.querySelectorAll('input[name="situaciones"]')
    const calls = document.querySelectorAll('input[name="calls"]')
    const sueldos = document.querySelectorAll('input[name="sueldos"]')
    const capitales = document.querySelectorAll('input[name="capitales"]')
    const deudas = document.querySelectorAll('input[name="deudas"]')
    const importes = document.querySelectorAll('input[name="importes"]')
    const ubics = document.querySelectorAll('input[name="ubics"]')
    const entidades = document.querySelectorAll('input[name="entidades"]')
    const clientes = document.querySelectorAll('input[name="clientes"]')
    const usuarios = document.querySelectorAll('input[name="usuarios"]')
    const valorSelectCartera = selectCartera.options[selectCartera.selectedIndex].value;

    console.log({valorSelectCartera})

    /*const contenedorUsuario = document.getElementById('contenedor-usuarios');
    const contenedorCantidad = document.getElementById('contenedor-cantidad');*/

    /*contenedorUsuario.innerHTML = '';
    contenedorCantidad.innerHTML = '';*/
 
    var arrayTramos = [];
    //var noChequeados = [];
    tramos.forEach(el=> {
        if (el.checked) {
            arrayTramos.push(el.value);
        /*} else {
          noChequeados.push(el.value);*/
        }
    })
      
    console.log(arrayTramos);
    var lengthTramos = arrayTramos.length;
    console.log(lengthTramos);
      //console.log(noChequeados);
    var arrayDeps = [];
    deps.forEach(el=> {
        if (el.checked) {
            arrayDeps.push(el.value);
        }
    })
    console.log(arrayDeps);
    var lengthDeps = arrayDeps.length;

    var arrayPrioridades = [];
    prioridades.forEach(el=> {
        if (el.checked) {
            arrayPrioridades.push(el.value);
        }
    })
    console.log(arrayPrioridades);
    var lengthPrio = arrayPrioridades.length;

    var arraySituaciones = [];
    situaciones.forEach(el=> {
        if (el.checked) {
            arraySituaciones.push(el.value);
        }
    })
    console.log(arraySituaciones);
    var lengthSitu = arraySituaciones.length;

    var arrayCalls = [];
    calls.forEach(el=> {
        if (el.checked) {
            arrayCalls.push(el.value);
        }
    })
    console.log(arrayCalls);
    var lengthCalls = arrayCalls.length;

    var arraySueldos = [];
    sueldos.forEach(el=> {
        if (el.checked) {
            arraySueldos.push(el.value);
        }
    })
    console.log(arraySueldos);
    var lengthSueldos = arraySueldos.length;

    var arrayCapitales = [];
    capitales.forEach(el=> {
        if (el.checked) {
            arrayCapitales.push(el.value);
        }
    })
    console.log(arrayCapitales);
    var lengthCapitales = arrayCapitales.length;

    var arrayDeudas = [];
    deudas.forEach(el=> {
        if (el.checked) {
            arrayDeudas.push(el.value);
        }
    })
    console.log(arrayDeudas);
    var lengthDeudas = arrayDeudas.length;

    var arrayImportes = [];
    importes.forEach(el=> {
        if (el.checked) {
            arrayImportes.push(el.value);
        }
    })
    console.log(arrayImportes);
    var lengthImportes = arrayImportes.length;

    var arrayUbics = [];
    ubics.forEach(el=> {
        if (el.checked) {
            arrayUbics.push(el.value);
        }
    })
    console.log(arrayUbics);
    var lengthUbics = arrayUbics.length;

    var arrayEntidades = [];
    entidades.forEach(el=> {
        if (el.checked) {
            arrayEntidades.push(el.value);
        }
    })
    console.log(arrayEntidades);

    var arrayClientes = [];
    clientes.forEach(el=> {
        if (el.checked) {
            arrayClientes.push(el.value);
        }
    })
    console.log(arrayClientes);

    var arrayUsuarios = [];
    usuarios.forEach(el=> {
        if (el.checked) {
            arrayUsuarios.push(el.value);
        }
    })
    console.log(arrayUsuarios);

    if(validaFormVer()){
        cargandoVer.style.display = '';
        fetch('/indicadores/public/plan/ver?car='+(valorSelectCartera || null)+'&tramos='+(arrayTramos || null)+'&deps='+(arrayDeps || null)+'&prioridades='+(arrayPrioridades || null)+'&situaciones='+(arraySituaciones || null)+'&calls='+(arrayCalls || null)+'&sueldos='+(arraySueldos || null)+'&capitales='+(arrayCapitales || null)+'&deudas='+(arrayDeudas || null)+'&importes='+(arrayImportes || null)+'&ubics='+(arrayUbics || null)+'&entidades='+(arrayEntidades || null)+'&clientes='+(arrayClientes || null)+'&usuarios='+(arrayUsuarios || null))
        //fetch('/plan/ver?car='+(valorSelectCartera || null)+'&tramos='+(arrayTramos || null)+'&deps='+(arrayDeps || null)+'&prioridades='+(arrayPrioridades || null)+'&situaciones='+(arraySituaciones || null)+'&calls='+(arrayCalls || null)+'&sueldos='+(arraySueldos || null)+'&capitales='+(arrayCapitales || null)+'&deudas='+(arrayDeudas || null)+'&importes='+(arrayImportes || null)+'&ubics='+(arrayUbics || null)+'&entidades='+(arrayEntidades || null)+'&clientes='+(arrayClientes || null)+'&usuarios='+(arrayUsuarios || null))
        .then(res=>res.json())
            .then(res=>{
                cargandoVer.style.display = 'none';
                console.log(res);
                let total = 0;
                res.forEach(el=>{
                    total += parseInt(el.cantidad)
                })
                console.log(total);
                let html=``;
                let html2=``;
                html = `<table class="table table-sm table-borderless table-responsive-sm">
                                <thead class="text-center">
                                    <tr>
                                        <th scope="col">Usuarios</th>
                                        <th scope="col" class="text-white">---------</th>
                                        <th scope="col">Clientes</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">`
                res.forEach(el=>{
                    html +=         `<tr class="text-center">
                                        <td>${el.usuario_nom} </td>
                                        <td class="text-center"> 
                                            <input class="form-check-input pl-1" name="usuarios" type="checkbox" value="${el.usuario}" checked>
                                        </td>
                                        <td>${el.cantidad}</td>
                                    </tr>`
                })
                html +=         `
                            </tbody>
                        </table>`
                contenedorUsuario.innerHTML = html;

                html2 = `<div class="card border-info">
                            <div class="card-body text-dark">
                                <h3 class="card-title text-center">TOTAL CLIENTES:</h3><br>
                                <h3 class="card-text text-center">${total}</h3>
                            </div>
                        </div>`
                contenedorCantidad.innerHTML = html2;
            })
    }
}

function selectInsertar(){
    
    
    const selectCartera = document.getElementById('cartera')
    const tramos = document.querySelectorAll('input[name="tramos"]')
    const deps = document.querySelectorAll('input[name="deps"]')
    const prioridades = document.querySelectorAll('input[name="prioridades"]')
    const situaciones = document.querySelectorAll('input[name="situaciones"]')
    const calls = document.querySelectorAll('input[name="calls"]')
    const sueldos = document.querySelectorAll('input[name="sueldos"]')
    const capitales = document.querySelectorAll('input[name="capitales"]')
    const deudas = document.querySelectorAll('input[name="deudas"]')
    const importes = document.querySelectorAll('input[name="importes"]')
    const ubics = document.querySelectorAll('input[name="ubics"]')
    const entidades = document.querySelectorAll('input[name="entidades"]')
    const clientes = document.querySelectorAll('input[name="clientes"]')
    const usuarios = document.querySelectorAll('input[name="usuarios"]')
    const selectFec_i = document.getElementById('fec_i')
    const selectFec_f = document.getElementById('fec_f')
    const selectPlan = document.getElementById('plan')
    const selectSpeech = document.getElementById('speech')
    const valorSelectFec_i = selectFec_i.value;
    const valorSelectFec_f = selectFec_f.value;
    const valorSelectPlan = selectPlan.value;
    const valorSelectSpeech = selectSpeech.value;
    const valorSelectCartera = selectCartera.options[selectCartera.selectedIndex].value;
    console.log({valorSelectCartera});
    console.log({valorSelectFec_i});
    console.log({valorSelectFec_f});
    console.log({valorSelectPlan});
    console.log({valorSelectSpeech});

    const contenedorMensaje = document.getElementById('mensaje');

    var arrayTramos = [];
    //var noChequeados = [];
    tramos.forEach(el=> {
        if (el.checked) {
            arrayTramos.push(el.value);
        /*} else {
          noChequeados.push(el.value);*/
        }
    })
      
    console.log(arrayTramos);
    var lengthTramos = arrayTramos.length;
    console.log(lengthTramos);
      //console.log(noChequeados);
    var arrayDeps = [];
    deps.forEach(el=> {
        if (el.checked) {
            arrayDeps.push(el.value);
        }
    })
    console.log(arrayDeps);
    var lengthDeps = arrayDeps.length;

    var arrayPrioridades = [];
    prioridades.forEach(el=> {
        if (el.checked) {
            arrayPrioridades.push(el.value);
        }
    })
    console.log(arrayPrioridades);
    var lengthPrio = arrayPrioridades.length;

    var arraySituaciones = [];
    situaciones.forEach(el=> {
        if (el.checked) {
            arraySituaciones.push(el.value);
        }
    })
    console.log(arraySituaciones);
    var lengthSitu = arraySituaciones.length;

    var arrayCalls = [];
    calls.forEach(el=> {
        if (el.checked) {
            arrayCalls.push(el.value);
        }
    })
    console.log(arrayCalls);
    var lengthCalls = arrayCalls.length;

    var arraySueldos = [];
    sueldos.forEach(el=> {
        if (el.checked) {
            arraySueldos.push(el.value);
        }
    })
    console.log(arraySueldos);
    var lengthSueldos = arraySueldos.length;

    var arrayCapitales = [];
    capitales.forEach(el=> {
        if (el.checked) {
            arrayCapitales.push(el.value);
        }
    })
    console.log(arrayCapitales);
    var lengthCapitales = arrayCapitales.length;

    var arrayDeudas = [];
    deudas.forEach(el=> {
        if (el.checked) {
            arrayDeudas.push(el.value);
        }
    })
    console.log(arrayDeudas);
    var lengthDeudas = arrayDeudas.length;

    var arrayImportes = [];
    importes.forEach(el=> {
        if (el.checked) {
            arrayImportes.push(el.value);
        }
    })
    console.log(arrayImportes);
    var lengthImportes = arrayImportes.length;

    var arrayUbics = [];
    ubics.forEach(el=> {
        if (el.checked) {
            arrayUbics.push(el.value);
        }
    })
    console.log(arrayUbics);
    var lengthUbics = arrayUbics.length;

    var arrayEntidades = [];
    entidades.forEach(el=> {
        if (el.checked) {
            arrayEntidades.push(el.value);
        }
    })
    console.log(arrayEntidades);

    var arrayClientes = [];
    clientes.forEach(el=> {
        if (el.checked) {
            arrayClientes.push(el.value);
        }
    })
    console.log(arrayClientes);

    var arrayUsuarios = [];
    usuarios.forEach(el=> {
        if (el.checked) {
            arrayUsuarios.push(el.value);
        }
    })
    console.log(arrayUsuarios);

    if(validaForm()){
        cargandoGuardar.style.display = '';
        fetch('/indicadores/public/plan/insertar?car='+(valorSelectCartera || null)+'&plan='+(valorSelectPlan || null)+'&speech='+(valorSelectSpeech || null)+'&fec_i='+(valorSelectFec_i || null)+'&fec_f='+(valorSelectFec_f || null)+'&tramos='+(arrayTramos || null)+'&deps='+(arrayDeps || null)+'&prioridades='+(arrayPrioridades || null)+'&situaciones='+(arraySituaciones || null)+'&calls='+(arrayCalls || null)+'&sueldos='+(arraySueldos || null)+'&capitales='+(arrayCapitales || null)+'&deudas='+(arrayDeudas || null)+'&importes='+(arrayImportes || null)+'&ubics='+(arrayUbics || null)+'&entidades='+(arrayEntidades || null)+'&clientes='+(arrayClientes || null)+'&usuarios='+(arrayUsuarios || null))
        //fetch('/plan/insertar?car='+(valorSelectCartera || null)+'&plan='+(valorSelectPlan || null)+'&speech='+(valorSelectSpeech || null)+'&fec_i='+(valorSelectFec_i || null)+'&fec_f='+(valorSelectFec_f || null)+'&tramos='+(arrayTramos || null)+'&deps='+(arrayDeps || null)+'&prioridades='+(arrayPrioridades || null)+'&situaciones='+(arraySituaciones || null)+'&calls='+(arrayCalls || null)+'&sueldos='+(arraySueldos || null)+'&capitales='+(arrayCapitales || null)+'&deudas='+(arrayDeudas || null)+'&importes='+(arrayImportes || null)+'&ubics='+(arrayUbics || null)+'&entidades='+(arrayEntidades || null)+'&clientes='+(arrayClientes || null)+'&usuarios='+(arrayUsuarios || null))
        .then(res=>res.json())
            .then(res=>{
                cargandoGuardar.style.display = 'none';
                console.log(res);
                let datosInsert=res.insertado;
                console.log(datosInsert);
                let datosTabla=res.querytabla;
                console.log(datosTabla);

                let html=``;
                if(datosInsert==true){
                html = `<div class="alert alert-success alert-dismissible" data-auto-dismiss="10000">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h6><i class="icon fa fa-check"></i> MENSAJE DE SISTEMA DE REGISTRO</h6>
                            <ul>
                                <li>Registro con éxito</li>
                            </ul>
                        </div>`
                }else{
                    html = `<div class="alert alert-danger alert-dismissible" data-auto-dismiss="10000">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h6><i class="icon fa fa-check"></i> MENSAJE DE SISTEMA DE REGISTRO</h6>
                            <ul>
                                <li>Error al Registrar</li>
                            </ul>
                        </div>`
                }
                contenedorMensaje.innerHTML = html;

                let exportData=[];
                exportData = datosTabla.map(dato=>({codigo:"'"+dato.cuenta,nombre:dato.cli_nom,capital:dato.cap,deuda:dato.sal,importe:dato.importe,usuario_asignado:dato.emp_nom,medio_cel:"'"+dato.cli_tel_tel,ubicabilidad:dato.respuesta}));
                let title = `plan_trabajo`;
                //title+=datosTabla.cartera;

                exportExcelFromJson(exportData,title,true);

                
                limpiarFormulario();
                //document.getElementById("cont-ver").innerHTML="";
                $(document).ready(function () {
                    //Cerrar Las Alertas Automaticamente
                    $('.alert[data-auto-dismiss]').each(function (index, element) {
                        const $element = $(element),
                            timeout = $element.data('auto-dismiss') || 10000;
                        setTimeout(function () {
                            $element.alert('close');
                        }, timeout);
                    });
                });
                contenedorUsuario.innerHTML = '';
                contenedorCantidad.innerHTML = '';

            })
    }
}

function exportExcelFromJson(JSONData, ReportTitle, ShowLabel){
    //If JSONData is not an object then JSON.parse will parse the JSON string in an Object
    var arrData = typeof JSONData != 'object' ? JSON.parse(JSONData) : JSONData;

    var CSV = '';
    //Set Report title in first row or line

    //CSV += ReportTitle + '\r\n\n';

    //This condition will generate the Label/Header
    if (ShowLabel) {
        var row = "";

        //This loop will extract the label from 1st index of on array
        for (var index in arrData[0]) {
            //Now convert each value to string and comma-seprated
            row += index + '\t';
        }

        row = row.slice(0, -1);
        //append Label row with line break
        CSV += row.toUpperCase() + '\r\n';
    }
    //1st loop is to extract each row
    for (var i = 0; i < arrData.length; i++) {
        var row = "";
        //2nd loop will extract each column and convert it in string comma-seprated
        for (var index in arrData[i]) {
            row += '' + arrData[i][index] + '\t';
        }

        row.slice(0, row.length - 1);
        //add a line break after each row
        CSV += row + '\r\n';
    }

    if (CSV == '') {
        alert("Invalid data");
        return;
    }

    //Generate a file name
    var fileName = "";
    //this will remove the blank-spaces from the title and replace it with an underscore
    fileName += ReportTitle.replace(/ /g," ");

    //Initialize file format you want csv or xls
    var uri = 'data:text/xls;charset=utf-8,' + escape(CSV);

    // Now the little tricky part.
    // you can use either>> window.open(uri);
    // but this will not work in some browsers
    // or you will not get the correct file extension

    //this trick will generate a temp <a /> tag
    var link = document.createElement("a");
    link.href = uri;

    //set the visibility hidden so it will not effect on your web-layout
    link.style = "visibility:hidden";
    link.download = fileName + ".xls";

    //this part will append the anchor tag and remove it after automatic click
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}