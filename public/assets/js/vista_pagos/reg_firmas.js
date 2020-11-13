const botonBuscar = document.getElementById('buscar')
const botonPrevio = document.getElementById('previo')
const botonActualizar = document.getElementById('actualizar')
const contenedorTabla = document.getElementById('contenedor-tabla');


const selectCartera = document.getElementById('cartera')
const selectTipo = document.getElementById('tipo')
const selectMes = document.getElementById('mes')


//const contenedorSelect = document.getElementById('usuario');
const cargando= document.getElementById('cargando');
const cargandoModal= document.getElementById('cargando-detalle');
botonBuscar.addEventListener('click',(event)=>{event.preventDefault();selectBuscar()});
botonActualizar.addEventListener('click',(event)=>{event.preventDefault();GuardarDatos()});
botonPrevio.addEventListener('click',(event)=>{event.preventDefault();actualizarTabla()});

selectMes.addEventListener('change',()=>{selectCarteras()});

function selectCarteras(){
    //$('#cartera').val("");
    for (let i = selectCartera.options.length; i >= 1; i--) {
        selectCartera.remove(i);
    }
    const valorSelectMes = selectMes.value;
    const contenedorCarteras = document.getElementById('cartera');
    console.log({valorSelectMes})
    let url = '';
    url='carga_carteras/'+valorSelectMes;
    fetch(url)
        .then(res=>res.json())
            .then(res=>{
                console.log(res);
                let htmlSelect=``;
                
                htmlSelect = `
                        <option selected value="">Seleccione</option>
                        `
                res.forEach(el=>{
                    htmlSelect += `
                        <option class="option" value="${el.car_id_FK}">${el.car_nom}</option>
                    `
                });
                
                contenedorCarteras.innerHTML = htmlSelect;
            })

}

