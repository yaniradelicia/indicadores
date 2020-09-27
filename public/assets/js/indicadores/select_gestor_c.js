console.log('Ready!!')
function validaForm(){
  //campo select
  if($("#cartera").val() == ""){
      alert("Seleccionar Cartera.");
      $("#cartera").focus(); 
      return false;
  }

  if($("#comparativo").val() == ""){
      alert("Seleccionar Comparativo.");
      $("#comparativo").focus();
      return false;
  }

  return true;
}
var barChart = null;
const selectCartera = document.getElementById('cartera')
const selectComparativo = document.getElementById('comparativo')
const selectVerPagos = document.getElementById('verPagos')
const selectVerCarteras = document.getElementById('verCarteras')

const contenedorCanvas = document.getElementById('densityChart');

const contenedorCartera = document.getElementById('tabla-cartera');

//----------------------------chart carteraaa-------------------------------------------------------------

/*selectCartera.addEventListener('change',()=>{indicadoresCartera()});
selectComparativo.addEventListener('change',()=>{indicadoresCartera()});*/
const botonBuscar = document.getElementById('buscar')
botonBuscar.addEventListener('click',(event)=>{event.preventDefault();indicadoresCartera()});

function indicadoresCartera(){
    console.log('--Cambio de valor en el select---')
    if(barChart!=null) barChart.destroy();  
    contenedorCartera.innerHTML = '';
    const valorSelectCartera = selectCartera.options[selectCartera.selectedIndex].value;
    const valorSelectComparativo = selectComparativo.options[selectComparativo.selectedIndex].value;
  
    console.log({valorSelectCartera})
    console.log({valorSelectComparativo})

    //if(valorSelectCartera!=0 && valorSelectComparativo!=0){
    if(validaForm()){
      //Realizar la peticion al servidor 
      let url = '';
      if(valorSelectComparativo=='afecha'){
        if(valorSelectCartera=='34' || valorSelectCartera=='88' || valorSelectCartera=='2' || valorSelectCartera=='89' ||
            valorSelectCartera=='70' ||  valorSelectCartera=='20' || valorSelectCartera=='72' || valorSelectCartera=='5'){
            url = 'buscarg_cp/'+valorSelectCartera
        }else{
            url = 'buscarg_cco/'+valorSelectCartera
        }
      }else{
        if(valorSelectCartera=='34' || valorSelectCartera=='88' || valorSelectCartera=='2' || valorSelectCartera=='89' ||
            valorSelectCartera=='70' ||  valorSelectCartera=='20' || valorSelectCartera=='72' || valorSelectCartera=='5'){
            url = 'buscarg_cpcierre/'+valorSelectCartera
        }else{
            url = 'buscarg_ccocierre/'+valorSelectCartera
        }
      }
      cargando.style.display = '';
      
      fetch(url)
      .then(res=>res.json())
      .then(res=>{
        cargando.style.display = 'none';
        let pagos=res.pagos;
          console.log(pagos);
        let metas=res.metas;
        console.log(metas);

        //if(barChart!=null) barChart.destroy();

        metas.forEach(element=>{
          let aux = pagos.find(el=>el.m===element.mes)
          let recupero=aux? aux.recupero:0.00;
          element.recupero=recupero
        })
        console.log(metas);

        //contenedorCarteras.innerHTML = '';
        contenedorCanvas.innerHTML = '';

        //---------------obtener tabla info ---------------------
        let totales = {meta:0,recupero:0,alcance:0}
        let html = `<table class="table table-sm table-striped table-hover table-responsive-sm">
                        <thead class="cab-camp text-center">
                            <tr>
                                <th scope="col">MES</th>
                                <th scope="col">META</th>
                                <th scope="col">RECUPERO</th>
                                <th scope="col">ALCANCE</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">`
        metas.forEach(el=>{
        let alcance = Math.round(( parseFloat(el.recupero)/parseFloat(el.meta))*100);
        totales.meta += parseFloat(el.meta);
        totales.recupero  += parseFloat(el.recupero);
        totales.alcance    = Math.round((totales.recupero/totales.meta)*100);;
        html += 
                            `<tr>
                                <th scope="row">${el.mes_nombre}</th>
                                <td>${formatoMoneda(el.meta)}</td>
                                <td>${formatoMoneda(el.recupero)}</td>
                                <td>${alcance+'%'}</td>
                            </tr>`
        })        
        html += 
                        `</tbody>
                        <thead class="footer-total text-center">
                            <tr>
                                <th scope="row">TOTAL</th>
                                <th scope="row">${formatoMoneda(totales.meta)}</th>
                                <th scope="row">${formatoMoneda(totales.recupero)}</th>
                                <th scope="row">${totales.alcance+'%'}</th>
                            </tr>
                        </thead>
                    </table>`                  

        contenedorCartera.innerHTML = html
        /*--------------------fin tabla-------------------------------------- */
  
        let datos_meta = [];
        let datos_alcance = [];
        let datos_recupero = [];
        let datos_mes = [];

        //let alcance = Math.round(( parseFloat(el.recupero)/parseFloat(el.meta))*100);
  
        metas.forEach(mes=>{
            let alcance = Math.round(( parseFloat(mes.recupero)/parseFloat(mes.meta))*100);
            datos_meta.push(mes.meta);
            datos_alcance.push(alcance);
            datos_recupero.push(parseFloat(mes.recupero));
            datos_mes.push(mes.mes_nombre);
        })
  
        var densityCanvas = document.getElementById("densityChart");
  
        Chart.defaults.global.defaultFontFamily = "Lato";
        Chart.defaults.global.defaultFontSize = 14;
      
        
        var MetaData = {
          label: 'S/. Meta',
          data: datos_meta,
          backgroundColor: 'rgba(0, 99, 132, 0.6)',
          borderWidth: 0,
          yAxisID: "y-axis"
        };
        
        var RecuperoData = {
          label: 'S/. Recupero',
          data: datos_recupero,
          backgroundColor: 'rgba(99, 132, 0, 0.6)',
          borderWidth: 0,
          yAxisID: "y-axis"
        };
        
        var AlcanceData = {
            label: '% Alcance',
            data: datos_alcance,
            backgroundColor: 'rgba(132, 0, 99, 0.6)',
            borderWidth: 0,
            yAxisID: "y-axis-alcance"
        };
        
        var planetData = {
          labels:  datos_mes,
          datasets: [MetaData, RecuperoData, AlcanceData],
        };
        
        var chartOptions = {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            xAxes: [{
              barPercentage: 1,
              categoryPercentage: 0.5,
            }],

            yAxes: [
              {id: "y-axis",ticks: {beginAtZero:true,
                callback: function(value, index, values) {
                  if(parseInt(value) >= 1000){
                    return 'S/.' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                  } else {
                    return 'S/.' + value;
                  }
                }
              
              },
              scaleLabel: {
                display: true,
                fontSize:14,
                fontFamily: 'sans-serif',
                labelString: 'META / RECUPERO (S/.)'
              }
            }, 
              //{id: "y-axis-recupero",ticks: {beginAtZero:true}}, 
              {id: "y-axis-alcance",position: 'right',ticks: {max: 220, min: 0, stepSize: 20,
                  callback: function(value, index, values) {
                    return value + '%';
                  }
                },
                scaleLabel: {
                  display: true,
                  fontSize:14,
                  fontFamily: 'sans-serif',
                  labelString: 'ALCANCE (%)'
                }
              },
              
            /*{ticks: {
              beginAtZero:true
            }}*/
          ]
          },

          tooltips: {
            callbacks: {
               label: function(t,value) {
                  //return tooltipItem.yLabel;
                  if (t.datasetIndex === 0 || t.datasetIndex==1) {
                    return 'S/.' + formatoMoneda(Number(t.yLabel));
                  }else{
                    return t.yLabel +'%';
                  }
               },
               title: () => null,
            },
            bodyFontSize:16,
          }
        };
        
        barChart = new Chart(densityCanvas, {
          type: 'bar',
          data: planetData,
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
