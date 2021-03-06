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

    if($("#gestion").val() == ""){
        alert("Seleccionar una tipo de Gestión.");
        $("#gestion").focus();
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
const selectGestion = document.getElementById('gestion')
const selectInicio = document.getElementById('inicio')
const selectFin = document.getElementById('fin')
const cargando= document.getElementById('cargando')

//const para mostrar info pagos
const contenedorGestion = document.getElementById('tabla-gestion');

const contenedorCanvas = document.getElementById('oilChart');

const botonBuscar = document.getElementById('buscar')
botonBuscar.addEventListener('click',(event)=>{event.preventDefault();indicadoresGestion()});

/*selectCartera.addEventListener('change',()=>{indicadoresGestion()});
selectEstructura.addEventListener('change',()=>{indicadoresGestion()});
selectGestion.addEventListener('change',()=>{indicadoresGestion()});
selectInicio.addEventListener('change',()=>{indicadoresGestion()});
selectFin.addEventListener('change',()=>{indicadoresGestion()});*/

function indicadoresGestion(){
    if(pieChart!=null) pieChart.destroy();
    contenedorGestion.innerHTML = '';
    const valorSelectCartera = selectCartera.options[selectCartera.selectedIndex].value;
    const valorSelectEstructura = selectEstructura.options[selectEstructura.selectedIndex].value;
    const valorSelectGestion = selectGestion.options[selectGestion.selectedIndex].value;
    const valorSelectInicio = selectInicio.value;
    const valorSelectFin = selectFin.value;

    console.log({valorSelectCartera})
    console.log({valorSelectEstructura})
    console.log({valorSelectGestion})
    console.log({valorSelectInicio})
    console.log({valorSelectFin})

    //if(valorSelectCartera!="" && valorSelectEstructura!=""  && valorSelectGestion!=""  && valorSelectInicio!=""  && valorSelectFin!=""){
    if(validaForm()){
        if(valorSelectGestion!='pagos'){
        //Realizar la peticion al servidor
            if(valorSelectEstructura!="ubic"){
                let url = '';
                if(valorSelectGestion=='pdp'){
                    url = 'buscarpdp_g/'+valorSelectCartera+'/'+valorSelectEstructura+'/'+valorSelectInicio+'/'+valorSelectFin
                }else if(valorSelectGestion=='confirmacion'){
                    url = 'buscarcon_g/'+valorSelectCartera+'/'+valorSelectEstructura+'/'+valorSelectInicio+'/'+valorSelectFin
                }else{
                    url = 'buscarcli_g/'+valorSelectCartera+'/'+valorSelectEstructura+'/'+valorSelectInicio+'/'+valorSelectFin
                }
                cargando.style.display = '';
            
                fetch(url)
                .then(res=>res.json())
                .then(res=>{
                    cargando.style.display = 'none';

                    //if(pieChart!=null) pieChart.destroy();
                    
                    let totalCantidades = 0;          
                    if(valorSelectGestion == 'gestion') totalCantidades = calcularTotal(res,'cant_gestion');
                    else totalCantidades = calcularTotal(res);
                    res.forEach(el=>{
                        let cantidad = el.cantidad? el.cantidad:el.cant_gestion;                
                        el.porcentaje = (cantidad/totalCantidades)*100;
                    })
                    console.log(res)

                    if(valorSelectEstructura == 'saldo_deuda' || valorSelectEstructura == 'capital' || valorSelectEstructura == 'monto_camp'){
                        let rangos = [
                            {tipo:'A: [0-500>',cantidad:0,clientes:0,porcentaje:0.00,capital:0,deuda:0,importe:0,monto:0},
                            {tipo:'B: [500-1000>',cantidad:0,clientes:0,porcentaje:0.00,capital:0,deuda:0,importe:0,monto:0},
                            {tipo:'C: [1000-3000>',cantidad:0,clientes:0,porcentaje:0.00,capital:0,deuda:0,importe:0,monto:0},
                            {tipo:'D: [3000-+>',cantidad:0,clientes:0,porcentaje:0.00,capital:0,deuda:0,importe:0,monto:0}
                        ]
                        let index = -1;
                        res.forEach(el=>{
                            if(el.tipo>=0 && el.tipo<500 ) index = 0
                            else if(el.tipo>=500 && el.tipo<1000) index = 1
                            else if(el.tipo>=1000 && el.tipo<3000) index = 2
                            else index = 3
                            rangos[index].cantidad += el.cantidad? parseInt(el.cantidad):parseInt(el.cant_gestion);
                            rangos[index].clientes += el.cant_clientes? parseInt(el.cant_clientes):0;
                            rangos[index].porcentaje += parseFloat(el.porcentaje);
                            rangos[index].capital += parseFloat(el.total_capital);
                            rangos[index].deuda   += parseFloat(el.total_deuda);
                            rangos[index].importe += parseFloat(el.total_importe);
                            rangos[index].monto += parseFloat(el.monto);
                        })
                        //console.log(res)
                        if(valorSelectGestion!='intensidad')
                            res = rangos.filter(el=>el.porcentaje>0);
                        else{
                            res = rangos;
                        }
                        console.log(res)
                    }
                    

                    //------------------------tramooo-------------------------------
                    if(valorSelectEstructura == "tramo"){
                        let cantidades = {cantidad:0,clientes:0,porcentaje:0,capital:0,deuda:0,importe:0,monto:0};
                        let index = -1;
                        res.forEach((el,i)=>{
                            if(isNaN(el.tipo)==false){
                                if(parseInt(el.tipo)==2016) index = i;
                                if(parseInt(el.tipo)<=2016){
                                cantidades.cantidad   += el.cantidad? parseInt(el.cantidad):parseInt(el.cant_gestion);
                                cantidades.porcentaje += parseFloat(el.porcentaje);
                                cantidades.clientes += el.cant_clientes? parseInt(el.cant_clientes):0;
                                cantidades.capital += parseFloat(el.total_capital);
                                cantidades.deuda += parseFloat(el.total_deuda);
                                cantidades.importe += parseFloat(el.total_importe);
                                cantidades.monto += parseFloat(el.monto);
                                }
                            }
                        })

                        if(index!=-1){
                            cantidades.tipo = 2016;
                            res[index] = cantidades;
                        }else{
                            cantidades.tipo = 2016;
                            res.unshift(cantidades);
                        }

                        res = res.filter(el=>{
                            if(isNaN(el.tipo)) return true;
                            
                            return (parseInt(el.tipo)>=2016
                            && ((el.cantidad && el.cantidad>0) || (el.cant_gestion && el.cant_gestion>0)));
                            
                        })
                        console.log(res)
                    }

                    console.log("Antes de ordenar: ",res);
                    res.sort((a, b) => (b.clientes? b.clientes:b.cant_clientes) - (a.clientes? a.clientes:a.cant_clientes));
                    console.log("Despues de ordenar: ",res);


                    /////////------------fin tramo-------------------------------
                    //---------------obtener tabla info ---------------------
                    let totales = {clientes:0,capital:0,deuda:0,importe:0,cantidad:0,monto:0}
                    let html=``;
                    if(valorSelectGestion=='pdp'){
                        html = `<table class="table table-sm table-striped table-hover table-responsive-md">
                                        <thead class="cab-camp text-center">
                                            <tr>
                                                <th scope="col">ESTRUCTURA</th>
                                                <th scope="col">CLIENTES</th>
                                                <th scope="col">CAPITAL</th>
                                                <th scope="col">DEUDA</th>
                                                <th scope="col">IC</th>
                                                <th scope="col">PDP</th>
                                                <th scope="col">MONTO PDP</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">`
                    }else if(valorSelectGestion=='confirmacion'){
                        html = `<table class="table table-sm table-striped table-hover table-responsive-md">
                                        <thead class="cab-camp text-center">
                                            <tr>
                                                <th scope="col">ESTRUCTURA</th>
                                                <th scope="col">CLIENTES</th>
                                                <th scope="col">CAPITAL</th>
                                                <th scope="col">DEUDA</th>
                                                <th scope="col">IC</th>
                                                <th scope="col">CONF.</th>
                                                <th scope="col">MONTO CONF.</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">`
                    }else{
                        html = `<table class="table table-sm table-striped table-hover table-responsive-md">
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
                    }
                    res.forEach(el=>{
                        totales.clientes += el.clientes? parseInt(el.clientes):parseInt(el.cant_clientes);
                        totales.capital  += el.capital? parseFloat(el.capital):parseFloat(el.total_capital);
                        totales.deuda    += el.deuda? parseFloat(el.deuda):parseFloat(el.total_deuda);
                        totales.importe  += el.importe>=0? parseFloat(el.importe):parseFloat(el.total_importe);
                        totales.cantidad += parseInt(el.cantidad);
                        totales.monto  += parseFloat(el.monto);

                        if(valorSelectGestion=='pdp' || valorSelectGestion=='confirmacion'){
                            html +=
                                        `<tr>
                                            <th scope="row">${el.tipo}</th>
                                            <td>${el.clientes? el.clientes:el.cant_clientes}</td>
                                            <td>${formatoMoneda(el.capital? el.capital:el.total_capital) }</td>
                                            <td>${formatoMoneda(el.deuda? el.deuda:el.total_deuda)}</td>
                                            <td>${formatoMoneda(el.importe>=0? el.importe:el.total_importe)}</td>
                                            <td>${el.cantidad}</td>
                                            <td>${formatoMoneda(el.monto)}</td>
                                        </tr>`
                        }else{
                            html += 
                                        `<tr>
                                            <th scope="row">${el.tipo}</th>
                                            <td>${el.clientes? el.clientes:el.cant_clientes}</td>
                                            <td>${formatoMoneda(el.capital? el.capital:el.total_capital) }</td>
                                            <td>${formatoMoneda(el.deuda? el.deuda:el.total_deuda)}</td>
                                            <td>${formatoMoneda(el.importe>=0? el.importe:el.total_importe)}</td>
                                        </tr>`
                        }
                    }) 
                    
                    if(valorSelectGestion=='pdp' || valorSelectGestion=='confirmacion'){
                        html += 
                                    `</tbody>
                                    <thead class="footer-total text-center">
                                        <tr>
                                            <th scope="row">TOTAL</th>
                                            <th scope="row">${totales.clientes}</th>
                                            <th scope="row">${formatoMoneda(totales.capital)}</th>
                                            <th scope="row">${formatoMoneda(totales.deuda)}</th>
                                            <th scope="row">${formatoMoneda(totales.importe)}</th>
                                            <th scope="row">${totales.cantidad}</th>
                                            <th scope="row">${formatoMoneda(totales.monto)}</th>
                                        </tr>
                                    </thead>
                                </table>`   
                    }else{
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
                    }               
            
                    contenedorGestion.innerHTML = html
                    /*--------------------fin tabla-------------------------------------- */

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
                            backgroundColor: 'rgba(132, 0, 99, 0.6)',
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
                                {ticks: {max: 10, min: 0, stepSize: 2},
                                scaleLabel: {
                                    display: true,
                                    fontSize:16,
                                    fontFamily: 'sans-serif',
                                    labelString: 'INTENSIDAD'
                                }
                                }, 
                            ]
                            },
                            tooltips: {
                                callbacks: {
                                label: function(tooltipItem) {
                                    return tooltipItem.yLabel;
                                },
                                title: () => null,
                                },
                                bodyFontSize:20,
                            }
                        };

                        pieChart = new Chart(oilCanvas, {
                            type: 'bar',
                            data: {
                                labels: datos_tipo,
                                datasets: [densityData]
                            },
                            options: chartOptions
                        });
                    }else{

                        let datos_tipo=[];
                        let datos_cantidad=[];
                        let datos_suma_otros=[];
                        /*----ordenar por porcentajes*/
                        console.log("Antes de ordenar: ",res);
                        //res.sort((a, b) => b.porcentaje - a.porcentaje);
                        res.sort((a, b) => (b.clientes? b.clientes:b.cant_clientes) - (a.clientes? a.clientes:a.cant_clientes));
                        console.log("Despues de ordenar: ",res);

                        res.forEach(el => {
                            datos_tipo.push(el.tipo+' - '+ (el.clientes? parseInt(el.clientes):parseInt(el.cant_clientes)));
                            datos_cantidad.push(parseFloat(el.porcentaje))
                            datos_suma_otros.push(el.clientes? parseInt(el.clientes):parseInt(el.cant_clientes));
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
                                suma_datos2 += parseFloat(numero);
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
                                    ],
                                    //borderWidth: 1
                                }]
                        };

                        var chartOptions = {
                            legend: {
                                position: 'top',
                                labels: {
                                    fontSize: 16
                                }
                            },
                            responsive: true, 
                            maintainAspectRatio: false,
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
                                        //if(dataset.data[idx] >= 1){//para que muestre en la torta los mayores a 1%
                                            var langle = labelxy[idx],
                                                dx = centerx + lradius * Math.cos(langle),
                                                dy = centery + lradius * Math.sin(langle),
                                                //val = (dataset.data[idx] / total)*100;
                                                val = Math.round(dataset.data[idx] / total * 100);
                                                //console.log(val);
                                            ctx.fillText(val + '%', dx, dy);
                                        //}
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

                    }
                    
                })
            }
            else if(valorSelectEstructura=="ubic"){
                let url = '';
                if(valorSelectGestion=='pdp'){
                    url = 'buscarc_ubic_pdp/'+valorSelectCartera+'/'+valorSelectInicio+'/'+valorSelectFin
                }else if(valorSelectGestion=='confirmacion'){
                    url = 'buscarc_ubic_con/'+valorSelectCartera+'/'+valorSelectInicio+'/'+valorSelectFin
                }else{
                    url = 'buscarc_ubic/'+valorSelectCartera+'/'+valorSelectInicio+'/'+valorSelectFin
                }
                cargando.style.display = '';

                fetch(url)
                .then(res=>res.json())
                .then(res=>{
                    cargando.style.display = 'none';
                    if(pieChart!=null) pieChart.destroy();
                    let totalCantidades = 0;          
                    if(valorSelectGestion == 'gestion') totalCantidades = calcularTotal(res,'cant_gestion');
                    else totalCantidades = calcularTotal(res);

                    res.forEach(el=>{
                        let cantidad = el.cantidad? el.cantidad:el.cant_gestion;                
                        el.porcentaje = (cantidad/totalCantidades)*100;
                    })
                    console.log(res)


                    /////////------------ordenar datos para tabla-------------------------------
                    console.log("Antes de ordenar: ",res);
                    res.sort((a, b) => (b.clientes? b.clientes:b.cant_clientes) - (a.clientes? a.clientes:a.cant_clientes));
                    console.log("Despues de ordenar: ",res);
                    /////////------------fin ordenar datos para tabla-------------------------------
                    //---------------obtener tabla info ---------------------
                    let totales = {clientes:0,capital:0,deuda:0,importe:0,cantidad:0,monto:0}
                    let html=``;
                    if(valorSelectGestion=='pdp'){
                        html = `<table class="table table-sm table-striped table-hover table-responsive-md">
                                        <thead class="cab-camp text-center">
                                            <tr>
                                                <th scope="col">ESTRUCTURA</th>
                                                <th scope="col">CLIENTES</th>
                                                <th scope="col">CAPITAL</th>
                                                <th scope="col">DEUDA</th>
                                                <th scope="col">IC</th>
                                                <th scope="col">PDP</th>
                                                <th scope="col">MONTO PDP</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">`
                    }else if(valorSelectGestion=='confirmacion'){
                        html = `<table class="table table-sm table-striped table-hover table-responsive-md">
                                        <thead class="cab-camp text-center">
                                            <tr>
                                                <th scope="col">ESTRUCTURA</th>
                                                <th scope="col">CLIENTES</th>
                                                <th scope="col">CAPITAL</th>
                                                <th scope="col">DEUDA</th>
                                                <th scope="col">IC</th>
                                                <th scope="col">CONF.</th>
                                                <th scope="col">MONTO CONF.</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">`
                    }else{
                        html = `<table class="table table-sm table-striped table-hover table-responsive-md">
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
                    }
                    res.forEach(el=>{
                        totales.clientes += el.clientes? parseInt(el.clientes):parseInt(el.cant_clientes);
                        totales.capital  += el.capital? parseFloat(el.capital):parseFloat(el.total_capital);
                        totales.deuda    += el.deuda? parseFloat(el.deuda):parseFloat(el.total_deuda);
                        totales.importe  += el.importe? parseFloat(el.importe):parseFloat(el.total_importe);
                        totales.cantidad += parseInt(el.cantidad);
                        totales.monto  += parseFloat(el.monto);

                        if(valorSelectGestion=='pdp' || valorSelectGestion=='confirmacion'){
                            html +=
                                        `<tr>
                                            <th scope="row">${el.tipo}</th>
                                            <td>${el.clientes? el.clientes:el.cant_clientes}</td>
                                            <td>${formatoMoneda(el.capital? el.capital:el.total_capital) }</td>
                                            <td>${formatoMoneda(el.deuda? el.deuda:el.total_deuda)}</td>
                                            <td>${formatoMoneda(el.importe? el.importe:el.total_importe)}</td>
                                            <td>${el.cantidad}</td>
                                            <td>${formatoMoneda(el.monto)}</td>
                                        </tr>`
                        }else{
                            html += 
                                        `<tr>
                                            <th scope="row">${el.tipo}</th>
                                            <td>${el.clientes? el.clientes:el.cant_clientes}</td>
                                            <td>${formatoMoneda(el.capital? el.capital:el.total_capital) }</td>
                                            <td>${formatoMoneda(el.deuda? el.deuda:el.total_deuda)}</td>
                                            <td>${formatoMoneda(el.importe? el.importe:el.total_importe)}</td>
                                        </tr>`
                        }
                    }) 
                    
                    if(valorSelectGestion=='pdp' || valorSelectGestion=='confirmacion'){
                        html += 
                                    `</tbody>
                                    <thead class="footer-total text-center">
                                        <tr>
                                            <th scope="row">TOTAL</th>
                                            <th scope="row">${totales.clientes}</th>
                                            <th scope="row">${formatoMoneda(totales.capital)}</th>
                                            <th scope="row">${formatoMoneda(totales.deuda)}</th>
                                            <th scope="row">${formatoMoneda(totales.importe)}</th>
                                            <th scope="row">${totales.cantidad}</th>
                                            <th scope="row">${formatoMoneda(totales.monto)}</th>
                                        </tr>
                                    </thead>
                                </table>`   
                    }else{
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
                    }               
            
                    contenedorGestion.innerHTML = html
                    /*--------------------fin tabla-------------------------------------- */



                    /*---------------------grafica--------------------------- */
                    let datos_tipo=[];
                    let datos_cantidad=[];
                    let datos_suma_otros=[];
                    /*----ordenar por porcentajes*/
                    console.log("Antes de ordenar: ",res);
                    //res.sort((a, b) => b.porcentaje - a.porcentaje);
                    res.sort((a, b) => (b.clientes? b.clientes:b.cant_clientes) - (a.clientes? a.clientes:a.cant_clientes));
                    console.log("Despues de ordenar: ",res);

                    res.forEach(el => {
                        datos_tipo.push(el.tipo+' - '+ (el.clientes? parseInt(el.clientes):parseInt(el.cant_clientes)));
                        datos_cantidad.push(parseFloat(el.porcentaje))
                        datos_suma_otros.push(el.clientes? parseInt(el.clientes):parseInt(el.cant_clientes));
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
                            suma_datos2 += parseFloat(numero);
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
                                ],
                                //borderWidth: 1
                            }]
                    };

                    var chartOptions = {
                        legend: {
                            position: 'top',
                            labels: {
                                fontSize: 16
                            }
                        },
                        responsive: true, 
                        maintainAspectRatio: false,
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
                                    //if(dataset.data[idx] >= 1){//para que muestre en la torta los mayores a 1%
                                        var langle = labelxy[idx],
                                            dx = centerx + lradius * Math.cos(langle),
                                            dy = centery + lradius * Math.sin(langle),
                                            //val = (dataset.data[idx] / total)*100;
                                            val = Math.round(dataset.data[idx] / total * 100);
                                            console.log(val);
                                        ctx.fillText(val + '%', dx, dy);
                                    //}
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
            }//
        }else{///vista pagos
            if(valorSelectEstructura!="ubic"){
                cargando.style.display = '';
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
                    /*----------------para mostrar la grafica en porcentajes */
                    let anios = [];//
                    let datos = [];

                    anios = [...new Set(res.map(el=>el.tipo))]
                    let total = calcularTotalP(res,'total_pagos');
                    console.log({total})
                    anios.forEach(anio=>{
                        auxDatos = res.filter(el=>el.tipo == anio)
                        console.log({auxDatos})
                        clientes_total_estructura=calcularCantidadTotal(auxDatos)
                        sumaCapital_estructura = calcularTotalP(auxDatos,'capital_estructura_total')
                        sumaImporte_estructura = calcularTotalP(auxDatos,'importe_estructura_total')
                        cant_pagos = calcularTotalP(auxDatos,'cant_pagos')
                        cantidad = calcularCantidad(auxDatos)
                        suma_capital = calcularTotalP(auxDatos,'total_capital')
                        suma_deuda = calcularTotalP(auxDatos,'total_deuda')
                        suma_importe = calcularTotalP(auxDatos,'total_importe')
                        suma = calcularTotalP(auxDatos,'total_pagos')    
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
                                            <td>${el.suma_ic_total>0? Math.round10((parseFloat(el.monto)/parseFloat(el.suma_ic_total)*100),-2):'0'}%</td>
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
                                            <th>${totales.suma_ic_total>0? Math.round10((parseFloat(totales.monto)/parseFloat(totales.suma_ic_total)*100),-2):'0'}%</th>
                                        </tr>
                                    </thead>
                                </table>`                  
            
                    //contenedorPagos.innerHTML = html
                    contenedorGestion.innerHTML = html
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
            }else{//pagos ubic
                cargando.style.display = '';
                fetch('buscarc_ubic_pag/'+valorSelectCartera+'/'+valorSelectInicio+'/'+valorSelectFin)
                .then(res=>res.json())
                .then(res=>{
                    cargando.style.display = 'none';
                    console.log(res);
                    if(pieChart!=null) pieChart.destroy();
                    let anios = [];
                    let datos = [];

                    anios = [...new Set(res.map(el=>el.tipo))]
                    let total = calcularTotalP(res,'monto_pagos');
                    console.log({total})
                    anios.forEach(anio=>{
                        auxDatos = res.filter(el=>el.tipo == anio)
                        console.log({auxDatos})
                        clientes_total_estructura=calcularCantidadP(auxDatos,'clientes')
                        sumaCapital_estructura = calcularTotalP(auxDatos,'capital')
                        sumaImporte_estructura = calcularTotalP(auxDatos,'importe')
                        //cant_pagos = calcularTotalP(auxDatos,'monto_pagos')
                        cantidad = calcularCantidadP(auxDatos,'clientes_pagos')
                        suma_capital = calcularTotalP(auxDatos,'capital_pagos')
                        //suma_deuda = calcularTotalP(auxDatos,'deuda')
                        suma_importe = calcularTotalP(auxDatos,'importe_pagos')
                        suma = calcularTotalP(auxDatos,'monto_pagos')    
                        porcentaje = Math.round((suma/total)*100);
                        datos.push({
                            anio:anio,
                            monto:suma,
                            porcentaje:porcentaje,
                            cantidad:cantidad,
                            //cant_pagos:cant_pagos,
                            capital:suma_capital,
                            //deuda:suma_deuda,
                            importe:suma_importe,
                            cli_est_total:clientes_total_estructura,
                            suma_cap_total:sumaCapital_estructura,
                            suma_ic_total:sumaImporte_estructura,

                        })
                    })
                    console.log(datos.length)
                    console.log({datos})
                    res = datos;
                    console.log(res);
                    console.log("Antes de ordenar: ",res);
                    res.sort((a, b) => b.monto - a.monto);
                    console.log("Despues de ordenar: ",res);

                    /*
                    cli_est_total
                    suma_cap_total
                    suma_ic_total
                    * */

                    //---------------obtener tabla info ---------------------
                    let totales = {cantidad:0,/*cant_pagos:0,*/capital:0,/*deuda:0,*/importe:0,monto:0,cli_est_total:0,suma_cap_total:0,suma_ic_total:0}
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
                        //totales.cant_pagos += parseInt(el.cant_pagos);
                        totales.capital  += parseFloat(el.capital);
                        //totales.deuda    += parseFloat(el.deuda);
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
                                            <td>${el.suma_ic_total>0? Math.round10((parseFloat(el.monto)/parseFloat(el.suma_ic_total)*100),-2):'0'}%</td>
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
                                            <th>${totales.suma_ic_total>0? Math.round10((parseFloat(totales.monto)/parseFloat(totales.suma_ic_total)*100),-2):'0'}%</th>
                                        </tr>
                                    </thead>
                                </table>`                  
            
                    //contenedorPagos.innerHTML = html
                    contenedorGestion.innerHTML = html
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
    
    }
}

function calcularTotal(arr,prop = 'cantidad'){
  let total = 0;
  arr.forEach(el=>{total += el[prop]})
  return total;
}



//pagos
function calcularTotalP(arr,prop){
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

function calcularCantidadP(arr,prop){
    let total = 0;
    arr.forEach(el=>{
      total += parseInt(el[prop]);
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