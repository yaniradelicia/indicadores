console.log('Ready!!')
function validaForm(){
    //campo select
    if($("#cartera").val() == ""){
        alert("Seleccionar Cartera.");
        $("#cartera").focus(); 
        return false;
    }
    return true;
}
var lineChart = null;
const selectCartera = document.getElementById('cartera')
//const para mostrar info pagos
const contenedorTabla = document.getElementById('tabla');
const cargando= document.getElementById('cargando');

const botonBuscar = document.getElementById('buscar')
botonBuscar.addEventListener('click',(event)=>{event.preventDefault();indicadoresGestion()});
//selectCartera.addEventListener('change',()=>{indicadoresGestion()});

function indicadoresGestion(){
    console.log('--Cambio de valor en el select---') 
    if(lineChart!=null) lineChart.destroy(); 
    contenedorTabla.innerHTML = '';
    const valorSelectCartera = selectCartera.options[selectCartera.selectedIndex].value;
    console.log({valorSelectCartera});
    //if(valorSelectCartera!=""){
    if(validaForm()){
        let url = '';
        if(valorSelectCartera=='34' || valorSelectCartera=='88' || valorSelectCartera=='2' || valorSelectCartera=='89' ||
            valorSelectCartera=='70' ||  valorSelectCartera=='20' || valorSelectCartera=='72' || valorSelectCartera=='5'){
            url = 'buscarc_tp/'+valorSelectCartera
        }else{
            url = 'buscarc_tc/'+valorSelectCartera
        }
        cargando.style.display = '';

        fetch(url)
        .then(res=>res.json())
        .then(res=>{
            cargando.style.display = 'none';
            if(res.datos.length>0){
            let datos=res.datos;
            console.log(datos);
            let totales=res.totales;
            console.log(totales);
            let porcentajes=res.porcentajes;
            console.log(porcentajes);
            //if(lineChart!=null) lineChart.destroy();
            //obtener los meses
            let meses1 = [...new Set(datos.map(dato=>parseInt(dato.fec.substr(5,7))))]
            console.log(meses1);

            //agrupar por meses
            let datosPorMes = []
            meses1.forEach(mes=>{
                let arrDatos = datos.filter(dato=>mes == parseInt(dato.fec.substr(5,7)))
                datosPorMes.push({mes,datos:arrDatos})
            })
            console.log({datosPorMes});
            datosPorMes.forEach((datoPorMes,index)=>{
                let acumulador = 0;
                datoPorMes.datos.forEach(dato=>{
                    acumulador += parseFloat(dato.pagos)
                    dato.pagos_acumulada = acumulador;
                    dato.porcentaje_real = Math.round((dato.pagos_acumulada/totales[index].meta)*100);  
                })
            })
            console.log({datosPorMes});

            let meses2 = [...new Set(porcentajes.map(porcentaje=>parseInt(porcentaje.fec.substr(5,7))))]
            console.log(meses2);

            //agrupar por meses
            let datosPorcentajes = []
            meses2.forEach(mes2=>{
                let arrDatos = porcentajes.filter(porcentaje=>mes2 == parseInt(porcentaje.fec.substr(5,7)))
                datosPorcentajes.push({mes2,porcentajes:arrDatos})
            })
            console.log({datosPorcentajes});
            datosPorcentajes.forEach((datoPorcentaje,index)=>{
                datoPorcentaje.porcentajes.forEach(porcentaje=>{
                    porcentaje.pago_ideal = totales[index].meta*parseFloat(porcentaje.porce);
                    porcentaje.nuevo_porce=Math.round((porcentaje.porce)*100);  
                })
            })
            console.log({datosPorcentajes});
            /*porcentajes.forEach((elemento,index)=>{
                const valor=totales.find(el=>el.meta);
                if(valor){
                    elemento.porcentaje_ideal=valor.meta*elemento.porce;
                }
            })
            console.log({porcentajes});*/
            const datas = datosPorcentajes.concat(datosPorMes);
            console.log(datas);
            
            let arrDiasMes = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30];

            let meses_chart = [];
            /*arrDiasMes.forEach((_,index)=>{
                meses2.forEach((_,index_mes)=>{
                    if(index===0) meses_chart[index_mes] = []
                    let diaMes = datosPorcentajes[index_mes].porcentajes.find(porcentaje=>parseInt(porcentaje.fec.substr(8,10)) === index + 1)
                    meses_chart[index_mes][index] = diaMes? diaMes.nuevo_porce:null;
                })
            })*/
            arrDiasMes.forEach((_,index)=>{
                meses_chart =  datosPorcentajes[0].porcentajes.map(dato=>dato.nuevo_porce)
            })
            console.log({meses_chart})
            
            let date = new Date()

            let day = date.getDate()
            let month = date.getMonth()+1
            let year = date.getFullYear()
            if(month < 10){
                f=`"${year}-0${month}-${day}"`;
            }else{
                f=`"${year}-0${month}-${day}"`;
            }
            console.log(f);
            console.log(date.getDate()-10);
            /*let fecha = datosPorMes[0].datos.pop(dato=>parseInt(dato.fec));
            console.log(fecha);*/

            let meses_chart2 = [];
            arrDiasMes.forEach((_,index)=>{
                meses1.forEach((_,index_mes)=>{
                    if(index===0) meses_chart2[index_mes] = []
                    let diaMes = datosPorMes[index_mes].datos.find(dato=>parseInt(dato.fec.substr(8,10)) === index + 1)
                    if(index+1<date.getDate()){
                    meses_chart2[index_mes][index] = diaMes? diaMes.porcentaje_real:meses_chart2[index_mes][index-1];
                    }
                })
            })
            /*arrDiasMes.forEach((_,index)=>{
                meses_chart2 = datosPorMes[0].datos.map(dato=>dato.porcentaje_real)
            })*/
            console.log({meses_chart2})

            let meses_chart_completo = [];
            meses_chart_completo= meses_chart.concat(meses_chart2);
            console.log({meses_chart_completo})

            let tabla_pago_ideal = [];
            let tabla_pago_acum = [];
            tabla_pago_ideal=datosPorcentajes[0].porcentajes.map(dato=>dato.pago_ideal);
            //tabla_pago_acum=datosPorMes[0].datos.map(dato=>dato.pagos_acumulada);
            arrDiasMes.forEach((_,index)=>{
                meses1.forEach((_,index_mes)=>{
                    if(index===0) tabla_pago_acum[index_mes] = []
                    let diaMes = datosPorMes[index_mes].datos.find(dato=>parseInt(dato.fec.substr(8,10)) === index + 1)
                    if(index+1<date.getDate()){
                        tabla_pago_acum[index_mes][index] = diaMes? diaMes.pagos_acumulada:tabla_pago_acum[index_mes][index-1];
                    }
                })
            })

            console.log({tabla_pago_ideal});
            console.log({tabla_pago_acum});

            let tabla_pago_acum2=[];
            arrDiasMes.forEach((el,index)=>{
                if(index+1<date.getDate()){
                tabla_pago_acum2[index]=tabla_pago_acum[0][index]? tabla_pago_acum[0][index]:0
                }
            })
            console.log({tabla_pago_acum2});

            let meses_chart3=[];
            arrDiasMes.forEach((el,index)=>{
                if(index+1<date.getDate()){
                    meses_chart3[index]=meses_chart2[0][index]? meses_chart2[0][index]:0
                }
            })
            console.log({meses_chart3});

            contenedorTabla.innerHTML = '';
            let htmlTable = `<table class="table table-sm table-striped table-hover table-responsive-sm">
                                <thead class="cab-camp text-center">
                                <tr>
                                    <th scope="col">Día</th>
                                    <th scope="col">Avance Ideal (S/.)</th>
                                    <th scope="col">Avance Real (S/.)</th>
                                </tr>
                                </thead>
                                <tbody class="text-center">`

                                tabla_pago_ideal.forEach((el,index)=>{
                                htmlTable += `<tr>
                                    <th scope="row">${index+1}</th>
                                    <td>S/.${formatoMoneda(el)}</td>
                                    <td>${tabla_pago_acum2[index]>=0? "S/."+formatoMoneda(tabla_pago_acum2[index]):"-"}</td>
                                </tr>`
                                //console.log(tabla_pago_acum2[index]);
                                })

                                htmlTable += '</tbody></table>`';

            contenedorTabla.innerHTML = htmlTable

            var speedCanvas = document.getElementById("speedChart");

                Chart.defaults.global.defaultFontFamily = "Lato";
                Chart.defaults.global.defaultFontSize = 14;

                var dataFirst = {
                    label: "% Ideal Acumulado",
                    data: meses_chart,
                    lineTension: 0,
                    fill: false,
                    borderColor: 'blue'
                  };
                
                var dataSecond = {
                    label: "% Real Acumulado",
                    data: meses_chart3,
                    lineTension: 0,
                    fill: false,
                    borderColor: 'green'
                  };
                var speedData = {
                    labels: arrDiasMes,
                    datasets: [dataFirst, dataSecond]
                };

                var chartOptions = {
                    scales: {
                        yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function(value, index, values) {
                                return value + '%';
                            },
                            fontSize:20
                        }
                        }]
                    },
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                        boxWidth: 70,
                        fontColor: 'black'
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false ,

                    tooltips: {
                        callbacks: {
                            title: function (tooltipItem, data) { 
                                return "Día " + data.labels[tooltipItem[0].index]; 
                            },
                        label: function(t,value) {
                            return t.yLabel +'%';
                        },
                        //title: () => null,
                        },
                        bodyFontSize:20,
                    }
                };

                lineChart = new Chart(speedCanvas, {
                type: 'line',
                data: speedData,
                options: chartOptions
                });
            }else{
                if(lineChart!=null) lineChart.destroy();
                contenedorTabla.innerHTML = '';
                console.log('no hay datos');
            }

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