function selectBuscar(){
    contenedorTabla.innerHTML='';
    
    const valorSelectCartera = selectCartera.options[selectCartera.selectedIndex].value;
    const valorSelectTipo = selectTipo.options[selectTipo.selectedIndex].value;
    const valorSelectMes = selectMes.value;
    
    console.log({valorSelectCartera})
    console.log({valorSelectTipo})
    console.log({valorSelectMes})
    if(valorSelectCartera!='' && valorSelectMes!=''){
        let url = '';
        url='carga_clientes_pagos/'+valorSelectCartera+'/'+valorSelectMes;
        cargando.style.display = '';
        fetch(url)
        .then(res=>res.json())
            .then(res=>{
                cargando.style.display = 'none';
                console.log(res);
                /*res.sort(function (a, b){
                    return (b.pag_cli_fec - a.pag_cli_fec)
                })*/
                console.log("Antes de ordenar: ",res);
                    res.sort((a, b) => convertirFecha(b.fec) - convertirFecha(a.fec));
                    console.log("Despues de ordenar: ",res);
                console.log(res);
                if(valorSelectTipo=='0'){
                    res=res.filter(c=>parseInt(c.cantidad)==0);

                    /*res.sort(function (a, b){
                        return (b.pag_cli_fec - a.pag_cli_fec)
                    });*/

                    res.forEach(element => {
                        //console.log(element.pag_cli_cod);
                        if(parseInt(element.cantidad)==0){
                            element.boton_buscar = `  <a class="btn text-dark" role="button" data-toggle="modal" data-target="#miModal" onclick="cargarDatos(${parseInt(element.pag_cli_id)},${element.fec})">
                                                        <span class="badge badge-pill badge-info" style="font-size:12px;">Ver Usuarios</span>
                                                    </a>`;
                        }
                    });
                    let html=``;
                    html = `<table class="table table-sm table-hover display nowrap" style="font-size:14px;width:100%;height:100%;" id="tablaClientes">
                                            <thead class="text-center">
                                                <tr class="">
                                                    <th>Código</th>
                                                    <th>Fecha Pago</th>
                                                    <th>Monto Pago</th>
                                                    <th>Gestor</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-center text-dark">`
                
                                html +=     `</tbody>
                                </table>
                                `
                    contenedorTabla.innerHTML = html;

                }else if (valorSelectTipo=='1'){
                    res=res.filter(c=>parseInt(c.cantidad)==1);
                    res.sort(function (a, b){
                        return (b.pag_cli_fec - a.pag_cli_fec)
                    });

                    res.forEach(element => {
                        //console.log(element.pag_cli_cod);
                        if(parseInt(element.cantidad)==1){
                            element.boton_buscar = element.encargado;
                        }
                    });
                    let html=``;
                    html = `<table class="table table-sm table-hover display nowrap" style="font-size:14px;width:100%;height:100%;" id="tablaClientes">
                                            <thead class="text-center">
                                                <tr class="">
                                                    <th>Código</th>
                                                    <th>Fecha Pago</th>
                                                    <th>Monto Pago</th>
                                                    <th>Gestor</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-center text-dark">`
                
                                html +=     `</tbody>
                                </table>
                                `
                    contenedorTabla.innerHTML = html;

                }else if (valorSelectTipo=='2'){
                    res=res.filter(c=>parseInt(c.cantidad)>=2);
                    res.sort(function (a, b){
                        return (b.pag_cli_fec - a.pag_cli_fec)
                    });

                    res.forEach(element => {
                        //console.log(element.pag_cli_cod);
                        if(parseInt(element.cantidad)>=2){
                            element.boton_buscar = `  <a class="btn text-dark" role="button" data-toggle="modal" data-target="#miModal" onclick="cargarDatos(${parseInt(element.pag_cli_id)},${element.fec})">
                                                        <span class="badge badge-pill badge-info" style="font-size:12px;">Ver Usuarios</span>
                                                    </a>`;
                        }
                    });
                    let html=``;
                    html = `<table class="table table-sm table-hover display nowrap" style="font-size:14px;width:100%;height:100%;" id="tablaClientes">
                                            <thead class="text-center">
                                                <tr class="">
                                                    <th>Código</th>
                                                    <th>Fecha Pago</th>
                                                    <th>Monto Pago</th>
                                                    <th>Gestor</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-center text-dark">`
                
                                html +=     `</tbody>
                                </table>
                                `
                    contenedorTabla.innerHTML = html;

                }else{
                    console.log("Antes de ordenar: ",res);
                    res.sort((a, b) => convertirFecha(b.fec) - convertirFecha(a.fec));
                    console.log("Despues de ordenar: ",res);
                    res.forEach(element => {
                        //console.log(element.pag_cli_cod);
                        if(element.cantidad==1){
                            element.boton_buscar = element.encargado;

                        }else{
                            element.boton_buscar = `  <a class="btn text-dark" role="button" data-toggle="modal" data-target="#miModal" onclick="cargarDatos(${parseInt(element.pag_cli_id)},${element.fec})">
                                                        <span class="badge badge-pill badge-info" style="font-size:12px;">Ver Usuarios</span>
                                                    </a>`;
                        }
                    });
                    let html=``;
                    html = `<table class="table table-sm table-hover display nowrap" style="font-size:14px;width:100%;height:100%;" id="tablaClientes">
                                            <thead class="text-center">
                                                <tr class="">
                                                    <th>Código</th>
                                                    <th>Fecha Pago</th>
                                                    <th>Monto Pago</th>
                                                    <th>Gestor</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-center text-dark">`
                
                                html +=     `</tbody>
                                </table>
                                `
                    contenedorTabla.innerHTML = html;

                    
                }
                $('body').on('expanded.pushMenu collapsed.pushMenu', function() {
                    setTimeout(function(){
                        $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
                    }, 350);
                });
                
                $('a[data-toggle="tab"]').on( 'shown.bs.tab', function (e) {
                $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
                } );
                $('#tablaClientes').DataTable( {
                    "language": {
                    "search": "Buscar:",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                    "lengthMenu": "Ver _MENU_ registros",
                    //"pageLength: 10",
                    //"lengthMenu": "[[10,15,20,25,50,100,-1]]",
                    "paginate": {
                        "previous": "Anterior",
                        "next":"Siguiente"
                    }
                    },
                    "lengthMenu":		[[10, 15, 20, 25, 50, 100], [10, 15, 20, 25, 50, 100]],
                    "order": [[ 1, "desc" ]],
                    data : res,
                    scrollY: '50vh',
                    scrollCollapse: true,
                    //scroller:true,
                    columns: [
                            {"data" : "cli_cod"},
                            {"data" : "pag_cli_fec"},
                            {"data" : "pag_cli_mon"},
                            {"data" : "boton_buscar"},
                        ],
                    
                });
                /*$(document).ready(function() {
                    $('#tablaClientes').DataTable( {
                        "order": [[ 1, "desc" ]]
                    } );
                } );*/
                /*var table = $("#tablaClientes").DataTable() ;
                table.ajax.reload(null,false);*/
                
                /*window.onload=function(){
                    var pos=window.name || 0;
                    window.scrollTo(0,pos);
                }
                window.onunload=function(){
                    window.name=self.pageYOffset || (document.documentElement.scrollTop+document.body.scrollTop);
                }*/
                
                /*var table = $('#tablaClientes').DataTable();
                table.ajax.reload();*/
                //$('#tablaClientes').DataTable().ajax.reload(null,false);
                /*var table = $('#tablaClientes').DataTable( { ajax: "data.json" } );
                table.ajax.reload();*/
            })
    }
}


