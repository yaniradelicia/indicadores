console.log('Ready!!')
function validaForm(){
    //campo select
    if($("#cartera").val() == ""){
        alert("Seleccionar Cartera.");
        $("#cartera").focus(); 
        return false;
    }
    if($("#gestor").val() == ""){
        alert("Seleccionar Gestor.");
        $("#gestor").focus(); 
        return false;
    }
    if($("#estructura").val() == ""){
        alert("Seleccionar una Estructura.");
        $("#estructura").focus();  
        return false;
    }

    if($("#analisis").val() == ""){
        alert("Seleccionar una tipo de Análisis.");
        $("#analisis").focus();
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
const selectGestor = document.getElementById('i_gestor')
const selectEstructura = document.getElementById('estructura')
const selectAnalisis = document.getElementById('analisis')
const selectInicio = document.getElementById('inicio')
const selectFin = document.getElementById('fin')

const contenedor = document.getElementById('tabla-contenedor');
const contenedorMensaje = document.getElementById('mensaje');
const contenedorCanvas = document.getElementById('oilChart');
const contenedorItemGestor = document.getElementById('i_gestor');
const botonBuscar = document.getElementById('buscar');
const cargando= document.getElementById('cargando');

selectCartera.addEventListener('change',()=>{seleccionGestor()});
botonBuscar.addEventListener('click',(event)=>{event.preventDefault();indicadoresGestor()});

function seleccionGestor(){
    contenedorItemGestor.innerHTML='';
    valorSelectCartera = selectCartera.options[selectCartera.selectedIndex].value;
    console.log({valorSelectCartera})
    if(valorSelectCartera!=''){
        let url = '';
        if(valorSelectCartera=='0'){
            url='carga_gestores_total';
        }else{
            url = 'carga_gestores/'+valorSelectCartera;
        }
        if(url!=null){
            fetch(url)
            .then(res=>res.json())
            .then(res=>{
                console.log(res);
                let htmlSelect=``;
                    
                htmlSelect = `
                        <option selected value="">Seleccione</option>
                            `
                res.forEach(el=>{
                    htmlSelect += `
                        <option class="option" value="${el.emp_firma}">${el.emp_nom}</option>
                        `
                });
                    
                contenedorItemGestor.innerHTML = htmlSelect;
            })
        }
    }

}

function indicadoresGestor(){
    contenedor.innerHTML = '';
    contenedorMensaje.innerHTML = '';
    if(pieChart!=null) pieChart.destroy();
    const valorSelectCartera = selectCartera.options[selectCartera.selectedIndex].value;
    const valorSelectGestor = selectGestor.options[selectGestor.selectedIndex].value;
    const valorSelectEstructura = selectEstructura.options[selectEstructura.selectedIndex].value;
    const valorSelectAnalisis = selectAnalisis.options[selectAnalisis.selectedIndex].value;
    const valorSelectInicio = selectInicio.value;
    const valorSelectFin = selectFin.value;

    console.log({valorSelectCartera})
    console.log({valorSelectGestor})
    console.log({valorSelectEstructura})
    console.log({valorSelectAnalisis})
    console.log({valorSelectInicio})
    console.log({valorSelectFin})

    if(validaForm()){
        let url = '';
        if(valorSelectEstructura!="ubic"){
            if(valorSelectAnalisis=='pdps'){
                url = 'buscargestor_pdp/'+valorSelectCartera+'/'+valorSelectGestor+'/'+valorSelectEstructura+'/'+valorSelectInicio+'/'+valorSelectFin;
            }else if(valorSelectAnalisis=='confirmacion'){
                url = 'buscargestor_conf/'+valorSelectCartera+'/'+valorSelectGestor+'/'+valorSelectEstructura+'/'+valorSelectInicio+'/'+valorSelectFin;
            }else if(valorSelectAnalisis=='pagos'){
                url = 'buscargestor_pagos/'+valorSelectCartera+'/'+valorSelectGestor+'/'+valorSelectEstructura+'/'+valorSelectInicio+'/'+valorSelectFin;
            }else{
                url = 'buscargestor_g/'+valorSelectCartera+'/'+valorSelectGestor+'/'+valorSelectEstructura+'/'+valorSelectInicio+'/'+valorSelectFin
            }
        }else{
            if(valorSelectAnalisis=='pdps'){
                url = 'buscargestor_ubic_pdp/'+valorSelectCartera+'/'+valorSelectGestor+'/'+valorSelectInicio+'/'+valorSelectFin;
            }else if(valorSelectAnalisis=='confirmacion'){
                url = 'buscargestor_ubic_conf/'+valorSelectCartera+'/'+valorSelectGestor+'/'+valorSelectInicio+'/'+valorSelectFin;
            }else if(valorSelectAnalisis=='pagos'){
                url = 'buscargestor_ubic_pagos/'+valorSelectCartera+'/'+valorSelectGestor+'/'+valorSelectInicio+'/'+valorSelectFin;
            }else{
                url = 'buscargestor_ubic/'+valorSelectCartera+'/'+valorSelectGestor+'/'+valorSelectInicio+'/'+valorSelectFin
            }
        }
            cargando.style.display = '';
            fetch(url)
            .then(res=>res.json())
            .then(res=>{
                if(res.length>0){
                    console.log(res)
                    cargando.style.display = 'none';
                    let totalCantidades = 0;          
                    if(valorSelectAnalisis == 'gestion') totalCantidades = calcularTotal(res,'cant_clientes');
                    else totalCantidades = calcularTotal(res);
                    res.forEach(el=>{
                        let clientes_gestion = el.cantidad? el.cantidad:el.cant_clientes;                
                        el.porcentaje = (clientes_gestion/totalCantidades)*100;
                    })
                    console.log(res)

                    console.log("Antes de ordenar: ",res);
                    res.sort((a, b) => (b.clientes? b.clientes:b.cant_clientes) - (a.clientes? a.clientes:a.cant_clientes));
                    console.log("Despues de ordenar: ",res);

                    let totales = {clientes:0,capital:0,deuda:0,importe:0,cantidad:0,monto:0}
                    let html=``;
                    if(valorSelectAnalisis=='pdps'){
                        html = `<table class="table table-sm table-striped table-hover table-responsive-md">
                                        <thead class="cab-camp text-center">
                                            <tr>
                                                <th scope="col">ESTRUCTURA</th>
                                                <th scope="col">CLIENTES</th>
                                                <th scope="col">CAPITAL</th>
                                                <th scope="col">DEUDA</th>
                                                <th scope="col">IC</th>
                                                <th scope="col">CANT.PDP</th>
                                                <th scope="col">MONTO PDP</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">`
                    }else if(valorSelectAnalisis=='confirmacion'){
                        html = `<table class="table table-sm table-striped table-hover table-responsive-md">
                                        <thead class="cab-camp text-center">
                                            <tr>
                                                <th scope="col">ESTRUCTURA</th>
                                                <th scope="col">CLIENTES</th>
                                                <th scope="col">CAPITAL</th>
                                                <th scope="col">DEUDA</th>
                                                <th scope="col">IC</th>
                                                <th scope="col">CANT.CONF.</th>
                                                <th scope="col">MONTO CONF.</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">`
                    }else if(valorSelectAnalisis=='pagos'){
                        html = `<table class="table table-sm table-striped table-hover table-responsive-md">
                                        <thead class="cab-camp text-center">
                                            <tr>
                                                <th scope="col">ESTRUCTURA</th>
                                                <th scope="col">CLIENTES</th>
                                                <th scope="col">CAPITAL</th>
                                                <th scope="col">DEUDA</th>
                                                <th scope="col">IC</th>
                                                <th scope="col">CANT.PAGOS</th>
                                                <th scope="col">MONTO PAGOS</th>
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
                                                <th scope="col">CANT.GESTIONES</th>
                                                <th scope="col">INTENSIDAD</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">`
                    }
                    res.forEach(el=>{
                        totales.clientes += el.clientes? parseInt(el.clientes):parseInt(el.cant_clientes);
                        totales.capital  += el.capital? parseFloat(el.capital):parseFloat(el.total_capital);
                        totales.deuda    += el.deuda? parseFloat(el.deuda):parseFloat(el.total_deuda);
                        totales.importe  += el.importe? parseFloat(el.importe):parseFloat(el.total_importe);
                        totales.cantidad += el.cantidad? parseInt(el.cantidad):parseInt(el.cant_gestion);
                        totales.monto  += el.monto? parseFloat(el.monto):0;

                        if(valorSelectAnalisis=='pdps' || valorSelectAnalisis=='confirmacion' || valorSelectAnalisis=='pagos'){
                            html +=
                                        `<tr>
                                            <th scope="row">${el.tipo}</th>
                                            <td>${el.clientes? el.clientes:el.cant_clientes}</td>
                                            <td>${formatoMoneda(el.capital? el.capital:el.total_capital) }</td>
                                            <td>${formatoMoneda(el.deuda? el.deuda:el.total_deuda)}</td>
                                            <td>${formatoMoneda(el.importe? el.importe:el.total_importe)}</td>
                                            <td>${el.cantidad? el.cantidad:el.cant_gestion}</td>
                                            <td>${el.monto? formatoMoneda(el.monto):0}</td>
                                        </tr>`
                        }else{
                            html += 
                                        `<tr>
                                            <th scope="row">${el.tipo}</th>
                                            <td>${el.clientes? el.clientes:el.cant_clientes}</td>
                                            <td>${formatoMoneda(el.capital? el.capital:el.total_capital) }</td>
                                            <td>${formatoMoneda(el.deuda? el.deuda:el.total_deuda)}</td>
                                            <td>${formatoMoneda(el.importe? el.importe:el.total_importe)}</td>
                                            <td>${el.cant_gestion}</td>
                                            <td>${el.intensidad}</td>
                                        </tr>`
                        }
                    }) 
                    
                    if(valorSelectAnalisis=='pdps' || valorSelectAnalisis=='confirmacion' || valorSelectAnalisis=='pagos'){
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
                                            <th scope="row">${totales.cantidad}</th>
                                            <th scope="row">${Math.round10((totales.cantidad/totales.clientes),-2)}</th>
                                        </tr>
                                    </thead>
                                </table>`
                    }               
            
                    contenedor.innerHTML = html

                    let datos_tipo=[];
                    let datos_cantidad_clientes=[];
                    let datos_suma_otros=[];

                    /*----ordenar por porcentajes*/
                    console.log("Antes de ordenar: ",res);
                    //res.sort((a, b) => b.porcentaje - a.porcentaje);
                    res.sort((a, b) => (b.clientes? b.clientes:b.cant_clientes) - (a.clientes? a.clientes:a.cant_clientes));
                    console.log("Despues de ordenar: ",res);

                    res.forEach(el => {
                        datos_tipo.push(el.tipo+' - '+ (el.clientes? parseInt(el.clientes):parseInt(el.cant_clientes)));
                        datos_cantidad_clientes.push(parseFloat(el.porcentaje))
                        datos_suma_otros.push(el.clientes? parseInt(el.clientes):parseInt(el.cant_clientes));
                    });
                    console.log(datos_tipo);
                    console.log(datos_cantidad_clientes);
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

                        const datos1 = datos_cantidad_clientes.slice(0,7);
                        console.log("7 datos: ",datos1);
                        const datos2 = datos_cantidad_clientes.slice(7);
                        suma_datos2 = 0;
                        datos2.forEach (function(numero){
                            suma_datos2 += parseFloat(numero);
                        });
                        console.log('otros',suma_datos2);
                        datos1.push(suma_datos2);
                        datos_cantidad_clientes=datos1;
                        console.log(datos_cantidad_clientes);
                    }else{
                        datos_tipo=datos_tipo;
                        console.log(datos_tipo);
                        datos_cantidad_clientes=datos_cantidad_clientes;
                        console.log(datos_cantidad_clientes);
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
                                data: datos_cantidad_clientes,
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
                }else{
                    cargando.style.display = 'none';
                    contenedorMensaje.innerHTML = `No hay datos`;
                }
            })
    }
}

function calcularTotal(arr,prop = 'cantidad'){
    let total = 0;
    arr.forEach(el=>{total += el[prop]})
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