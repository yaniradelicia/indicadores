console.log('Ready!!')
function validaForm(){
    //campo select
    if($("#cartera").val() == ""){
        alert("Seleccionar Cartera.");
        $("#cartera").focus(); 
        return false;
    }

    if($("#ubic").val() == ""){
        alert("Seleccionar Ubicabilidad.");
        $("#ubic").focus();
        return false;
    }

    if($("#estructura").val() == ""){
        alert("Seleccionar Estructura.");
        $("#estructura").focus();
        return false;
    }

    if($("#mes").val() == ""){
        alert("Seleccionar Mes.");
        $("#mes").focus();
        return false;
    }
    return true;
}
var pieChart = null;

const selectCartera = document.getElementById('cartera')
const selectUbic = document.getElementById('ubic')
const selectEstructura = document.getElementById('estructura')
const selectMes = document.getElementById('mes')

//const para mostrar info pagos
const contenedorCartera = document.getElementById('tabla-cartera');

const contenedorCanvas = document.getElementById('oilChart');

/*selectCartera.addEventListener('change',()=>{indicadoresGestion()});
selectUbic.addEventListener('change',()=>{indicadoresGestion()});
selectEstructura.addEventListener('change',()=>{indicadoresGestion()});
selectMes.addEventListener('change',()=>{indicadoresGestion()});*/
const botonBuscar = document.getElementById('buscar')
botonBuscar.addEventListener('click',(event)=>{event.preventDefault();indicadoresGestion()});


