var pieChart = null;

const selectCartera = document.getElementById('cartera')
const selectEstructura = document.getElementById('estructura')
const selectGestion = document.getElementById('gestion')
const selectInicio = document.getElementById('inicio')
const selectFin = document.getElementById('fin')

const contenedorCanvas = document.getElementById('oilChart');

selectCartera.addEventListener('change',()=>{indicadoresGestion()});
selectEstructura.addEventListener('change',()=>{indicadoresGestion()});
selectGestion.addEventListener('change',()=>{indicadoresGestion()});
selectInicio.addEventListener('change',()=>{indicadoresGestion()});
selectFin.addEventListener('change',()=>{indicadoresGestion()});

function indicadoresGestion(){
    const valorSelectCartera = selectCartera.options[selectCartera.selectedIndex].value;
    const valorSelectEstructura = selectEstructura.options[selectEstructura.selectedIndex].value;
    const valorSelectGestion = selectGestion.options[selectGestion.selectedIndex].value;
    const valorSelectInicio = selectInicio.value;
    const valorSelectFin = selectFin.value;

    if(valorSelectCartera!="" && valorSelectEstructura!=""  && valorSelectGestion!=""  && valorSelectInicio!=""  && valorSelectFin!=""){
      //Realizar la peticion al servidor
        let url = '';
        if(valorSelectGestion=='pdp'){
            url = 'buscarpdp_g/'+valorSelectCartera+'/'+valorSelectEstructura+'/'+valorSelectInicio+'/'+valorSelectFin
        }else if(valorSelectGestion=='confirmacion'){
            url = 'buscarcon_g/'+valorSelectCartera+'/'+valorSelectEstructura+'/'+valorSelectInicio+'/'+valorSelectFin
        }else{
            url = 'buscarcli_g/'+valorSelectCartera+'/'+valorSelectEstructura+'/'+valorSelectInicio+'/'+valorSelectFin
        }
      
      
        fetch(url)
        .then(res=>res.json())
        .then(res=>{

            if(pieChart!=null) pieChart.destroy();
            
            let totalCantidades = 0;          
            if(valorSelectGestion == 'gestion') totalCantidades = calcularTotal(res,'cant_gestion');
            else totalCantidades = calcularTotal(res);
            res.forEach(el=>{
                let cantidad = el.cantidad? el.cantidad:el.cant_gestion;                
                el.porcentaje = cantidad/totalCantidades;
            })

            if(valorSelectEstructura == 'saldo_deuda' || valorSelectEstructura == 'capital' || valorSelectEstructura == 'monto_camp'){
                let rangos = [
                    {tipo:'A: [0-500>',cantidad:0,clientes:0,porcentaje:0.00,capital:0,deuda:0,importe:0},
                    {tipo:'B: [500-1000>',cantidad:0,clientes:0,porcentaje:0.00,capital:0,deuda:0,importe:0},
                    {tipo:'C: [1000-3000>',cantidad:0,clientes:0,porcentaje:0.00,capital:0,deuda:0,importe:0},
                    {tipo:'D: [3000-+>',cantidad:0,clientes:0,porcentaje:0.00,capital:0,deuda:0,importe:0}
                ]
                let index = -1;
                res.forEach(el=>{
                    if(el.tipo>=0 && el.tipo<500 ) index = 0
                    else if(el.tipo>=500 && el.tipo<1000) index = 1
                    else if(el.tipo>=1000 && el.tipo<3000) index = 2
                    else index = 3
                    rangos[index].cantidad += el.cantidad? el.cantidad:el.cant_gestion;
                    rangos[index].clientes += el.cant_clientes? el.cant_clientes:0;
                    rangos[index].porcentaje += el.porcentaje;
                    rangos[index].capital += el.total_capital;
                    rangos[index].deuda   += el.total_deuda;
                    rangos[index].importe += el.total_importe;
                })
                if(valorSelectGestion!='intensidad')
                    res = rangos.filter(el=>el.porcentaje>0);
                else{
                    res = rangos;
                }
            }
            
            if(valorSelectEstructura == "tramo"){
              let cantidades = {cantidad:0,clientes:0,porcentaje:0,capital:0,deuda:0,importe:0};
              let index = -1;
              res.forEach((el,i)=>{
                if(parseInt(el.tipo)==2016) index = i;
                if(parseInt(el.tipo)<=2016){
                  cantidades.cantidad   += el.cantidad? el.cantidad:el.cant_gestion;
                  cantidades.porcentaje += el.porcentaje;
                  cantidades.clientes += el.cant_clientes? el.cant_clientes:0;
                  cantidades.capital += el.total_capital;
                  cantidades.deuda += el.total_deuda;
                  cantidades.importe += el.total_importe;
                }
              })

              if(index!=-1){
                cantidades.tipo = 2016;
                res[index] = cantidades;
              }

              res = res.filter(el=>{
                if(isNaN(el.tipo)) return true;
                return parseInt(el.tipo)>=2016;
              })
                
            }

            ///////////////////grafica

            if(valorSelectGestion=='intensidad'){
                res = res.map(el=>{
                  let cantidad = el.cantidad? parseInt(el.cantidad):parseInt(el.cant_gestion);
                  let clientes = el.clientes? parseInt(el.clientes):parseInt(el.cant_clientes);
                  return {
                    tipo:el.tipo,
                    cantidad:Math.round(parseInt(cantidad)/parseInt(clientes))
                  }
                })
            }

            if(valorSelectGestion=='intensidad'){
                let datos_tipo=[];
                let datos_cantidad=[];

                res.forEach(el => {
                    datos_tipo.push(el.tipo);
                    datos_cantidad.push(el.cantidad)
                });

                var oilCanvas = document.getElementById("oilChart");
                Chart.defaults.global.defaultFontFamily = "Lato";
                Chart.defaults.global.defaultFontSize = 18;
                var densityData = {
                    label: 'Intensidad',
                    data: datos_cantidad,
                };
                pieChart = new Chart(oilCanvas, {
                    type: 'bar',
                    data: {
                        labels: datos_tipo,
                        datasets: [densityData]
                    }
                });
            }else{

                let datos_tipo=[];
                let datos_cantidad=[];

                res.forEach(el => {
                    datos_tipo.push(el.tipo);
                    datos_cantidad.push(el.porcentaje)
                });

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

            }
            
        })
    
    }
}

function calcularTotal(arr,prop = 'cantidad'){
  let total = 0;
  arr.forEach(el=>{total += el[prop]})
  return total;
}