function cargarDatos(id,pag_fec){
    //console.log(codCli);
    console.log(id);
    //cargandoModal.style.display = '';
    let codigo=parseInt(id);
    let fecha=pag_fec;
    //let cartera=car;
    const contenedorSelect = document.getElementById('usuario');
    //console.log(car);
    console.log(codigo);
    console.log(fecha);
    let url = '';
    url='carga_cliente/'+codigo+'/'+fecha;
    
    fetch(url)
    .then(res=>res.json())
        .then(res=>{
            //cargandoModal.style.display = 'none';
            console.log(Object.keys(res).length);
            console.log(res);
            let cantidadRes=Object.keys(res).length;
            if(cantidadRes==2){
                let datosPagos=[];
                let datosUsuarios=[];
                datosPagos=res.clientes;
                datosUsuarios=res.encargados;
                let pag_fec=datosPagos[0].pag_cli_fec;
                let pag_car=datosPagos[0].car_id_FK;
                let pag_cod=datosPagos[0].cli_cod;
                let pag_pago=datosPagos[0].pag_cli_mon;
                console.log(pag_fec);
                console.log(pag_car);
                console.log(pag_cod);
                console.log(pag_pago);
                let cartera_id=document.getElementById('car_id');
                cartera_id.value=pag_car;
                let codigo_cli=document.getElementById('cod_cli');
                codigo_cli.value=pag_cod;
                let cli_fec=document.getElementById('fecha');
                cli_fec.value=pag_fec;
                let cli_pago=document.getElementById('pago');
                cli_pago.value=pag_pago;

                let htmlSelect=``;
                htmlSelect = `
                    <option selected value="">Seleccione</option>`
                datosUsuarios.forEach(e => {
                    htmlSelect +=`<option class="option" value="${e.encargado}">${e.encargado +' - '+ e.emp_nom}</option>`
                });
                contenedorSelect.innerHTML = htmlSelect;

            }else{
                let datosEncargados=[];
                datosEncargados=res.usuario;
                let pag_fec_u=datosEncargados[0].pag_cli_fec;
                let pag_car_u=datosEncargados[0].car_id_FK;
                let pag_cod_u=datosEncargados[0].cli_cod;
                let pag_pago_u=datosEncargados[0].pag_cli_mon;
                console.log(pag_fec_u);
                console.log(pag_car_u);
                console.log(pag_cod_u);
                console.log(pag_pago_u);
                let cartera_id=document.getElementById('car_id');
                cartera_id.value=pag_car_u;
                let codigo_cli=document.getElementById('cod_cli');
                codigo_cli.value=pag_cod_u;
                let cli_fec=document.getElementById('fecha');
                cli_fec.value=pag_fec_u;
                let cli_pago=document.getElementById('pago');
                cli_pago.value=pag_pago_u;

                let htmlSelect=``;
                htmlSelect = `
                    <option selected value="">Seleccione</option>`
                datosEncargados.forEach(e => {
                    htmlSelect +=`<option class="option" value="${e.encargado}">${e.encargado +' - '+ e.emp_nom}</option>`
                });
                contenedorSelect.innerHTML = htmlSelect;
            }
            $('#miModal').modal('show');
            //$('#miModal').modal('hide');
            $("#miModal").on("hidden.bs.modal", function(){
                $(".cont-body").html("");
            });

            /*$("#miModal").on("show.bs.modal", function(){
                $(".cont-body").html("");

            });*/
        })
}

