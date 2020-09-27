console.log('Ready!!')
function validaForm(){
    //campo select
    if($("#cartera").val() == ""){
        alert("Seleccionar Cartera.");
        $("#cartera").focus(); 
        return false;
    }

    if($("#inicio").val() == ""){
        alert("Seleccionar fecha de Inicio.");
        $("#inicio").focus();
        return false;
    }

    if($("#fin").val() == ""){
        alert("Seleccionar fecha Fin.");
        $("#fin").focus();
        return false;
    }
    return true;
}
const selectCartera = document.getElementById('cartera')
const selectInicio = document.getElementById('inicio')
const selectFin = document.getElementById('fin')

//const para mostrar info pagos
const contenedorRecupero = document.getElementById('recupero');

/*selectCartera.addEventListener('change',()=>{indicadoresGestion()});
selectInicio.addEventListener('change',()=>{indicadoresGestion()});
selectFin.addEventListener('change',()=>{indicadoresGestion()});*/

const botonBuscar = document.getElementById('buscar')
botonBuscar.addEventListener('click',(event)=>{event.preventDefault();indicadoresGestion()});

function indicadoresGestion(){
    console.log('--Cambio de valor en el select---')  
    contenedorRecupero.innerHTML = '';
    const valorSelectCartera = selectCartera.options[selectCartera.selectedIndex].value;
    const valorSelectInicio = selectInicio.value;
    const valorSelectFin = selectFin.value;
    console.log({valorSelectCartera})
    console.log({valorSelectInicio})
    console.log({valorSelectFin})

    //if(valorSelectCartera!="" && valorSelectInicio!=""  && valorSelectFin!=""){
    if(validaForm()){
        let url = '';
        if(valorSelectCartera=='34' || valorSelectCartera=='88' || valorSelectCartera=='2' || valorSelectCartera=='89' ||
            valorSelectCartera=='70' ||  valorSelectCartera=='20' || valorSelectCartera=='72' || valorSelectCartera=='5'){
            url = 'buscarc_recp/'+valorSelectCartera+'/'+valorSelectInicio+'/'+valorSelectFin
        }else{
            url = 'buscarc_recc/'+valorSelectCartera+'/'+valorSelectInicio+'/'+valorSelectFin
        }
        cargando.style.display = '';
        fetch(url)
        .then(res=>res.json())
        .then(res=>{
            cargando.style.display = 'none';
            console.log(res);
            //let datos=res.datos;
            //console.log(datos);
            //let tabla = [];
            //tabla=res[0].porcentajes.map(dato=>dato.pago_ideal);
            let alcance = Math.round(res[0].alcance);

            contenedorRecupero.innerHTML = '';
            let htmlTable = `<table class="table table-sm table-striped table-hover table-responsive-sm">
                                <thead class="cab-camp text-center">
                                <tr>
                                    <th scope="col">Meta</th>
                                    <th scope="col">Recupero</th>
                                    <th scope="col">Alcance</th>
                                </tr>
                                </thead>
                                <tbody class="text-center">`

            //datos.forEach((el,index)=>{
                                htmlTable += `<tr>
                                    <th scope="row">S/.${formatoMoneda(res[0].meta)}</th>
                                    <td>S/.${formatoMoneda(res[0].recupero)}</td>
                                    <td>${alcance}%</td>
                                </tr>`
            //})

            htmlTable += '</tbody></table>`';

            contenedorRecupero.innerHTML = htmlTable;
        })
    }
}

function verPagos(){
  console.log('--hola---')
  const contenedorPagos = document.getElementById('contenedor-pagos');
  fetch('ver_pagos')
        .then(res=>res.json())
        .then(res=>{
            console.log(res);
            let carteras=res.carteras;
            console.log(carteras);
            let carterasPagos=res.carterasPagos;
            console.log(carterasPagos);
            let carterasCon=res.carterasCon;
            console.log(carterasCon);
            let pagos=res.pagos;
            console.log(pagos);

            carterasPagos.forEach(item=>{
              let itemCartera = pagos.find(el=>parseInt(el.car_id) == parseInt(item.car_id));
              console.log(itemCartera);
              if(!itemCartera) return;
              item.fecha = itemCartera.fecha;
            })
            console.log(carterasPagos);
            let htmlContPagos=``;
            htmlContPagos=`
            <table class="table table-sm table-borderless table-hover table-responsive-sm" style="font-size:12px">
                                    <thead class="text-center">
                                        <tr class="">
                                            <th>Cartera</th>
                                            <th>Última Fecha de Pago</th>
                                            <th class='text-white'>-----</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center text-dark">`
            carterasPagos.forEach((el)=>{
              if(el.fecha!='Sin Pagos a la Fecha'){
                htmlContPagos+=  `<tr>
                                              <th scope="row">${el.car_nom}</th>
                                              <td style='color:blue'>${el.fecha}</td>
                                              
                                          </tr>`
              }else{
                htmlContPagos+=  `<tr>
                                              <th scope="row">${el.car_nom}</th>
                                              <td style='color:red'>${el.fecha}</td>
                                          </tr>`
              }
            })
                        
        
            htmlContPagos+=`
                                    </tbody>
                                </table>`;
            contenedorPagos.innerHTML = htmlContPagos;
            $("#modalPagos").on("hidden.bs.modal", function(){
                $(".cont-pagos").html("");
            });

        })
}

function verCarteras(){
  console.log('--hola---')
  const contCarteras = document.getElementById('cont-carteras');
  fetch('ver_carteras')
        .then(res=>res.json())
        .then(res=>{
            console.log(res);
            let carteras=res.carteras;
            console.log(carteras);
            let carterasCargadas=res.carterasCargadas;
            console.log(carterasCargadas);

            carteras.forEach(item=>{
              let itemEstado = carterasCargadas.find(el=>parseInt(el.car_id) == parseInt(item.car_id));
              console.log(itemEstado);
              if(!itemEstado) return;
              item.estado = itemEstado.estado;
            })
            console.log(carteras);
            let htmlContCar=``;
            htmlContCar=`
            <table class="table table-sm table-borderless table-hover table-responsive-sm" style="font-size:12px">
                                    <thead class="text-center">
                                        <tr class="pl-0">
                                            <th>Cartera</th>
                                            <th class='text-white'>-----------</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center text-dark">`
            carteras.forEach((el)=>{
              if(el.estado=='cargadas'){
                htmlContCar+=  `<tr>
                                            <th scope="row">${el.car_nom}</th>
                                            <td><i class='fas fa-circle' style='color:green'></i></td>
                                        </tr>`
              }else{
                htmlContCar+=  `<tr>
                                              <th scope="row">${el.car_nom}</th>
                                              <td><i class='fas fa-circle' style='color:red'></td>
                                          </tr>`
              }
            })
                        
        
            htmlContCar+=`
                                    </tbody>
                                </table>`;
            contCarteras.innerHTML = htmlContCar;
            $("#modalCarteras").on("hidden.bs.modal", function(){
                $(".cont-car").html("");
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