function indicadoresGestion(){
    console.log('--Cambio de valor en el select---')  
    if(pieChart!=null) pieChart.destroy();
    contenedorCartera.innerHTML = '';
    const valorSelectCartera = selectCartera.options[selectCartera.selectedIndex].value;
    const valorSelectUbic = selectUbic.options[selectUbic.selectedIndex].value;
    const valorSelectEstructura = selectEstructura.options[selectEstructura.selectedIndex].value;
    const valorSelectMes = selectMes.value;

    //document.getElementById('spinner').show();
    console.log({valorSelectCartera})
    console.log({valorSelectUbic})
    console.log({valorSelectEstructura})
    console.log({valorSelectMes})

    //if(valorSelectCartera!="" && valorSelectUbic!="" && valorSelectEstructura!="" && valorSelectMes!=""){
    if(validaForm()){
        let url = '';
        if(valorSelectUbic=='todos'){
            url = 'buscarc_c/'+valorSelectCartera+'/'+valorSelectEstructura+'/'+valorSelectMes
        }else if(valorSelectUbic=='contacto' || valorSelectUbic=='nocontacto' || valorSelectUbic=='inubicable' || valorSelectUbic=='cfrn' || valorSelectUbic=='nodisponible'){
            url = 'buscarc_ubcon/'+valorSelectCartera+'/'+ valorSelectUbic +'/'+valorSelectEstructura+'/'+valorSelectMes
        }else{
            url = 'buscarc_ubsing/'+valorSelectCartera+'/'+valorSelectEstructura+'/'+valorSelectMes
        }
        cargando.style.display = '';
        fetch(url)
        .then(res=>res.json())
        .then(res=>{
            cargando.style.display = 'none';
            console.log(res)

            //if(pieChart!=null) pieChart.destroy();


            //para porcentajess
            let totalCantidades = calcularTotal(res);
            res.forEach(el=>{
              let cantidad = el.cantidad;
              el.porcentaje = (cantidad/totalCantidades) * 100
              /*console.log(cantidad)
              console.log(el.porcentaje)*/
            })
            console.log(res)
            console.log(totalCantidades)
            //////////////////////

            ///para rangosss
            if(valorSelectEstructura == 'saldo_deuda' || valorSelectEstructura == 'capital' || valorSelectEstructura == 'monto_camp'){
                let rangos = [
                  {tipo:'A: [0-500>',cantidad:0,porcentaje:0,capital:0,deuda:0,importe:0},
                  {tipo:'B: [500-1000>',cantidad:0,porcentaje:0,capital:0,deuda:0,importe:0},
                  {tipo:'C: [1000-3000>',cantidad:0,porcentaje:0,capital:0,deuda:0,importe:0},
                  {tipo:'D: [3000-+>',cantidad:0,porcentaje:0,capital:0,deuda:0,importe:0}
                ]
                let index = -1;
                res.forEach(el=>{
                  if(el.tipo>=0 && el.tipo<500 ) index = 0
                  else if(el.tipo>=500 && el.tipo<1000) index = 1
                  else if(el.tipo>=1000 && el.tipo<3000) index = 2
                  else index = 3
                  rangos[index].cantidad += parseInt(el.cantidad);
                  rangos[index].porcentaje += el.porcentaje;
                  rangos[index].capital += parseFloat(el.total_capital);
                  rangos[index].deuda   += parseFloat(el.total_deuda);
                  rangos[index].importe += parseFloat(el.total_importe);
                })
                //res = rangos;
                res = rangos.filter(el=>el.porcentaje>0);
            }
            ////////////////////////

            ///para tramos
            if(valorSelectEstructura == "tramo"){
                let cantidades = {cantidad:0,porcentaje:0,capital:0,deuda:0,importe:0};
                let index = -1;
                res.forEach((el,i)=>{
                    if(isNaN(el.tipo)==false){
                        if(parseInt(el.tipo)==2016) index = i;
                        if(parseInt(el.tipo)<=2016){
                            cantidades.cantidad   += parseInt(el.cantidad);
                            cantidades.porcentaje += el.porcentaje;
                            cantidades.capital += parseFloat(el.total_capital);
                            cantidades.deuda += parseFloat(el.total_deuda);
                            cantidades.importe += parseFloat(el.total_importe);
                        }
                    }
                })
                //if(index!=-1){ res[index].cantidad = cantidades.cantidad; res[index].porcentaje = cantidades.porcentaje}
                if(index!=-1){
                    cantidades.tipo = 2016;
                    res[index] = cantidades;
                }else{
                    cantidades.tipo = 2016;
                    res.unshift(cantidades);
                }

                res = res.filter(el=>{
                    if(isNaN(el.tipo)) return true;
                    //return parseInt(el.tipo)>=2016;
                    return parseInt(el.tipo)>=2016 && el.cantidad>0;
                })
                console.log(res)
                  
            }
            //////////////////////////------------fin tramo-------------------------------

            console.log("Antes de ordenar: ",res);
            res.sort((a, b) => b.cantidad - a.cantidad);
            console.log("Despues de ordenar: ",res);



            //---------------obtener tabla info ---------------------
            let totales = {clientes:0,capital:0,deuda:0,importe:0}
            let html = `<table class="table table-sm table-striped table-hover table-responsive-sm">
                            <thead class="cab-camp text-center">
                                <tr>
                                    <th scope="col">ESTRUCTURA</th>
                                    <th scope="col">CLIENTES</th>
                                    <th scope="col">CAPITAL</th>
                                    <th scope="col">DEUDA</th>
                                    <th scope="col">IC</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">`
            res.forEach(el=>{
            totales.clientes += parseInt(el.cantidad);
            totales.capital  += el.capital? parseFloat(el.capital):parseFloat(el.total_capital);
            totales.deuda    += el.deuda? parseFloat(el.deuda):parseFloat(el.total_deuda);
            totales.importe  += el.importe>=0? parseFloat(el.importe):parseFloat(el.total_importe);
            html += 
                                `<tr>
                                    <th scope="row">${el.tipo}</th>
                                    <td>${el.cantidad}</td>
                                    <td>${formatoMoneda(el.capital? el.capital:el.total_capital )}</td>
                                    <td>${formatoMoneda(el.deuda? el.deuda:el.total_deuda)}</td>
                                    <td>${formatoMoneda(el.importe>=0? el.importe:el.total_importe)}</td>
                                </tr>`
            })        
            html += 
                            `</tbody>
                            <thead class="footer-total text-center">
                                <tr>
                                    <th scope="row">TOTAL</th>
                                    <th scope="row">${totales.clientes}</th>
                                    <th scope="row">${formatoMoneda(totales.capital)}</th>
                                    <th scope="row">${formatoMoneda(totales.deuda)}</th>
                                    <th scope="row">${formatoMoneda(totales.importe)}</th>
                                </tr>
                            </thead>
                        </table>`                  
    
            contenedorCartera.innerHTML = html
            /*--------------------fin tabla-------------------------------------- */



            let datos_cantidad = [];
            let datos_tipo = [];
            let datos_suma_otros=[];

            /*----ordenar por porcentajes*/
            console.log("Antes de ordenar: ",res);
            res.sort((a, b) => b.porcentaje - a.porcentaje);
            console.log("Despues de ordenar: ",res);


            res.forEach(el=>{
                //console.log(el)
                datos_cantidad.push(parseFloat(el.porcentaje));
                datos_tipo.push(el.tipo + ' - '+parseInt(el.cantidad));
                //datos_mes.push(mes.mes + ' - ' + (carteras.indexOf(mes.cartera + ' - ' + mes.modalidad) + 1));
                datos_suma_otros.push(parseInt(el.cantidad));
            })

            console.log(datos_cantidad);
            console.log(datos_tipo);
            console.log(datos_suma_otros);

            /*--------------ordenar los 7 primeros------------------*/
            console.log(datos_tipo.length);
            if(datos_tipo.length>7){
                const otros = datos_suma_otros.slice(7);
                sumaotros=0;
                otros.forEach (function(numero){
                    sumaotros += parseInt(numero);
                });
                console.log(sumaotros);
                //extraer 7 primeros labels y datos
                const labels1 = datos_tipo.slice(0, 7);
                console.log("7 labels: ",labels1);
                const labels2='OTROS - '+sumaotros;
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
                    //display: false
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
                            if(dataset.data[idx] >= 1){//para que muestre en la torta los mayores a 1%
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



function calcularTotal(arr,prop = 'cantidad'){
    let total = 0;
    arr.forEach(el=>{
        total += parseInt(el[prop])
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

  /*function calcularTotal(arr,prop){
    let total = 0;
    arr.forEach(el=>{
      total += parseFloat(el[prop])
    })
    return total;
}*/