function GuardarDatos(){
    const selectUsuario = document.getElementById('usuario')
    const selectCar = document.getElementById('car_id')
    const selectCod = document.getElementById('cod_cli')
    const selectFec = document.getElementById('fecha')
    const selectPag = document.getElementById('pago')
    const usuario = selectUsuario.options[selectUsuario.selectedIndex].value;
    const car = selectCar.value;
    const cod = selectCod.value;
    const fec = selectFec.value;
    const pago = selectPag.value;
    console.log({usuario})
    console.log({car})
    console.log({cod})
    console.log({fec})
    console.log({pago})
    let url = '';
    url='guardar_cliente/'+car+'/'+cod+'/'+pago+'/'+fec+'/'+usuario;
    fetch(url)
    .then(res=>res.json())
        .then(res=>{
            console.log(res);

            selectBuscar();

            $('#miModal').modal('toggle');
            //$('#tablaClientes').DataTable().ajax.reload(null,false);










            /*var table = $('#tablaClientes').DataTable();
                table.ajax.reload();*/

                /*$("#actualizar").click(function() {
                    $('#tablaClientes').DataTable().clear().draw();
                 });*/
        })
}

function actualizarTabla(){
    const selectCartera = document.getElementById('cartera')
    const valorSelectCartera = selectCartera.options[selectCartera.selectedIndex].value;
    const valorSelectMes = selectMes.value;
    const contenedorMensaje = document.getElementById('mensaje');
    console.log({valorSelectCartera})
    console.log({valorSelectMes})
    if(valorSelectCartera!='' && valorSelectMes!=''){
        let url = '';
        url='actualizado_automatico/'+valorSelectCartera+'/'+valorSelectMes;
        cargando.style.display = '';
        fetch(url)
        .then(res=>res.json())
            .then(res=>{
                cargando.style.display = 'none';
                console.log(res);
                let html=``;
                if(res==true){
                html = `<div class="alert alert-success alert-dismissible" data-auto-dismiss="10000">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h6><i class="icon fa fa-check"></i> MENSAJE</h6>
                            <ul>
                                <li>Actualización con éxito</li>
                            </ul>
                        </div>`
                }else{
                    html = `<div class="alert alert-danger alert-dismissible" data-auto-dismiss="10000">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h6><i class="icon fa fa-check"></i> MENSAJE</h6>
                            <ul>
                                <li>Error al Actualizar</li>
                            </ul>
                        </div>`
                }
                contenedorMensaje.innerHTML = html;
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
            })
    }
}

function convertirFecha (fechaString) {
    var fechaSp = fechaString.split("-");
    var anio = new Date().getFullYear();
    if (fechaSp.length == 3) {
      anio = fechaSp[2];
    }
    var mes = fechaSp[1] - 1;
    var dia = fechaSp[0];
  
    return new Date(anio, mes, dia);
  }