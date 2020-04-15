console.log('Ready!!')

var pieChart = null;

const selectCartera = document.getElementById('cartera')
const selectEstructura = document.getElementById('estructura')
const selectInicio = document.getElementById('inicio')
const selectFin = document.getElementById('fin')


//const para mostrar info pagos
const contenedorPagos = document.getElementById('pagos');

//const para mostrar grafica pastel
const contenedorCanvas = document.getElementById('oilChart');

selectCartera.addEventListener('change',()=>{indicadoresGestion()});
selectEstructura.addEventListener('change',()=>{indicadoresGestion()});
selectInicio.addEventListener('change',()=>{indicadoresGestion()});
selectFin.addEventListener('change',()=>{indicadoresGestion()});

function indicadoresGestion(){
    console.log('--Cambio de valor en el select---')  
    const valorSelectCartera = selectCartera.options[selectCartera.selectedIndex].value;
    const valorSelectEstructura = selectEstructura.options[selectEstructura.selectedIndex].value;
    const valorSelectInicio = selectInicio.value;
    const valorSelectFin = selectFin.value;
    //document.getElementById('spinner').show();
    console.log({valorSelectCartera})
    console.log({valorSelectEstructura})
    console.log({valorSelectInicio})
    console.log({valorSelectFin})

    if(valorSelectCartera!="" && valorSelectEstructura!="" && valorSelectInicio!=""  && valorSelectFin!=""){
        fetch('buscarc_p/'+valorSelectCartera+'/'+valorSelectEstructura+'/'+valorSelectInicio+'/'+valorSelectFin)
        .then(res=>res.json())
        .then(res=>{
            console.log(res)

            //destruir grafica
            if(pieChart!=null) pieChart.destroy();


            /*----------------para mostrar la grafica en porcentajes */
            let anios = [];//
            let datos = [];

            anios = [...new Set(res.map(el=>el.tipo))]
            let total = calcularTotal(res,'total_pagos');
            console.log({total})
            anios.forEach(anio=>{
                auxDatos = res.filter(el=>el.tipo == anio)
                console.log({auxDatos})  
                cantidad = calcularCantidad(auxDatos)
                suma_capital = calcularTotal(auxDatos,'total_capital')
                suma_deuda = calcularTotal(auxDatos,'total_deuda')
                suma_importe = calcularTotal(auxDatos,'total_importe')
                suma = calcularTotal(auxDatos,'total_pagos')    
                porcentaje = Math.round((suma/total)*100)/100;
                datos.push({
                    anio:anio,
                    monto:suma,
                    porcentaje:porcentaje,
                    cantidad:cantidad,
                    capital:suma_capital,
                    deuda:suma_deuda,
                    importe:suma_importe
                })
            })
            console.log({datos})
            res = datos;
            /*----------------fin mostrar porcentaje en grafica*/

            /*-----------------agrupar por grupo los rangos------------------------------------- */
            if(valorSelectEstructura == 'saldo_deuda' || valorSelectEstructura == 'capital' || valorSelectEstructura == 'monto_camp'){
                let rangos = [
                  {anio:'A',cantidad:0,monto:0,porcentaje:0,capital:0,deuda:0,importe:0},
                  {anio:'B',cantidad:0,monto:0,porcentaje:0,capital:0,deuda:0,importe:0},
                  {anio:'C',cantidad:0,monto:0,porcentaje:0,capital:0,deuda:0,importe:0},
                  {anio:'D',cantidad:0,monto:0,porcentaje:0,capital:0,deuda:0,importe:0}
                ]
                let i = -1;
                res.forEach(el=>{
                  if(el.anio>=0 && el.anio<500) {i = 0}
                  else if(el.anio>=500 && el.anio<1000) {i = 1}
                  else if(el.anio>=1000 && el.anio<3000) {i = 2}
                  else {i = 3}
        
                  rangos[i].cantidad += el.cantidad;
                  rangos[i].monto += el.monto;
                  rangos[i].porcentaje += el.porcentaje;
                  rangos[i].capital += el.capital;
                  rangos[i].deuda += el.deuda;
                  rangos[i].importe += el.importe;
                  
                })      
                console.log({rangos})  
                //res = rangos;
                res = rangos.filter(el=>el.porcentaje>0);
            }
            /*--------------------fin agrupar por grupos los rangos--------------------- */


            /*if(valorSelectEstructura == 'saldo_deuda' || valorSelectEstructura == 'capital' || valorSelectEstructura == 'monto_camp'){
                console.log('ingresar en deudas')
                let rangos = [
                    {tipo:'A: [0-500>',cantidad:0},
                    {tipo:'B: [500-1000>',cantidad:0},
                    {tipo:'C: [1000-3000>',cantidad:0},
                    {tipo:'D: [3000-+>',cantidad:0}
                ]
                res.forEach(el=>{
                    if(el.tipo>=0 && el.tipo<500 ){
                        rangos[0].cantidad +=parseInt(el.cantidad);
                        //if(el.cant_clientes) rangos[0].gestiones += parseInt(el.cant_gestion);
                    }else if(el.tipo>=500 && el.tipo<1000){
                        rangos[1].cantidad += parseInt(el.cantidad);
                        //if(el.cant_clientes) rangos[1].gestiones += parseInt(el.cant_gestion);
                    }else if(el.tipo>=1000 && el.tipo<3000){
                        rangos[2].cantidad += parseInt(el.cantidad);         
                        //if(el.cant_clientes) rangos[2].gestiones += parseInt(el.cant_gestion);
                    }else{
                        rangos[3].cantidad += parseInt(el.cantidad);        
                        // if(el.cant_clientes) rangos[3].gestiones += parseInt(el.cant_gestion);
                    }
                })
                console.log({rangos})
                res = rangos;
            }*/

            /*if(valorSelectEstructura == "tramo"){
                let total = 0;
                let index = -1;
                res.forEach((el,i)=>{
                    if(parseInt(el.tipo)==2016) index = i;
                    if(parseInt(el.tipo)<=2016){
                        total = total + parseInt(el.cantidad);
                    }       
                })
                if(index!=-1){
                    res[index] = {tipo:"2016",cantidad:total}
                }
                //res = res.filter(el=>parseInt(el.tipo)>=2016)
                res = res.filter(el=>{
                    if(isNaN(el.tipo)) return true;
                    return parseInt(el.tipo)>=2016
                })
            }*/

            /*------------agrupar menores de 2015 en tramo--------------------------------- */
            if(valorSelectEstructura =='tramo'){
                let index = -1;
                let acumCantidad = 0;
                let acumPorcentaje = 0;
                let acumMonto = 0;
                let acumCapital = 0;
                let acumDeuda = 0;
                let acumImporte = 0;
                res.forEach((el,i)=>{
                  if(el.anio==2016) index = i;
                  if(parseInt(el.anio)<=2016){
                    acumCantidad += el.cantidad;
                    acumMonto += el.monto;
                    acumPorcentaje += el.porcentaje;
                    acumCapital += el.capital;
                    acumDeuda += el.deuda;
                    acumImporte += el.importe;
                  } 
                })
                if(index!=-1) res[index] == {
                    anio:2016,
                    cantidad:acumCantidad,
                    porcentaje:acumPorcentaje,
                    monto:acumMonto,
                    capital:acumCapital,
                    deuda:acumDeuda,
                    importe:acumImporte
                }
                res = res.filter(el=>{
                  if(isNaN(el.anio)) return true;
                  return el.anio>=2016
                })
            }

            /*-------------------fin agrupar 2015 por tramo---------------- */


            //---------------obtener tabla info ---------------------
            let totales = {cantidad:0,capital:0,deuda:0,importe:0,monto:0}
            let html = `<table class="table table-sm table-dark">
                            <thead>
                                <tr>
                                    <th scope="col">ESRUCTURA</th>
                                    <th scope="col">CLIENTES</th>
                                    <th scope="col">CAPITAL</th>
                                    <th scope="col">DEUDA</th>
                                    <th scope="col">IMPORTE</th>
                                    <th scope="col">MONTO</th>
                                </tr>
                            </thead>
                            <tbody>`
            res.forEach(el=>{
            totales.cantidad += el.cantidad;
            totales.capital  += el.capital;
            totales.deuda    += el.deuda;
            totales.importe  += el.importe;
            totales.monto    += el.monto;
            html += 
                                `<tr>
                                    <th scope="row">${el.anio}</th>
                                    <td>${el.cantidad}</td>
                                    <td>${el.capital}</td>
                                    <td>${el.deuda}</td>
                                    <td>${el.importe}</td>
                                    <td>${el.monto}</td>
                                </tr>`
            })        
            html += 
                            `</tbody>
                            <tfooter>
                                <tr>
                                    <th scope="row">TOTAL</th>
                                    <td>${totales.cantidad}</td>
                                    <td>${totales.capital}</td>
                                    <td>${totales.deuda}</td>
                                    <td>${totales.importe}</td>
                                    <td>${totales.monto}</td>
                                </tr>
                            </tfooter>
                        </table>`                  
    
            contenedorPagos.innerHTML = html
            /*--------------------fin tabla-------------------------------------- */

            /*-----------------------obtener datos para la grafica------------------------- */
            let datos_tipo=[];
            let datos_cantidad=[];

            res.forEach(el => {
                console.log(el)
                datos_tipo.push(el.anio);
                datos_cantidad.push(el.porcentaje)
                
            });

            console.log(datos_tipo)
            console.log(datos_cantidad)

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
                            "#A93226",
                            "#16A085",
                            "#8E44AD",
                            "#F39C12",
                            "#3498DB",
                            "#F9E79F",
                            "#5D6D7E",
                            "#BB8FCE",
                            "#F9E79F",
                            "#E317C7",
                            "#175BE3",
                            "#D74594"
                        ]
                    }]
            };

            pieChart = new Chart(oilCanvas, {
            type: 'pie',
            data: oilData
            });

        })
            
    }
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
