console.log('Ready!!')
function validaForm(){
  //campo select
  if($("#gestor").val() == ""){
      alert("Seleccionar Gestor.");
      $("#gestor").focus(); 
      return false;
  }

  return true;
}
var barChart = null;
//const carteras - gestor
const selectGestor = document.getElementById('gestor')
//const selectCartera = document.getElementById('cartera')

//const para mostrar info carteras 
const contenedorCarteras = document.getElementById('carteras');

//para mostrar tabla
const contenedorGestor = document.getElementById('tabla-gestor');

const contenedorCanvas = document.getElementById('densityChart');

const botonBuscar = document.getElementById('buscar')
botonBuscar.addEventListener('click',(event)=>{event.preventDefault();gestores()});

//--------------------------------- chart gestorr-------------------------------------------
function gestores(){
  console.log('--Cambio de valor en el select---')  
  if(barChart!=null) barChart.destroy();
  contenedorCanvas.innerHTML = '';
  contenedorGestor.innerHTML = '';
  contenedorCarteras.innerHTML = '';
  const valorSelectGestor = selectGestor.options[selectGestor.selectedIndex].value;
  //document.getElementById('spinner').show();
  console.log({valorSelectGestor})
  //if(valorSelectGestor!=0){
  if(validaForm()){
    //Realizar la peticion al servidor 
    cargando.style.display = '';
    fetch('buscarg_g/'+valorSelectGestor)
    .then(res=>res.json())
    .then(res=>{
      cargando.style.display = 'none';
      console.log(res)
      //console.log(barchart)
      //if(barChart!=null) barChart.destroy();

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
      res.forEach(el=>{
      totales.meta += parseFloat(el.meta);
      totales.recupero  += parseFloat(el.ranking);
      totales.alcance    = Math.round((totales.recupero/totales.meta)*100);
      html += 
                          `<tr>
                              <th scope="row">${el.mes}</th>
                              <td>${formatoMoneda(el.meta)}</td>
                              <td>${formatoMoneda(el.ranking)}</td>
                              <td>${el.efectividad+'%'}</td>
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

      contenedorGestor.innerHTML = html;
      /*--------------------fin tabla-------------------------------------- */
      

      //obtener carteras del gestor
      let carteras = [...new Set(res.map(el=>el.cartera + ' - ' + el.modalidad))];
      let html2 = '';
      carteras.forEach((cartera,index)=>{
          html2 += `<di class="pt-4"><p class="text-primary text-center">${index + 1}: ${cartera}</p></div>`
      })

      
      contenedorCarteras.innerHTML = html2;

      let datos_meta = [];
      let datos_alcance = [];
      let datos_recupero = [];
      let datos_mes = [];

      res.forEach(mes=>{
          datos_meta.push(mes.meta);
          datos_alcance.push(mes.efectividad);
          datos_recupero.push(mes.ranking);
          datos_mes.push(mes.mes + ' - ' + (carteras.indexOf(mes.cartera + ' - ' + mes.modalidad) + 1));
      })


      var densityCanvas = document.getElementById("densityChart");

      Chart.defaults.global.defaultFontFamily = "Lato";
      Chart.defaults.global.defaultFontSize = 14;
      
      var MetaData = {
        label: 'S/. Meta',
        data: datos_meta,
        backgroundColor: 'rgba(0, 99, 132, 0.6)',
        borderWidth: 0,
        yAxisID: "y-axis",
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
            /*scaleLabel: {
              display: true,
              labelString: ['Date','dd','gg']
            },*/
          }],
          yAxes: [
            {id: "y-axis",ticks: {max: 65000, min: 0, stepSize: 5000,
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
            ///{id: "y-axis-recupero",ticks: {beginAtZero:true}}, 
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

          ]
        },
        tooltips: {
          callbacks: {
             label: function(t) {
                //return tooltipItem.yLabel;
                if (t.datasetIndex === 0 || t.datasetIndex==1) {
                  return 'S/.' + formatoMoneda(Number(t.yLabel));
                }else{
                  return t.yLabel +'%';
                }
             },
             title: () => null,
          },
          bodyFontSize:18,
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