const botonBuscar = document.getElementById('buscar')
botonBuscar.addEventListener('click',(event)=>{event.preventDefault();selectBuscar()});

function selectBuscar(){
    const selectCartera = document.getElementById('cartera')
    const selectEtiqueta = document.getElementById('etiqueta')

    const contenedorEtiquetas = document.getElementById('contenedor-etiquetas');

    const valorSelectCartera = selectCartera.options[selectCartera.selectedIndex].value;
    const valorSelectEtiqueta = selectEtiqueta.value;

    console.log({valorSelectCartera})
    console.log({valorSelectEtiqueta})

    //fetch('/etiqueta/buscar?car='+(valorSelectCartera || null)+'&etiqueta='+(valorSelectEtiqueta || null))
    fetch('/indicadores/public/etiqueta/buscar?car='+(valorSelectCartera || null)+'&etiqueta='+(valorSelectEtiqueta || null))
    .then(res=>res.json())
        .then(res=>{
            console.log(res);
            res.forEach(element => {
                element.boton_etiqueta = `  <a class="btn text-dark" role="button" data-toggle="modal" data-target="#miModal" onclick="mostrarDatos(${element.id_etiqueta},${element.id_cartera},${element.cant_clientes})">
                                                <span class="badge badge-pill badge-info">Resultados</span>
                                            </a>`;
                
            });
            let html=``;
            html = `<table class="table table-sm table-hover display nowrap" style="font-size:14px;width:100%;" id="tabla-etiqueta">
                                    <thead class="text-center">
                                        <tr class="">
                                            <th>Etiqueta</th>
                                            <th>Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center text-dark">`
           
                        html +=     `</tbody>
                        </table>
                        `
            contenedorEtiquetas.innerHTML = html
            $('body').on('expanded.pushMenu collapsed.pushMenu', function() {
                setTimeout(function(){
                    $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
                }, 350);
            });
            
            $('a[data-toggle="tab"]').on( 'shown.bs.tab', function (e) {
              $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
            } );
            $('#tabla-etiqueta').DataTable( {
                responsive: true,
                "language": {
                  "search": "Buscar:",
                  "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                  "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                  "lengthMenu": "Ver _MENU_ registros",
                  "paginate": {
                    "previous": "Anterior",
                    "next":"Siguiente"
                  }
                },
                data : res,
                scrollY: '50vh',
                scrollCollapse: true,
                //scroller: true,
                columns: [
                        {"data" : "nombre_etiqueta"},
                        {"data" : "boton_etiqueta"},     
                     ],
            } );
        })
}

