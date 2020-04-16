console.log('Ready!!')

var pieChart = null;

const selectCartera = document.getElementById('cartera')
const selectEstructura = document.getElementById('estructura')

const contenedorCanvas = document.getElementById('oilChart');

selectCartera.addEventListener('change',()=>{indicadoresGestion()});
selectEstructura.addEventListener('change',()=>{indicadoresGestion()});

function indicadoresGestion(){
    console.log('--Cambio de valor en el select---')  
    const valorSelectCartera = selectCartera.options[selectCartera.selectedIndex].value;
    const valorSelectEstructura = selectEstructura.options[selectEstructura.selectedIndex].value;

    //document.getElementById('spinner').show();
    console.log({valorSelectCartera})
    console.log({valorSelectEstructura})

    if(valorSelectCartera!="" && valorSelectEstructura!=""){
        fetch('buscarc_c/'+valorSelectCartera+'/'+valorSelectEstructura)
        .then(res=>res.json())
        .then(res=>{
            console.log(res)

            if(pieChart!=null) pieChart.destroy();


            //para porcentajess
            let totalCantidades = calcularTotal(res);
            res.forEach(el=>{
              let cantidad = el.cantidad;
              el.porcentaje = Math.round((cantidad/totalCantidades) * 10000)/100
              console.log(cantidad)
              console.log(el.porcentaje)
            })
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
                  rangos[index].cantidad += el.cantidad;
                  rangos[index].porcentaje += el.porcentaje;
                  rangos[index].capital += el.total_capital;
                  rangos[index].deuda   += el.total_deuda;
                  rangos[index].importe += el.total_importe;
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
                    if(parseInt(el.tipo)==2016) index = i;
                    if(parseInt(el.tipo)<=2016){
                        cantidades.cantidad   += el.cantidad;
                        cantidades.porcentaje += el.porcentaje;
                        cantidades.capital += el.total_capital;
                        cantidades.deuda += el.total_deuda;
                        cantidades.importe += el.total_importe;
                    }
                })
  
                //if(index!=-1){ res[index].cantidad = cantidades.cantidad; res[index].porcentaje = cantidades.porcentaje}
                if(index!=-1){
                    cantidades.tipo = 2016;
                    res[index] = cantidades;
                }


                res = res.filter(el=>{
                    if(isNaN(el.tipo)) return true;
                    return parseInt(el.tipo)>=2016;
                })
                  
            }
            /////////////////

            let datos_cantidad = [];
            let datos_tipo = [];


            res.forEach(el=>{
                console.log(el)
                datos_cantidad.push(el.porcentaje);
                datos_tipo.push(el.tipo);

                //datos_mes.push(mes.mes + ' - ' + (carteras.indexOf(mes.cartera + ' - ' + mes.modalidad) + 1));
            })

            console.log(datos_cantidad)
            console.log(datos_tipo)

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



function calcularTotal(arr,prop = 'cantidad'){
    let total = 0;
    arr.forEach(el=>{
        total += parseInt(el[prop])
    })
    return total;
}

  /*function calcularTotal(arr,prop){
    let total = 0;
    arr.forEach(el=>{
      total += parseFloat(el[prop])
    })
    return total;
}*/