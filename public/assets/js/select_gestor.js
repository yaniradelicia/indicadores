console.log('Ready!!')

var barChart = null;
//const carteras - gestor
const selectGestor = document.getElementById('gestor')
const selectCartera = document.getElementById('cartera')

//const para mostrar info carteras 
const contenedorCarteras = document.getElementById('carteras');
const contenedorCanvas = document.getElementById('densityChart');

//document.getElementById('spinner').hide();
//document.getElementsByClassName('spin').hide();
//document.getElementByClassName('spin').style.display = "none";


//--------------------------------- chart gestorr-------------------------------------------
selectGestor.addEventListener('change',()=>{
  console.log('--Cambio de valor en el select---')  
  const valorSelectGestor = selectGestor.options[selectGestor.selectedIndex].value;
  //document.getElementById('spinner').show();
  console.log({valorSelectGestor})
  if(valorSelectGestor!=0){
    //Realizar la peticion al servidor 
    
    
    fetch('buscarg_g/'+valorSelectGestor)
    .then(res=>res.json())
    .then(res=>{
      
      console.log(res)
      //console.log(barchart)
      

      if(barChart!=null) barChart.destroy();

      contenedorCanvas.innerHTML = '';
      

      //obtener carteras del gestor
      let carteras = [...new Set(res.map(el=>el.cartera + ' - ' + el.modalidad))];
      let html = '';
      carteras.forEach((cartera,index)=>{
          html += `<p>${index + 1}: ${cartera}</p>`
      })

      
      contenedorCarteras.innerHTML = html;

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
        yAxisID: "y-axis-meta"
      };
      
      var RecuperoData = {
        label: 'S/. Recupero',
        data: datos_recupero,
        backgroundColor: 'rgba(99, 132, 0, 0.6)',
        borderWidth: 0,
        yAxisID: "y-axis-recupero"
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
            {id: "y-axis-meta"}, 
            {id: "y-axis-recupero"}, 
            {id: "y-axis-alcance",position: 'right',}
          ]
        }
      };
      
        barChart = new Chart(densityCanvas, {
        type: 'bar',
        data: planetData,
        options: chartOptions
      });

    })
  
  }
  
})





//----------------------------chart carteraaa---------------------------------------

selectCartera.addEventListener('change',()=>{
    console.log('--Cambio de valor en el select---')  
    const valorSelectCartera = selectCartera.options[selectCartera.selectedIndex].value;
  
    console.log({valorSelectCartera})
    if(valorSelectCartera!=0){
      //Realizar la peticion al servidor 
      
      fetch('buscarg_c/'+valorSelectCartera)
      .then(res=>res.json())
      .then(res=>{
        console.log(res)

        if(barChart!=null) barChart.destroy();

        contenedorCarteras.innerHTML = '';
        contenedorCanvas.innerHTML = '';
  
        let datos_meta = [];
        let datos_alcance = [];
        let datos_recupero = [];
        let datos_mes = [];
  
        res.forEach(mes=>{
            datos_meta.push(mes.meta);
            datos_alcance.push(mes.efectividad);
            datos_recupero.push(mes.recupero);
            datos_mes.push(mes.mes);
        })
  
        var densityCanvas = document.getElementById("densityChart");
  
        Chart.defaults.global.defaultFontFamily = "Lato";
        Chart.defaults.global.defaultFontSize = 14;
        
        var MetaData = {
          label: 'S/. Meta',
          data: datos_meta,
          backgroundColor: 'rgba(0, 99, 132, 0.6)',
          borderWidth: 0,
          yAxisID: "y-axis-meta"
        };
        
        var RecuperoData = {
          label: 'S/. Recupero',
          data: datos_recupero,
          backgroundColor: 'rgba(99, 132, 0, 0.6)',
          borderWidth: 0,
          yAxisID: "y-axis-recupero"
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
            yAxes: [{
              id: "y-axis-meta"
            }, {
              id: "y-axis-recupero"
            }, {
              id: "y-axis-alcance",
              position: 'right',
            }]
          }
        };
        
           barChart = new Chart(densityCanvas, {
          type: 'bar',
          data: planetData,
          options: chartOptions
        });
  
  
  
      })
    
    }
    
  })