function mostrarDatos(id,car,cant){

    console.log(id);
    console.log(car);
    let cantidadTotal=cant;
    const contenedorCobertura = document.getElementById('contenedor-cobertura');
    const contenedorPDP = document.getElementById('contenedor-pdp');
    const contenedorGestiones = document.getElementById('contenedor-gestiones');
    const contenedorPagos = document.getElementById('contenedor-pagos');
    const contenedorUbic = document.getElementById('contenedor-ubic');

    //fetch('/etiqueta/mostrar?id='+id+'&car='+car)
    fetch('/indicadores/public/etiqueta/mostrar?id='+id+'&car='+car)
    .then(res=>res.json())
        .then(res=>{
            console.log(res);

            /**---cobertura--------- */
            let cobertura=res.cobertura;
            console.log(cobertura);
            let porcentajeGestionados = Math.round((parseInt(cobertura[0].can_clientes)/parseInt(cantidadTotal))*100);
            let cantidadSinGestion=parseInt(cantidadTotal) - parseInt(cobertura[0].can_clientes)
            let porcentajeNoGestionados = Math.round((parseInt(cantidadSinGestion)/parseInt(cantidadTotal))*100);

            let htmlCober=``;
            htmlCober=`
                        <table class="table table-center table-sm table-striped table-hover pre-wrap table-responsive-lg" style="font-size:12px">
                                    <thead class="text-center">
                                        <tr class="">
                                            <th>Cobertura</th>
                                            <th>Clientes</th>
                                            <th>%</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center text-dark">
                                        <tr>
                                            <th scope="row">Gestionados</th>
                                            <td>${cobertura[0].can_clientes}</td>
                                            <td>${porcentajeGestionados}%</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">No Gestionados</th>
                                            <td>${cantidadSinGestion}</td>
                                            <td>${porcentajeNoGestionados}%</td>
                                        </tr>
                                    </tbody>
                        </table>
                        `
            contenedorCobertura.innerHTML = htmlCober;

            /**---fin cobertura--------- */

            /**---PDP--------- */
            let pdp=res.pdp;
            console.log(pdp);
            let porcentajePDP = Math.round((parseInt(pdp[0].can_clientes)/parseInt(cantidadTotal))*100);
            let cantidadSinPDP=parseInt(cantidadTotal) - parseInt(pdp[0].can_clientes)
            let porcentajeSinPDP = Math.round((parseInt(cantidadSinPDP)/parseInt(cantidadTotal))*100);
            let htmlPDP=``;
            htmlPDP=`
                        <table class="table table-center table-sm table-striped table-hover pre-wrap table-responsive-lg" style="font-size:12px">
                                    <thead class="text-center">
                                        <tr class="">
                                            <th>PDP</th>
                                            <th>Clientes</th>
                                            <th>%</th>
                                            <th>Monto Generado</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center text-dark">
                                        <tr>
                                            <th scope="row">Con PDP</th>
                                            <td>${pdp[0].can_clientes}</td>
                                            <td>${porcentajePDP}%</td>
                                            <td>${pdp[0].monto_pdp>0? "S/."+formatoMoneda(pdp[0].monto_pdp):"-"}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Sin PDP</th>
                                            <td>${cantidadSinPDP}</td>
                                            <td>${porcentajeSinPDP}%</td>
                                            <td>-</td>
                                        </tr>
                                    </tbody>
                        </table>
                        `
            contenedorPDP.innerHTML = htmlPDP;
            /**---fin PDP--------- */

            /**---Gestiones--------- */
            let gestiones=res.gestiones;
            console.log(pdp);
            let porcentajeGestion1 = Math.round((parseInt(gestiones[0].can_clientes)/parseInt(cantidadTotal))*100);
            let porcentajeGestion2 = Math.round((parseInt(gestiones[1].can_clientes)/parseInt(cantidadTotal))*100);
            let porcentajeGestion3 = Math.round((parseInt(gestiones[2].can_clientes)/parseInt(cantidadTotal))*100);
            let htmlGes=``;
            htmlGes=`
                        <table class="table table-center table-sm table-striped table-hover pre-wrap table-responsive-lg" style="font-size:12px">
                                    <thead class="text-center">
                                        <tr class="">
                                            <th>Gestiones</th>
                                            <th>Clientes</th>
                                            <th>%</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center text-dark">
                                        <tr>
                                            <th scope="row">1</th>
                                            <td>${gestiones[0].can_clientes}</td>
                                            <td>${porcentajeGestion1}%</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">2</th>
                                            <td>${gestiones[1].can_clientes}</td>
                                            <td>${porcentajeGestion2}%</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">3+</th>
                                            <td>${gestiones[2].can_clientes}</td>
                                            <td>${porcentajeGestion3}%</td>
                                        </tr>
                                    </tbody>
                        </table>
                        `
            contenedorGestiones.innerHTML = htmlGes;
            /**---fin Gestiones--------- */

            /**---Pagos--------- */
            let pagos=res.pagos;
            console.log(pdp);
            let porcentajePagos = Math.round((parseInt(pagos[0].can_clientes)/parseInt(cantidadTotal))*100);
            let cantidadSinPagos=parseInt(cantidadTotal) - parseInt(pagos[0].can_clientes)
            let porcentajeSinPagos = Math.round((parseInt(cantidadSinPagos)/parseInt(cantidadTotal))*100);
            let htmlPagos=``;
            htmlPagos=`
                        <table class="table table-center table-sm table-striped table-hover pre-wrap table-responsive-lg" style="font-size:12px">
                                    <thead class="text-center">
                                        <tr class="">
                                            <th>Pagos</th>
                                            <th>Clientes</th>
                                            <th>%</th>
                                            <th>Monto Recuperado</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center text-dark">
                                        <tr>
                                            <th scope="row">Con Pagos</th>
                                            <td>${pagos[0].can_clientes}</td>
                                            <td>${porcentajePagos}%</td>
                                            <td>${pagos[0].monto_pago>0? "S/."+formatoMoneda(pagos[0].monto_pago):"-"}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Sin Pagos</th>
                                            <td>${cantidadSinPagos}</td>
                                            <td>${porcentajeSinPagos}%</td>
                                            <td>-</td>
                                        </tr>
                                    </tbody>
                        </table>
                        `
            contenedorPagos.innerHTML = htmlPagos;
            /**---fin Pagos--------- */

            /**---ubic--------- */
            let serverUbicabilidades=res.ubicabilidad;
            console.log(serverUbicabilidades);
            let ubicabilidades = [
                {name:"C-F-R-N",clients:0,percentage:0},
                {name:"Contacto PDP",clients:0,percentage:0},
                {name:"Contacto Positivo",clients:0,percentage:0},
                {name:"Contacto Otros",clients:0,percentage:0},
                {name:"No Disponible",clients:0,percentage:0},
                {name:"No Contacto",clients:0,percentage:0},
                {name:"Ilocalizados",clients:0,percentage:0},
                //{name:"Sin gestión",clients:0,percentage:0}
            ]
            
            /*const serverUbicabilidades = [
                {cant_clientes: 6, ubic: "No Contacto"},
              {cant_clientes: 1, ubic: "No Disponible"}
            ]
            */
            let totalClients = 0;
            
            //Obtener el total para generar los porcentajes
            serverUbicabilidades.forEach(item=> totalClients += parseInt(item.cant_clientes));
            
            ubicabilidades.forEach(item=>{
                const itemClient = serverUbicabilidades.find(el=>el.ubic.toLowerCase() == item.name.toLowerCase());
                console.log(itemClient);
                if(!itemClient) return;
                item.clients = parseInt(itemClient.cant_clientes);
                item.percentage = Math.round(parseInt(item.clients)/parseInt(cantidadTotal)*100);
            })
            
            console.log(ubicabilidades);

            let cantSinGestion=parseInt(cantidadTotal)-parseInt(totalClients);
            let porcentajeCantSinGestion=Math.round(parseInt(cantSinGestion)/parseInt(cantidadTotal)*100);
            let htmlUbic=``;
            htmlUbic=`
                        <table class="table table-center table-sm table-striped table-hover pre-wrap table-responsive-lg" style="font-size:12px">
                                    <thead class="text-center">
                                        <tr class="">
                                            <th>Ubicabilidad</th>
                                            <th>Clientes</th>
                                            <th>%</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center text-dark">`
            ubicabilidades.forEach((el)=>{
                            htmlUbic+=  `<tr>
                                            <th scope="row">${el.name}</th>
                                            <td>${el.clients}</td>
                                            <td>${el.percentage}%</td>
                                        </tr>`
            })
                            htmlUbic+=`<tr>
                                            <th scope="row">Sin gestión</th>
                                            <td>${cantSinGestion}</td>
                                            <td>${porcentajeCantSinGestion}%</td>
                                        </tr>
                                    </tbody>
                        </table>
                        `
            contenedorUbic.innerHTML = htmlUbic;
            /**---fin ubic--------- */
            $('#modal-editar').modal('show');
                $("#modal-editar").on("hidden.bs.modal", function(){
                    $(".body").html("");
                });
        })
}

function formatoMoneda(monto){
    let numeros = String(monto).split('.');
    let parteDecimal = numeros[1]? numeros[1]:'00';
    let parteEntera  = numeros[0];
    let montoFormateado = parteEntera;
    if(parteEntera.length>3){
      montoFormateado = parteEntera.substr(0,parteEntera.length - 3) + "," + parteEntera.substr(parteEntera.length - 3);
      parteEntera = montoFormateado;
    }
    if(parteEntera.length>7){
      montoFormateado = parteEntera.substr(0,parteEntera.length - 7) + "'" + parteEntera.substr(parteEntera.length - 7);
    }
  
    parteDecimal = Math.round(parseInt(parteDecimal)/Math.pow(10,(parteDecimal.length - 2)))
    if(String(parteDecimal).length==1) parteDecimal = parteDecimal + '0';
    return montoFormateado + '.' + parteDecimal;
}