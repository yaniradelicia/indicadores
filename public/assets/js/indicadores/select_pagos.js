console.log('Ready!!')

function validaForm(){
    //campo select
    if($("#cartera").val() == ""){
        alert("Seleccionar Cartera.");
        $("#cartera").focus(); 
        return false;
    }
    if($("#estructura").val() == ""){
        alert("Seleccionar una Estructura.");
        $("#estructura").focus();  
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

var pieChart = null;

const selectCartera = document.getElementById('cartera')
const selectEstructura = document.getElementById('estructura')
const selectInicio = document.getElementById('inicio')
const selectFin = document.getElementById('fin')


//const para mostrar info pagos
const contenedorPagos = document.getElementById('pagos');

//const para mostrar grafica pastel
const contenedorCanvas = document.getElementById('oilChart');

/*selectCartera.addEventListener('change',()=>{indicadoresGestion()});
selectEstructura.addEventListener('change',()=>{indicadoresGestion()});
selectInicio.addEventListener('change',()=>{indicadoresGestion()});
selectFin.addEventListener('change',()=>{indicadoresGestion()});*/

const botonBuscar = document.getElementById('buscar')
botonBuscar.addEventListener('click',(event)=>{event.preventDefault();indicadoresGestion()});

function indicadoresGestion(){
    console.log('--Cambio de valor en el select---')  
    if(pieChart!=null) pieChart.destroy();
    const valorSelectCartera = selectCartera.options[selectCartera.selectedIndex].value;
    const valorSelectEstructura = selectEstructura.options[selectEstructura.selectedIndex].value;
    const valorSelectInicio = selectInicio.value;
    const valorSelectFin = selectFin.value;
    //document.getElementById('spinner').show();
    console.log({valorSelectCartera})
    console.log({valorSelectEstructura})
    console.log({valorSelectInicio})
    console.log({valorSelectFin})

    if(validaForm()){
        cargando.style.display = '';
    //if(valorSelectCartera!="" && valorSelectEstructura!="" && valorSelectInicio!=""  && valorSelectFin!=""){
        fetch('buscarc_p/'+valorSelectCartera+'/'+valorSelectEstructura+'/'+valorSelectInicio+'/'+valorSelectFin)
        .then(res=>res.json())
        .then(res=>{
            cargando.style.display = 'none';
            console.log(res);
            let totalesQuery=res.totales;
            console.log(totalesQuery);
             res=res.datos;
            console.log(res);


            res.forEach(item=>{
                const itemTotal = totalesQuery.find(el=>el.tipo.toLowerCase() == item.tipo.toLowerCase());
                if(!itemTotal) return;
                item.capital_estructura_total = parseFloat(itemTotal.capital_estructura_total);
                item.importe_estructura_total = parseFloat(itemTotal.importe_estructura_total);
                item.clientes_estructura_total = parseInt(itemTotal.clientes_estructura_total);
            })
            console.log(res);
            //destruir grafica
            //if(pieChart!=null) pieChart.destroy();


            /*----------------para mostrar la grafica en porcentajes */
            let anios = [];//
            let datos = [];

            anios = [...new Set(res.map(el=>el.tipo))]
            let total = calcularTotal(res,'total_pagos');
            console.log({total})
            anios.forEach(anio=>{
                auxDatos = res.filter(el=>el.tipo == anio)
                console.log({auxDatos})
                clientes_total_estructura=calcularCantidadTotal(auxDatos)
                sumaCapital_estructura = calcularTotal(auxDatos,'capital_estructura_total')
                sumaImporte_estructura = calcularTotal(auxDatos,'importe_estructura_total')
                cant_pagos = calcularTotal(auxDatos,'cant_pagos')
                cantidad = calcularCantidad(auxDatos)
                suma_capital = calcularTotal(auxDatos,'total_capital')
                suma_deuda = calcularTotal(auxDatos,'total_deuda')
                suma_importe = calcularTotal(auxDatos,'total_importe')
                suma = calcularTotal(auxDatos,'total_pagos')    
                porcentaje = Math.round((suma/total)*100);
                datos.push({
                    anio:anio,
                    monto:suma,
                    porcentaje:porcentaje,
                    cantidad:cantidad,
                    cant_pagos:cant_pagos,
                    capital:suma_capital,
                    deuda:suma_deuda,
                    importe:suma_importe,
                    cli_est_total:clientes_total_estructura,
                    suma_cap_total:sumaCapital_estructura,
                    suma_ic_total:sumaImporte_estructura,

                })
            })
            //cli_est_total:0,suma_cap_total:0,suma_ic_total:0
            console.log(datos.length)
            console.log({datos})
            res = datos;
            console.log(res);
            /*----------------fin mostrar porcentaje en grafica*/

            /*-----------------agrupar por grupo los rangos------------------------------------- */
            /*if(valorSelectEstructura == 'saldo_deuda' || valorSelectEstructura == 'capital' || valorSelectEstructura == 'monto_camp'){
                let rangos = [
                  {anio:'A: [0-500>',cantidad:0,cant_pagos:0,monto:0,porcentaje:0,capital:0,deuda:0,importe:0,cli_est_total:0,suma_cap_total:0,suma_ic_total:0},
                  {anio:'B: [500-1000>',cantidad:0,cant_pagos:0,monto:0,porcentaje:0,capital:0,deuda:0,importe:0,cli_est_total:0,suma_cap_total:0,suma_ic_total:0},
                  {anio:'C: [1000-3000>',cantidad:0,cant_pagos:0,monto:0,porcentaje:0,capital:0,deuda:0,importe:0,cli_est_total:0,suma_cap_total:0,suma_ic_total:0},
                  {anio:'D: [3000-+>',cantidad:0,cant_pagos:0,monto:0,porcentaje:0,capital:0,deuda:0,importe:0,cli_est_total:0,suma_cap_total:0,suma_ic_total:0}
                ]
                let i = -1;
                res.forEach(el=>{
                  if(el.anio>=0 && el.anio<500) {i = 0}
                  else if(el.anio>=500 && el.anio<1000) {i = 1}
                  else if(el.anio>=1000 && el.anio<3000) {i = 2}
                  else {i = 3}
        
                  rangos[i].cantidad += parseInt(el.cantidad);
                  rangos[i].cant_pagos += parseInt(el.cant_pagos);
                  rangos[i].monto += parseFloat(el.monto);
                  rangos[i].porcentaje += parseFloat(el.porcentaje);
                  rangos[i].capital += parseFloat(el.capital);
                  rangos[i].deuda += parseFloat(el.deuda);
                  rangos[i].importe += parseFloat(el.importe);
                  rangos[i].cli_est_total += parseInt(el.cli_est_total);
                  rangos[i].suma_cap_total += parseFloat(el.suma_cap_total);
                  rangos[i].suma_ic_total += parseFloat(el.suma_ic_total);
                  
                })      
                console.log({rangos})  
                //res = rangos;
                res = rangos.filter(el=>el.porcentaje>0);
            }*/
            /*--------------------fin agrupar por grupos los rangos--------------------- */


            /*------------agrupar menores de 2015 en tramo--------------------------------- */

            if(valorSelectEstructura == "tramo"){
                let cantidades = {cantidad:0,cant_pagos:0,porcentaje:0,capital:0,deuda:0,importe:0,monto:0,cli_est_total:0,suma_cap_total:0,suma_ic_total:0};
                let index = -1;
                res.forEach((el,i)=>{
                    if(isNaN(el.anio)==false){
                        if(parseInt(el.anio)==2016) index = i;
                        if(parseInt(el.anio)<=2016){
                        cantidades.cantidad   += parseInt(el.cantidad);
                        cantidades.cant_pagos   += parseInt(el.cant_pagos);
                        cantidades.porcentaje += el.porcentaje;
                        cantidades.monto += parseFloat(el.monto);
                        cantidades.capital += parseFloat(el.capital);
                        cantidades.deuda += parseFloat(el.deuda);
                        cantidades.importe += parseFloat(el.importe);
                        cantidades.cli_est_total += parseInt(el.cli_est_total);
                        cantidades.suma_cap_total += parseFloat(el.suma_cap_total);
                        cantidades.suma_ic_total += parseFloat(el.suma_ic_total);
                        }
                    }
                })

                if(index!=-1){
                    cantidades.anio = 2016;
                    res[index] = cantidades;
                }else{
                    cantidades.anio = 2016;
                    res.unshift(cantidades);
                }

                res = res.filter(el=>{
                    if(isNaN(el.anio)) return true;
                    
                    return parseInt(el.anio)>=2016 && el.cantidad>0;
                    
                })
                console.log(res)
            }


            /*-------------------fin agrupar 2015 por tramo---------------- */

            console.log("Antes de ordenar: ",res);
            res.sort((a, b) => b.monto - a.monto);
            console.log("Despues de ordenar: ",res);

            /*
            cli_est_total
            suma_cap_total
            suma_ic_total
            * */

            //---------------obtener tabla info ---------------------
            let totales = {cantidad:0,cant_pagos:0,capital:0,deuda:0,importe:0,monto:0,cli_est_total:0,suma_cap_total:0,suma_ic_total:0}
            let html = `<table class="table table-sm table-striped table-hover table-responsive-lg">
                            <thead class="cab-camp text-center" style="font-size:11px;">
                                <tr>
                                    <th scope="col">ESTRUCTURA</th>
                                    <th scope="col">CLIENTES TOTAL</th>
                                    <th scope="col">CAPITAL TOTAL</th>
                                    <th scope="col">IC TOTAL</th>
                                    <th scope="col">CLIENTES C/ PAGO</th>
                                    <th scope="col">CAPITAL</th>
                                    <th scope="col">IC</th>
                                    <th scope="col">MONTO PAGO</th>
                                    <th scope="col">%CLIENTES</th>
                                    <th scope="col">%RECUPERO</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">`
            res.forEach(el=>{
            totales.cantidad += parseInt(el.cantidad);
            totales.cant_pagos += parseInt(el.cant_pagos);
            totales.capital  += parseFloat(el.capital);
            totales.deuda    += parseFloat(el.deuda);
            totales.importe  += parseFloat(el.importe);
            totales.monto    += parseFloat(el.monto);
            totales.cli_est_total += parseInt(el.cli_est_total);
            totales.suma_cap_total += parseFloat(el.suma_cap_total);
            totales.suma_ic_total += parseFloat(el.suma_ic_total);
            html += 
                                `<tr>
                                    <th scope="row">${el.anio}</th>
                                    <th scope="row">${el.cli_est_total}</th>
                                    <th scope="row">${formatoMoneda(el.suma_cap_total)}</th>
                                    <th scope="row">${formatoMoneda(el.suma_ic_total)}</th>
                                    <td>${el.cantidad}</td>
                                    <td>${formatoMoneda(el.capital)}</td>
                                    <td>${formatoMoneda(el.importe)}</td>
                                    <td>${formatoMoneda(el.monto)}</td>
                                    <td>${parseInt(el.cantidad)/parseInt(el.cli_est_total)>0? Math.round10((parseInt(el.cantidad)/parseInt(el.cli_est_total)*100),-2):'0'}%</td>
                                    <td>${parseFloat(el.monto)/parseFloat(el.suma_ic_total)>0? Math.round10((parseFloat(el.monto)/parseFloat(el.suma_ic_total)*100),-2):'0'}%</td>
                                </tr>`
            })        
            html += 
                            `</tbody>
                            <thead class="footer-total text-center">
                                <tr>
                                    <th scope="row">TOTAL</th>
                                    <th scope="row">${totales.cli_est_total}</th>
                                    <th scope="row">${formatoMoneda(totales.suma_cap_total)}</th>
                                    <th scope="row">${formatoMoneda( totales.suma_ic_total)}</th>
                                    <th scope="row">${totales.cantidad}</th>
                                    <th scope="row">${formatoMoneda(totales.capital)}</th>
                                    <th scope="row">${formatoMoneda(totales.importe)}</th>
                                    <th scope="row">${formatoMoneda(totales.monto)}</th>
                                    <th>${parseInt(totales.cantidad)/parseInt(totales.cli_est_total)>0? Math.round10((parseInt(totales.cantidad)/parseInt(totales.cli_est_total)*100),-2):'0'}%</th>
                                    <th>${parseFloat(totales.monto)/parseFloat(totales.suma_ic_total)>0? Math.round10((parseFloat(totales.monto)/parseFloat(totales.suma_ic_total)*100),-2):'0'}%</th>
                                </tr>
                            </thead>
                        </table>`                  
    
            contenedorPagos.innerHTML = html
            /*--------------------fin tabla-------------------------------------- */

            /*-----------------------obtener datos para la grafica------------------------- */
            let datos_tipo=[];
            let datos_cantidad=[];
            let datos_suma_otros=[];

            /*----ordenar por porcentajes*/
            console.log("Antes de ordenar: ",res);
            res.sort((a, b) => b.porcentaje - a.porcentaje);
            console.log("Despues de ordenar: ",res);

            res.forEach(el => {
                //console.log(el)
                datos_tipo.push(el.anio+ ' -  S/.'+formatoMoneda(el.monto));
                datos_cantidad.push(parseFloat(el.porcentaje))
                datos_suma_otros.push(parseFloat(el.monto));
                
            });

            console.log(datos_tipo);
            console.log(datos_cantidad);
            console.log(datos_suma_otros);

            /*--------------ordenar los 7 primeros------------------*/
            console.log(datos_tipo.length);
            if(datos_tipo.length>7){
                const otros = datos_suma_otros.slice(7);
                sumaotros=0;
                otros.forEach (function(numero){
                    sumaotros += parseFloat(numero);
                });
                console.log(sumaotros);
                //extraer 7 primeros labels y datos
                const labels1 = datos_tipo.slice(0, 7);
                console.log("7 labels: ",labels1);
                const labels2='OTROS - '+formatoMoneda(sumaotros);
                labels1.push(labels2);
                datos_tipo=labels1;
                console.log(datos_tipo);

                const datos1 = datos_cantidad.slice(0,7);
                console.log("7 datos: ",datos1);
                const datos2 = datos_cantidad.slice(7);
                suma_datos2 = 0;
                datos2.forEach (function(numero){
                    suma_datos2 += numero;
                });
                console.log('otros',suma_datos2);
                datos1.push(suma_datos2);
                datos_cantidad=datos1;
                console.log(datos_cantidad);
            }else{
                datos_tipo=datos_tipo;
                console.log(datos_tipo);
                datos_cantidad=datos_cantidad;
                console.log(datos_cantidad);
            }
            /*--------------fin ordenar los 7 primeros----------------------------*/

            //grafica pastell

            var oilCanvas = document.getElementById("oilChart");

            Chart.defaults.global.defaultFontFamily = "Lato";
            Chart.defaults.global.defaultFontSize = 18;

            var oilData = {
                labels: 
                    datos_tipo
                ,
                datasets: [
                    {
                        data: datos_cantidad,
                        backgroundColor: [
                            "#D46574",//rojo
                                "#F7DC6F",//amarillo
                                "#AED6F1",//celeste
                                "#58D68D",//verde
                                "#F1948A",//rosa
                                "#FD902F",//naranja
                                "#C39BD3",//lila
                                "#B2BABB"//gris
                        ]
                    }]
            };

            var chartOptions = {
                legend: {
                    position: 'top',
                    labels: {
                        //fontColor: "white",
                        fontSize: 16
                    }
                },
                responsive: true,
                maintainAspectRatio: false ,
                events: false,
                tooltips: {
                    enabled: false
                },
                hover: {
                    animationDuration: 0
                },
                animation: {
                    duration: 0,
                    onComplete: function () {
                      var self = this,
                          chartInstance = this.chart,
                          ctx = chartInstance.ctx;
               
                      ctx.font = '18px Arial';
                      ctx.textAlign = "center";
                      ctx.fillStyle = "#17202A";
               
                      Chart.helpers.each(self.data.datasets.forEach(function (dataset, datasetIndex) {
                          var meta = self.getDatasetMeta(datasetIndex),
                              total = 0, //total values to compute fraction
                              labelxy = [],
                              offset = Math.PI / 2, //start sector from top
                              radius,
                              centerx,
                              centery, 
                              lastend = 0; //prev arc's end line: starting with 0
               
                          for (var val of dataset.data) { total += val; } 
               
                          Chart.helpers.each(meta.data.forEach( function (element, index) {
                              radius = 0.9 * element._model.outerRadius - element._model.innerRadius;
                              centerx = element._model.x;
                              centery = element._model.y;
                              var thispart = dataset.data[index],
                                  arcsector = Math.PI * (2 * thispart / total);
                              if (element.hasValue() && dataset.data[index] > 0) {
                                labelxy.push(lastend + arcsector / 2 + Math.PI + offset);
                              }
                              else {
                                labelxy.push(-1);
                              }
                              lastend += arcsector;
                          }), self)
               
                          var lradius = radius * 3 / 4;
                          for (var idx in labelxy) {
                            if (labelxy[idx] === -1) continue;
                            if(dataset.data[idx] >= 1){//para que muestre en la torta los mayores a 3%
                                var langle = labelxy[idx],
                                    dx = centerx + lradius * Math.cos(langle),
                                    dy = centery + lradius * Math.sin(langle),
                                    val = Math.round(dataset.data[idx] / total * 100);
                                    console.log(val);
                                ctx.fillText(val + '%', dx, dy);
                            }
                          }
               
                      }), self);
                    }
               },
            }

            pieChart = new Chart(oilCanvas, {
            type: 'pie',
            data: oilData,
            options: chartOptions
            });

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

/*------------------------funcionesss-------------------------*/
function calcularTotal(arr,prop){
    let total = 0;
    arr.forEach(el=>{
      total += parseFloat(el[prop])
    })
    return total;
}
  
function calcularCantidad(arr){
    let total = 0;
    arr.forEach(el=>{
      total += parseInt(el.cantidad);
    })
    return total;
}

function calcularCantidadTotal(arr){
    let total = 0;
    arr.forEach(el=>{
      total += parseInt(el.clientes_estructura_total);
    })
    return total;
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


/*function formatoMoneda(monto){
    let numeros = String(monto).split('.');
    let parteDecimal = numeros[1]? numeros[1]:'00';
    let parteEntera  = numeros[0];
    let montoFormateado = '';
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
}*/









/*function calcularTotal(arr){
    let total = 0;
    arr.forEach(el=>{
      total += parseFloat(el.pag_cli_mon)*parseInt(el.cantidad);
    })
    return total;
}
  
function calcularCantidad(arr){
    let total = 0;
    arr.forEach(el=>{
      total += parseInt(el.cantidad);
    })
    return total;
}

function calcularCapital(arr){
    let total = 0;
    arr.forEach(el=>{
      total += parseFloat(el.total_capital);
    })
    return total;
}

function calcularDeuda(arr){
    let total = 0;
    arr.forEach(el=>{
      total += parseFloat(el.total_deuda);
    })
    return total;
}

function calcularImporte(arr){
    let total = 0;
    arr.forEach(el=>{
      total += parseFloat(el.total_importe);
    })
    return total;
}*/
