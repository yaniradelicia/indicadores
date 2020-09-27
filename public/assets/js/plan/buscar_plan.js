function validaForm(){
    //campo select
    if($("#cartera").val() == ""){
        alert("Seleccionar Cartera.");
        $("#cartera").focus();    
        return false;
    }
    if($("#fec_i").val() == ""){
        alert("Colocar Fecha Inicio.");
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


const botonBuscar = document.getElementById('buscar')
const cargandoFiltro= document.getElementById('cargando-filtro')
const cargandoResultado= document.getElementById('cargando-resultado')
const cargandoDetalle= document.getElementById('cargando-detalle')
const cargandoUsuario= document.getElementById('cargando-usuario')
const contenedorTabla = document.getElementById('tabla');
const contenedorPag = document.getElementById('developer_page');
botonBuscar.addEventListener('click',(event)=>{event.preventDefault();selectBuscar()});

function selectBuscar(){
    contenedorTabla.innerHTML = '';
    const selectCartera = document.getElementById('cartera')
    const selectFec_i = document.getElementById('fec_i')
    const selectFec_f = document.getElementById('fec_f')

    const valorSelectCartera = selectCartera.options[selectCartera.selectedIndex].value;
    const valorSelectFec_i = selectFec_i.value;
    const valorSelectFec_f = selectFec_f.value;

    console.log({valorSelectCartera})
    console.log({valorSelectFec_i})
    console.log({valorSelectFec_f})

    if(validaForm()){
        cargandoFiltro.style.display = '';
        fetch('/indicadores/public/plan/filtro?car='+(valorSelectCartera || null)+'&fec_i='+(valorSelectFec_i || null)+'&fec_f='+(valorSelectFec_f || null))
        //fetch('/plan/filtro?car='+(valorSelectCartera || null)+'&fec_i='+(valorSelectFec_i || null)+'&fec_f='+(valorSelectFec_f || null))
        .then(res=>res.json())
            .then(res=>{
                cargandoFiltro.style.display = 'none';
                contenedorTabla.innerHTML = '';
                let htmlTable = ``;

                htmlTable = `<table class="table table-sm table-bordered table-responsive-sm">
                                <thead class="cab-camp text-center" style="font-size:12px;">
                                    <tr>
                                        <th>FECHA</th>
                                        <th>CARTERA</th>
                                        <th>NOMBRE</th>
                                        <th></th>
                                        <th ></th>
                                    </tr>                                   
                                </thead>
                                <tbody class="text-center" style="font-size:12px;" id="paginas">`
                res.forEach((el)=>{
                    htmlTable+=     `<tr>
                                        <td>${el.fecha_i}</td>
                                        <td>${el.nombre_cartera}</td>
                                        <td>${el.nombre_plan}</td>
                                        <td>
                                            <a class="btn text-dark" role="button" data-toggle="modal" data-target="#modalDetalle" onclick="mostrarDetalle(${el.id_plan})">
                                                <span class="badge badge-pill badge-info">Detalle</span>
                                            </a>
                                        </td>
                                        <td>
                                            <a class="btn text-dark" role="button" data-toggle="modal" data-target="#modalResultado" onclick="mostrarResultado(${el.id_plan},${el.id_cartera},${el.cant_clientes})">
                                                <span class="badge badge-pill badge-info">Resultados</span>
                                            </a>
                                        </td>
                                    </tr>`
                })
                htmlTable +=     `</tbody>
                                
                    </table>`;

                contenedorTabla.innerHTML = htmlTable;
                /*$(document).ready(function() {
                    $('#paginas').pageMe({
                    pagerSelector: '#developer_page',
                    showPrevNext: true,
                    hidePageNumbers: false,
                    perPage: 3
                    });
                });*/
            })
    }
}

function mostrarDetalle(id){
    cargandoDetalle.style.display = '';
    console.log(id);
    const contenedorDetalle = document.getElementById('contenedor-detalle');
    const contenedorCantidad = document.getElementById('contenedor-cantidad');
    const contenedorFecha = document.getElementById('contenedor-fecha');
    
    fetch('/indicadores/public/plan/mostrar_detalle?id='+id)
    //fetch('/plan/mostrar_detalle?id='+id)
    .then(res=>res.json())
        .then(res=>{
            cargandoDetalle.style.display = 'none';
            console.log(res);
            const texto =res[0].detalle;
            console.log(res[0].detalle);

            const categorias = texto.split(";");
            const objCategorias = {};
            categorias.forEach(categoria => {
                const auxCategorias = categoria.split(":");
                objCategorias[auxCategorias[0]] = auxCategorias[1].split(",");
            });

            console.log(objCategorias)

            // Cómo guardar como json 

            const jsonToString = JSON.stringify(objCategorias);

            console.log('===== JSON A STRING ====');
            console.log(jsonToString);

            // Cómo dibujarlo en tu HTML. 
            // Primero el string tiene que regresar a json 
            const objetoReporte = JSON.parse(jsonToString);


            /*let html = '';
            Object.keys(objetoReporte).forEach(key => {
            html += `<p><span> ${key}:  <span>  ${objetoReporte[key].join('; ')} <p>`
            })*/

            //document.getElementById('reporte').innerHTML = html;
            
            let htmlDetalle=``;
            htmlDetalle=`
                        <table class="table table-center table-sm table-striped table-hover pre-wrap table-responsive-lg" style="font-size:12px">
                                    <tbody class="text-left text-dark">
                                        <tr>
                                            <th scope="row">CARTERA:</th>
                                            <td>${res[0].nombre_cartera}</td>
                                        </tr>`
            Object.keys(objetoReporte).forEach(key => {
                htmlDetalle+=`
                                        <tr>
                                            <th scope="row">${key}:</th>
                                            <td>${objetoReporte[key].join(' ; ')}</td>
                                        </tr>
                                        `
            })
            htmlDetalle+=               `<tr>
                                            <th scope="row">Speech:</th>
                                            <td>${res[0].speech}</td>
                                        </tr>
                                    </tbody>
                        </table>
                        `
            contenedorDetalle.innerHTML = htmlDetalle;
            let htmlCant=``;
            htmlCant=`<b style="font-size:16px;">${res[0].cant_clientes} CLIENTES</b>`
            contenedorCantidad.innerHTML = htmlCant;

            let htmlfecha=``;
            htmlfecha=`<b style="font-size:16px;">${res[0].fecha_i}</b>`
            contenedorFecha.innerHTML = htmlfecha;

            $('#modalDetalle').modal('show');
                $("#modalDetalle").on("hidden.bs.modal", function(){
                    $(".cont-body").html("");
                });
        })
}

function mostrarResultado(id,car,cant){
    cargandoResultado.style.display = '';
    
    console.log(id);
    console.log(car);
    let cantidadTotal=cant;
    //let fechaR=fecha;
    const contenedorResultado = document.getElementById('contenedor-resultado');
    const contenedorCantidadR = document.getElementById('contenedor-cantidad-r');
    const contenedorFechaR = document.getElementById('contenedor-fecha-r');

    fetch('/indicadores/public/plan/mostrar_resultado?id='+id+'&car='+car)
    //fetch('/plan/mostrar_resultado?id='+id+'&car='+car)
    .then(res=>res.json())
        .then(res=>{
            cargandoResultado.style.display = 'none';
            let cobertura=res.cobertura;
            console.log(cobertura);
            let porcentajeCobertura = Math.round((parseInt(cobertura[0].can_clientes)/parseInt(cantidadTotal))*100);
            let porcentajeIntensidad = Math.round10((parseInt(cobertura[0].cant_gestiones)/parseInt(cobertura[0].can_clientes)),-1);
            console.log(porcentajeIntensidad);
            
            let contacto=res.contacto;
            console.log(contacto);
            let porcentajeContacto = Math.round((parseInt(contacto[0].can_clientes)/parseInt(cantidadTotal))*100);

            let pdp=res.pdp;
            console.log(pdp);
            let conf=res.conf;
            console.log(conf);

            let usuario=res.usuario;
            console.log(usuario);

            let negocio=res.negocio;
            console.log(negocio);
            let porcentajeNegocio = Math.round((parseInt(negocio[0].can_clientes)/parseInt(cantidadTotal))*100);
            /*let UsuarioCod=[];
            usuario.forEach((u)=>{
                UsuarioCod=u.emp_cod;
            })
            console.log(UsuarioCod);
            let cadenaUsuario=UsuarioCod.join();
            console.log(cadenaUsuario);*/

            let htmlResul=``;
            htmlResul=`
                        <table class="table table-center table-md table-striped table-hover pre-wrap table-responsive-lg" style="font-size:12px">
                                    <tbody class="text-left text-dark" style="font-size:14px !important;">
                                        <tr>
                                            <th scope="row">Cobertura:</th>
                                            <td>${porcentajeCobertura>0? porcentajeCobertura:'0'}%</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Contactabilidad:</th>
                                            <td>${porcentajeContacto>0? porcentajeContacto:'0'}%</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">PDPS:</th>
                                            <td>${pdp[0].monto_pdp>0? formatoNumero(pdp[0].can_pdp)+"; S/."+formatoMoneda(pdp[0].monto_pdp):'0 ; S/.0'}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Confirmaciones:</th>
                                            <td>${conf[0].monto_conf>0? formatoNumero(conf[0].can_conf)+"; S/."+formatoMoneda(conf[0].monto_conf):'0 ; S/.0'}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Intensidad:</th>
                                            <td>${porcentajeIntensidad>0? porcentajeIntensidad:'0'}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">En Negociación:</th>
                                            <td>${negocio[0].can_mot_np>0? negocio[0].can_mot_np>0+";"+porcentajeNegocio+"%":'0 ; 0%'}</td>
                                        </tr>
                                        <tr>
                                            <th style="vertical-align: middle;">Usuarios:</th>
                                            <td>`
                usuario.forEach((el)=>{
                        htmlResul+=`<a class="btn btn-xs text-dark" role="button" 
                                                    data-toggle="modal" data-target="#modalUsuario"
                                                    onclick="mostrarUsuario(${el.emp_cod},${id},${car},${el.cantidad})">
                                                    <p><b>${el.emp_cod}</b></p>
                                                </a>`
                })
                htmlResul+=`</td>
                                        </tr>
                                    </tbody>
                        </table>
                        `
                contenedorResultado.innerHTML = htmlResul;
 
                let htmlCantR=``;
                htmlCantR=`<b style="font-size:16px;">${cantidadTotal} CLIENTES</b>`
                contenedorCantidadR.innerHTML = htmlCantR;

                /*let htmlfecha=``;
                htmlfecha=`<p>${fechaR}</p>`
                contenedorFechaR.innerHTML = htmlfecha;*/

                $('#modalResultado').modal('show');
                $("#modalResultado").on("hidden.bs.modal", function(){
                    $(".cont-body-r").html("");
                });

                //$('[data-toggle="popover"]').popover({ html : true });
                /*$(document).ready(function(){
                    $('[data-toggle="popover"]').popover({ html : true });
                    });*/
                    /*$("[data-toggle=popover]").popover({
                        html : true,
                        trigger: 'focus',
                        content: function() {
                            var content = $(this).attr("data-popover-content");
                            return $(content).children(".popover-body").html();
                        }
                    });*/
        })

}

function mostrarUsuario(cod,id,car,cant){
    cargandoUsuario.style.display = '';
    console.log(cod);
    console.log(id);
    console.log(car);
    console.log(cant);
    let cantidadTotal=cant;
    let codigo=cod;
    //const contenedorUsuario = document.getElementById('popover-contenido');
    const contenedorUsuario = document.getElementById('contenedor-usuario');
    const contenedorCodigo = document.getElementById('contenedor-codigo');
    const contenedorCantidadU = document.getElementById('contenedor-cantidad-u');
    const contenedorFechaU = document.getElementById('contenedor-fecha-u');
    
    fetch('/indicadores/public/plan/mostrar_usuario?id='+id+'&car='+car+'&cod='+cod)
    //fetch('/plan/mostrar_usuario?id='+id+'&car='+car+'&cod='+cod)
    .then(res=>res.json())
        .then(res=>{
            cargandoUsuario.style.display = 'none';
            let cobertura=res.cobertura;
            console.log(cobertura);
            let porcentajeCobertura = Math.round((parseInt(cobertura[0].can_clientes)/parseInt(cantidadTotal))*100);
            let porcentajeIntensidad = Math.round10((parseInt(cobertura[0].cant_gestiones)/parseInt(cobertura[0].can_clientes)),-1);
            console.log(porcentajeIntensidad);
            
            let contacto=res.contacto;
            console.log(contacto);
            let porcentajeContacto = Math.round((parseInt(contacto[0].can_clientes)/parseInt(cantidadTotal))*100);

            let pdp=res.pdp;
            console.log(pdp);
            let conf=res.conf;
            console.log(conf);

            let negocio=res.negocio;
            console.log(negocio);
            let porcentajeNegocio = Math.round((parseInt(negocio[0].can_clientes)/parseInt(cantidadTotal))*100);

            let htmlUsu=``;
            htmlUsu=`
                        <table class="table table-center table-sm table-striped table-hover pre-wrap table-responsive-lg" style="font-size:12px">
                                    <tbody class="text-left text-dark">
                                        <tr>
                                            <th scope="row">Cobertura:</th>
                                            <td>${porcentajeCobertura>0? porcentajeCobertura:'0'}%</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Contactabilidad:</th>
                                            <td>${porcentajeContacto>0? porcentajeContacto:'0'}%</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">PDPS:</th>
                                            <td>${pdp[0].monto_pdp>0? formatoNumero(pdp[0].can_pdp)+"; S/."+formatoMoneda(pdp[0].monto_pdp):'0 ; S/.0'}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Confirmaciones:</th>
                                            <td>${conf[0].monto_conf>0? formatoNumero(conf[0].can_conf)+"; S/."+formatoMoneda(conf[0].monto_conf):'0 ; S/.0'}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Intensidad:</th>
                                            <td>${porcentajeIntensidad>0? porcentajeIntensidad:'0'}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">En Negociación:</th>
                                            <td>${negocio[0].can_mot_np>0? negocio[0].can_mot_np>0+";"+porcentajeNegocio+"%":'0 ; 0%'}</td>
                                        </tr>
                                    </tbody>
                        </table>
                        `
                contenedorUsuario.innerHTML = htmlUsu;
                let htmlCant=``;
                htmlCant=`<b style="font-size:13px;">${cantidadTotal} Clientes</b>`
                contenedorCantidadU.innerHTML = htmlCant;

                let htmlCod=``;
                htmlCod=`<b style="font-size:13px;">U: ${codigo}</b>`
                contenedorCodigo.innerHTML = htmlCod;
                /*$(document).ready(function(){
                    $('[data-toggle="popover"]').popover({ html : true });
                    });*/
                    $('#modalUsuario').modal('show');
                    $("#modalUsuario").on("hidden.bs.modal", function(){
                    $(".cont-body-u").html("");
                });
        })

}

(function() {
    function decimalAdjust(type, value, exp) {
        // Si el exp no está definido o es cero...
        if (typeof exp === 'undefined' || +exp === 0) {
          return Math[type](value);
        }
        value = +value;
        exp = +exp;
        // Si el valor no es un número o el exp no es un entero...
        if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) {
          return NaN;
        }
        // Shift
        value = value.toString().split('e');
        value = Math[type](+(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp)));
        // Shift back
        value = value.toString().split('e');
        return +(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp));
      }
    if (!Math.round10) {
        Math.round10 = function(value, exp) {
          return decimalAdjust('round', value, exp);
        };
      }

})();


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

function formatoNumero(monto){
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
  
    return montoFormateado;
}