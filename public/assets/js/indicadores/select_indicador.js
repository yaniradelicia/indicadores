console.log('Ready!!')

function validaForm(){
    //campo select
    if($("#cartera").val() == ""){
        alert("Seleccionar Cartera.");
        $("#cartera").focus();       // Esta función coloca el foco de escritura del usuario en el campo Nombre directamente.
        return false;
    }
    if($("#indicador").val() == ""){
        alert("Seleccionar Indicador.");
        $("#indicador").focus();       // Esta función coloca el foco de escritura del usuario en el campo Nombre directamente.
        return false;
    }

    if($("#asignacion").val() == ""){
        alert("Seleccionar una Asignacion.");
        $("#asignacion").focus();
        return false;
    }
    return true;
}

function validaFormEstr(){
    //campo select
    if($("#item").val() == ""){
        alert("Seleccionar un valor para estructura.");
        $("#item").focus();       // Esta función coloca el foco de escritura del usuario en el campo Nombre directamente.
        return false;
    }
    return true;
}



var lineChart = null;
let selectCartera = document.getElementById('cartera')
let selectEstructura = document.getElementById('estructura')
const selectIndicador = document.getElementById('indicador')
const selectItem = document.getElementById('item')

//para mostrar tabla

//const contenedorCanvas = document.getElementById('densityChart');

//--------------------------------- chart gestorr-------------------------------------------
selectCartera.addEventListener('change',()=>{asignacion()});
selectEstructura.addEventListener('change',()=>{items()});

var valorSelectCartera = '';
var valorSelectEstructura = '';
/*console.log({valorSelectCartera});
console.log({valorSelectEstructura});*/

function asignacion(){
    $('#estructura').val("");
    //$('#item').Attr('disabled','true');
    $('#item').val("");
    for (let i = selectItem.options.length; i >= 1; i--) {
        selectItem.remove(i);
      }
    valorSelectCartera = selectCartera.options[selectCartera.selectedIndex].value;
    const contenedorAsignacion = document.getElementById('asignacion');
    //console.log({valorSelectCartera});
    if(valorSelectCartera=='34'){
        let html = `
            <option selected value="">Seleccione</option>
            <option class="option" value="0">Toda la Cartera</option>
            <option class="option" value="CALL 01">CALL 01</option>
            <option class="option" value="CALL 02">CALL 02</option>
            <option class="option" value="CALL 03">CALL 03</option>
            <option class="option" value="nuevos">NUEVOS CASTIGO</option>
        
        `;
        contenedorAsignacion.innerHTML = html;
    }else{
        let html = `
            <option selected value="">Seleccione</option>
            <option class="option" value="0">Toda la Cartera</option>
            <option class="option" value="CALL 01">CALL 01</option>
            <option class="option" value="CALL 02">CALL 02</option>
            <option class="option" value="CALL 03">CALL 03</option>
            <option class="option" value="nuevos">NUEVOS</option>
        
        `;
        contenedorAsignacion.innerHTML = html;
    }
}

function items(){
    
    for (let i = selectItem.options.length; i >= 1; i--) {
        selectItem.remove(i);
      }
    //$('#item').removeAttr('disabled');
    const contenedorItem = document.getElementById('item');
    //$('#estructura').val("");
    //$('#contenedor-item').html("");
    valorSelectEstructura = selectEstructura.options[selectEstructura.selectedIndex].value;
    //console.log({valorSelectEstructura});
    if(valorSelectCartera!='' && valorSelectEstructura!=''){
        let url = '';
        let label='';
        if(valorSelectEstructura=='entidades'){
            url = 'lista_entidades/'+valorSelectCartera;
            label='Entidades';
        }else if(valorSelectEstructura=='score'){
            url = 'lista_score/'+valorSelectCartera;
            label='Score';
        }else if(valorSelectEstructura=='dep'){
            url = 'lista_dep/'+valorSelectCartera;
            label='Departamentos';
        }else if(valorSelectEstructura=='tramo'){
            url = 'lista_tramos/'+valorSelectCartera;
            label='Tramos';
        }else if(valorSelectEstructura=='prioridad'){
            url = 'lista_prioridad/'+valorSelectCartera;
            label='Prioridad';
        }else if(valorSelectEstructura=='dep_ind'){
            url = 'lista_situacion/'+valorSelectCartera;
            label='Situación';
        }else if(valorSelectEstructura=='saldo_deuda'){
            url=null;
            label='Rango Deuda';
        }
        else if(valorSelectEstructura=='capital'){
            url=null;
            label='Rango Capital';
        }
        else if(valorSelectEstructura=='monto_camp'){
            url=null;
            label='Rango Importe';
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
                        <option class="option" value="${el.valor}">${el.valor}</option>
                    `
                });
                
                contenedorItem.innerHTML = htmlSelect;
            })
        }else{
            let htmlSelect=``;
            htmlSelect += `
                        <option selected value=""></option>
                        <option selected value="1">A: [0-500> </option>
                        <option selected value="2">B: [500-1000> </option>
                        <option selected value="3">C: [1000-3000> </option>
                        <option selected value="4">D: [3000-+> </option>
                        <option selected value="">Seleccione</option>
                        `
            ;
            contenedorItem.innerHTML = htmlSelect;
        }
    }
}

const selectAsignacion = document.getElementById('asignacion')
//const selectItem = document.getElementById('item')
const contenedorMensajeIndicador= document.getElementById('mensaje-indicador')
const cargando= document.getElementById('cargando')
//selectCartera.addEventListener('change',()=>{indicadoresGestion()});
//selectIndicador.addEventListener('change',()=>{indicadoresGestion()});
const botonBuscar = document.getElementById('buscar')
botonBuscar.addEventListener('click',(event)=>{event.preventDefault();indicadores()});

function indicadores(){
    
    if(lineChart!=null) lineChart.destroy();
    const valorSelectIndicador = selectIndicador.options[selectIndicador.selectedIndex].value;
    const valorSelectAsignacion = selectAsignacion.options[selectAsignacion.selectedIndex].value;
    const valorSelectItem = selectItem.options[selectItem.selectedIndex].value;
    console.log({valorSelectCartera});
    console.log({valorSelectIndicador});
    console.log({valorSelectAsignacion});
    console.log({valorSelectEstructura});
    console.log({valorSelectItem});
    //if(valorSelectCartera!="" && valorSelectIndicador!="" && valorSelectAsignacion!="" ){
    if(validaForm()){

        if(valorSelectIndicador=="cobertura" || valorSelectIndicador=="contacto" || valorSelectIndicador=="efectiva" || valorSelectIndicador=="intensidad"){
            let url = '';
            let estado=false;
            
            if(valorSelectEstructura!=""){
                validaFormEstr();
                if(valorSelectEstructura!="" && valorSelectItem!=""){
                    if(valorSelectIndicador=='cobertura'){
                        url = 'buscari_co/'+valorSelectCartera+'/'+valorSelectAsignacion+'/'+valorSelectEstructura+'/'+valorSelectItem
                    }else if(valorSelectIndicador=='contacto'){
                        url = 'buscari_con/'+valorSelectCartera+'/'+valorSelectAsignacion+'/'+valorSelectEstructura+'/'+valorSelectItem
                    }else if(valorSelectIndicador=='efectiva'){
                        url = 'buscari_conef/'+valorSelectCartera+'/'+valorSelectAsignacion+'/'+valorSelectEstructura+'/'+valorSelectItem
                    }else if(valorSelectIndicador=='intensidad'){
                        url = 'buscari_in/'+valorSelectCartera+'/'+valorSelectAsignacion+'/'+valorSelectEstructura+'/'+valorSelectItem
                    }
                    estado= true;
                }else{
                    url ='';
                    estado=false;
                }
            }else{
                if(valorSelectIndicador=='cobertura'){
                    url = 'buscari_co/'+valorSelectCartera+'/'+valorSelectAsignacion+'/'+(valorSelectEstructura || null)+'/'+(valorSelectItem || null)
                }else if(valorSelectIndicador=='contacto'){
                    url = 'buscari_con/'+valorSelectCartera+'/'+valorSelectAsignacion+'/'+(valorSelectEstructura || null)+'/'+(valorSelectItem || null)
                }else if(valorSelectIndicador=='efectiva'){
                    url = 'buscari_conef/'+valorSelectCartera+'/'+valorSelectAsignacion+'/'+(valorSelectEstructura || null)+'/'+(valorSelectItem || null)
                }else if(valorSelectIndicador=='intensidad'){
                    url = 'buscari_in/'+valorSelectCartera+'/'+valorSelectAsignacion+'/'+(valorSelectEstructura || null)+'/'+(valorSelectItem || null)
                }
                estado=true;
            }

            if(estado==true){
                cargando.style.display = '';
            }
            fetch(url)
            .then(res=>res.json())
            .then(res=>{
                cargando.style.display = 'none';
                //console.log(res);
                let cantidad=Object.keys(res).length;
                console.log(cantidad);

                let totales=[];
                let datos=[];

                if(cantidad==2){
                    totales=res.totales;
                    datos=res.datos1;
                    //console.log(datos);
                }else{
                    let datos1=res.datos1;
                    let datos2=res.datos2;
                    let datos3=res.datos3;
                    totales=res.totales;
                    console.log(totales);

                    datos = datos1.concat(datos2,datos3);
                    console.log(datos);
                }
                if(lineChart!=null) lineChart.destroy();
                let htmlIndicador=``;

                contenedorMensajeIndicador.innerHTML=``;

                if(totales.length==0 || datos.length==0){
                    htmlIndicador=`<p>No hay Datos</p>`;
                    contenedorMensajeIndicador.innerHTML = htmlIndicador;

                }else{

                    //obtener los meses
                    let meses = [...new Set(datos.map(dato=>parseInt(dato.fec.substr(5,7))))]
                    console.log(meses);
                    //agrupar por meses
                    let datosPorMes = []
                    meses.forEach(mes=>{
                        let arrDatos = datos.filter(dato=>mes == parseInt(dato.fec.substr(5,7)))
                        datosPorMes.push({mes,datos:arrDatos})
                    })
                    //console.log({datosPorMes});

                    if(valorSelectIndicador=='intensidad'){
                        datosPorMes.forEach((datoPorMes,index)=>{
                            let acumulador = 0;
                            datoPorMes.datos.forEach(dato=>{
                            acumulador += parseInt(dato.can_clientes)
                            dato.can_clientes_acumulada = acumulador;
                            dato.porcentaje = Math.round10((acumulador/totales[index].total_cuentas),-2);
                            })
                        })
                    }else{
                        datosPorMes.forEach((datoPorMes,index)=>{
                            let acumulador = 0;
                            datoPorMes.datos.forEach(dato=>{
                            acumulador += parseInt(dato.can_clientes)
                            dato.can_clientes_acumulada = acumulador;
                            dato.porcentaje = Math.round((acumulador/totales[index].total_cuentas)*100);
                            })
                        })
                    }                
                    console.log({datosPorMes});
                    let arrDiasMes = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31];

                    let meses_chart = [];
                    let date = new Date()
                    arrDiasMes.forEach((_,index)=>{
                        meses.forEach((_,index_mes)=>{
                            if(index===0) meses_chart[index_mes] = []
                            let diaMes = datosPorMes[index_mes].datos.find(dato=>parseInt(dato.fec.substr(8,10)) === index + 1)
                            meses_chart[index_mes][index] = diaMes? diaMes.porcentaje:null;
                            /*if(index+1<date.getDate()){
                                meses_chart[index_mes][index] = diaMes? diaMes.porcentaje:meses_chart[index_mes][index-1];
                            }*/
                        })
                    })
                    console.log({meses_chart})

                    let labels = datosPorMes.map(datoPorMes=> datoPorMes.datos[0].fec.substr(0,7))

                    console.log({labels})

                    var speedCanvas = document.getElementById("speedChart");

                    Chart.defaults.global.defaultFontFamily = "Lato";
                    Chart.defaults.global.defaultFontSize = 14;

                    let colors = ['#E67E22','#27AE60','#A569BD']
                    let arr_data = [];
                    meses.forEach((mes,index)=>{
                    arr_data[index] = {
                        label:labels[index],
                        data:meses_chart[index],
                        lineTension: 0,
                        fill:false,
                        borderColor: colors[index]
                    }
                    })
                    var speedData = {
                        labels: arrDiasMes,
                        datasets: arr_data
                    };

                    var chartOptions = {
                        scales: {
                            yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                callback: function(value, index, values) {
                                    if(valorSelectIndicador=='intensidad'){
                                        return value;
                                    }else{
                                        return value + '%';
                                    }
                                },
                                fontSize: 20
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
                                if(valorSelectIndicador=='intensidad'){
                                    return t.yLabel;
                                }else{
                                    return t.yLabel +'%';
                                }
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
                }
            })
        }else if(valorSelectIndicador=="directa" || valorSelectIndicador=="tasa"){
            let url = '';
            
            if(valorSelectEstructura!=""){
                validaFormEstr();
                if(valorSelectEstructura!="" && valorSelectItem!=""){
                    if(valorSelectIndicador=='directa'){
                        url = 'buscari_di/'+valorSelectCartera+'/'+valorSelectAsignacion+'/'+valorSelectEstructura+'/'+valorSelectItem
                    }else if(valorSelectIndicador=='tasa'){
                        url = 'buscari_tc/'+valorSelectCartera+'/'+valorSelectAsignacion+'/'+valorSelectEstructura+'/'+valorSelectItem
                    }
                    estado=true;
                }else{
                    url ='';
                    estado=false;
                }
            }else{
                if(valorSelectIndicador=='directa'){
                    url = 'buscari_di/'+valorSelectCartera+'/'+valorSelectAsignacion+'/'+(valorSelectEstructura || null)+'/'+(valorSelectItem || null)
                }else if(valorSelectIndicador=='tasa'){
                    url = 'buscari_tc/'+valorSelectCartera+'/'+valorSelectAsignacion+'/'+(valorSelectEstructura || null)+'/'+(valorSelectItem || null)
                }
                estado=true;
            }

            if(estado==true){
                cargando.style.display = '';
            }

            fetch(url)
            .then(res=>res.json())
            .then(res=>{
                cargando.style.display = 'none';
                let cantidad=Object.keys(res).length;
                let totales=[];
                let datos=[];
                if(cantidad==2){
                    totales=res.totales;
                    datos=res.datos1;
                }else{
                    let total1=res.total1;
                    let datos1=res.datos1;
                    let total2=res.total2;
                    let datos2=res.datos2;
                    let total3=res.total3;
                    let datos3=res.datos3;
                    totales = total1.concat(total2,total3);
                    console.log(totales);
                    datos = datos1.concat(datos2,datos3);
                    console.log(datos);
                }

                if(lineChart!=null) lineChart.destroy();
                let htmlIndicador=``;

                contenedorMensajeIndicador.innerHTML=``;

                if(totales.length==0 || datos.length==0){
                    htmlIndicador=`<p>No hay Datos</p>`;
                    contenedorMensajeIndicador.innerHTML = htmlIndicador;

                }else{
                    /*-----------------totalessssss-------------------------------------- */
                    let meses1 = [...new Set(totales.map(total=>parseInt(total.fec.substr(5,7))))]
                    console.log(meses1);
                    let totalesPorMes = []
                    meses1.forEach(mes1=>{
                        let arrDatos = totales.filter(total=>mes1 == parseInt(total.fec.substr(5,7)))
                        totalesPorMes.push({mes1,totales:arrDatos})
                    })
                    console.log({totalesPorMes});
                    totalesPorMes.forEach((totalPorMes,index)=>{
                        let acumulador = 0;
                        totalPorMes.totales.forEach(total=>{
                        acumulador += parseInt(total.can_clientes)
                        total.can_clientes_acumulada = acumulador;
                        })
                    })
                    console.log({totalesPorMes})
                    /*------------------------------------------------------------------- */

                    /*-----------------datosssssssssss-------------------------------------- */
                    let meses2 = [...new Set(datos.map(dato=>parseInt(dato.fec.substr(5,7))))]
                    console.log(meses2);
                    let datosPorMes = []
                    meses2.forEach(mes2=>{
                        let arrDatos = datos.filter(dato=>mes2 == parseInt(dato.fec.substr(5,7)))
                        datosPorMes.push({mes2,datos:arrDatos})
                    })
                    console.log({datosPorMes});
                    datosPorMes.forEach((datoPorMes,index)=>{
                        let acumulador = 0;
                        datoPorMes.datos.forEach(dato=>{
                            if(dato.can_gestiones){
                                acumulador += parseInt(dato.can_gestiones)
                                dato.can_gestiones_acumulada = acumulador;
                            }else if(dato.can_promesas){
                                acumulador += parseInt(dato.can_promesas)
                                dato.can_promesas_acumulada = acumulador;
                            }
                        
                        })
                    })
                    console.log({datosPorMes})
                    /*------------------------------------------------------------------- */
                    totalesPorMes.forEach((totalPorMes,index)=>{
                        const mes = totalPorMes.mes1;
                        //Obtener los datos de datosPorMes;
                        const datoPorMes = datosPorMes.find(dato=>dato.mes2 === mes);
                        //console.log({datoPorMes})
                        totalPorMes.totales.forEach(total=>{
                            const elemento = datoPorMes.datos.find(el=>el.fec==total.fec);
                            //console.log(elemento);
                            if(elemento){
                                total.division = elemento.can_gestiones_acumulada? Math.round10((elemento.can_gestiones_acumulada/total.can_clientes_acumulada),-2):Math.round10((elemento.can_promesas_acumulada/total.can_clientes_acumulada)*100,-1);
                            }
                        })
                    })
                    console.log({totalesPorMes})
                    //Obteniendo los porcentajes de cada mes 
                    let arrDiasMes = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31];

                    let meses_chart = [];

                    arrDiasMes.forEach((_,index)=>{
                    meses2.forEach((_,index_mes)=>{
                        if(index===0) meses_chart[index_mes] = []
                        let diaMes = totalesPorMes[index_mes].totales.find(total=>parseInt(total.fec.substr(8,10)) === index + 1)
                        //meses_chart[index_mes][index] = diaMes? diaMes.porcentaje:meses_chart[index_mes][index-1]
                        meses_chart[index_mes][index] = diaMes? diaMes.division:null;
                    })
                    })

                    console.log({meses_chart})

                    let labels = totalesPorMes.map(totalPorMes=> totalPorMes.totales[0].fec.substr(0,7))

                    console.log({labels})

                    var speedCanvas = document.getElementById("speedChart");

                    Chart.defaults.global.defaultFontFamily = "Lato";
                    Chart.defaults.global.defaultFontSize = 14;

                    let colors = ['#E67E22','#27AE60','#A569BD']
                    let arr_data = [];
                    meses2.forEach((mes,index)=>{
                    arr_data[index] = {
                        label:labels[index],
                        data:meses_chart[index],
                        lineTension: 0,
                        fill:false,
                        borderColor: colors[index]
                    }
                    })
                    var speedData = {
                        labels: arrDiasMes,
                        datasets: arr_data
                    };

                    var chartOptions = {
                        scales: {
                            yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                callback: function(value, index, values) {
                                    if(valorSelectIndicador=='directa'){
                                        return value;
                                    }else{
                                        return value + '%';
                                    }
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
                                if(valorSelectIndicador=='directa'){
                                    return t.yLabel;
                                }else{
                                    return t.yLabel +'%';
                                }
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
                }
            })
        }else if(valorSelectIndicador=="promesas"){
            
            let url = '';
            if(valorSelectEstructura!=""){
                validaFormEstr();
                if(valorSelectEstructura!="" && valorSelectItem!=""){
                    url = 'buscari_ep/'+valorSelectCartera+'/'+valorSelectAsignacion+'/'+valorSelectEstructura+'/'+valorSelectItem
                    estado=true;
                }else{
                    url ='';
                    estado=false;
                }
            }else{
                url = 'buscari_ep/'+valorSelectCartera+'/'+valorSelectAsignacion+'/'+(valorSelectEstructura || null)+'/'+(valorSelectItem || null)
                estado=true;
            }
            if(estado==true){
                cargando.style.display = '';
            }
            
            fetch(url)
            .then(res=>res.json())
            .then(res=>{
                cargando.style.display = 'none';
                let cantidad=Object.keys(res).length;
                let totales=[];
                let datos=[];
                if(cantidad==2){
                    totales=res.totales;
                    datos=res.datos1;
                }else{
                    let total1=res.total1;
                    let datos1=res.datos1;
                    let total2=res.total2;
                    let datos2=res.datos2;
                    let total3=res.total3;
                    let datos3=res.datos3;
                    totales = total1.concat(total2,total3);
                    console.log(totales);
                    datos = datos1.concat(datos2,datos3);
                    console.log(datos);
                }

                if(lineChart!=null) lineChart.destroy();
                let htmlIndicador=``;
                contenedorMensajeIndicador.innerHTML=``;

                if(totales.length==0 || datos.length==0){
                    htmlIndicador=`<p>No hay Datos</p>`;
                    contenedorMensajeIndicador.innerHTML = htmlIndicador;

                }else{
                    /*-----------------totalessssss-------------------------------------- */
                    let meses1 = [...new Set(totales.map(total=>parseInt(total.fec.substr(5,7))))]
                    console.log(meses1);
                    let totalesPorMes = []
                    meses1.forEach(mes1=>{
                        let arrDatos = totales.filter(total=>mes1 == parseInt(total.fec.substr(5,7)))
                        totalesPorMes.push({mes1,totales:arrDatos})
                    })
                    console.log({totalesPorMes});
                    totalesPorMes.forEach((totalPorMes,index)=>{
                        let acumulador = 0;
                        totalPorMes.totales.forEach(total=>{
                        acumulador += parseInt(total.can_clientes)
                        total.can_clientes_acumulada = acumulador;
                        })
                    })
                    console.log({totalesPorMes})
                    /*------------------------------------------------------------------- */

                    /*-----------------datosssssssssss-------------------------------------- */
                    let meses2 = [...new Set(datos.map(dato=>parseInt(dato.fec.substr(5,7))))]
                    console.log(meses2);
                    let datosPorMes = []
                    meses2.forEach(mes2=>{
                        let arrDatos = datos.filter(dato=>mes2 == parseInt(dato.fec.substr(5,7)))
                        datosPorMes.push({mes2,datos:arrDatos})
                    })
                    console.log({datosPorMes});
                    datosPorMes.forEach((datoPorMes,index)=>{
                        let acumulador = 0;
                        datoPorMes.datos.forEach(dato=>{
                            if(dato.can_gestiones){
                                acumulador += parseInt(dato.can_gestiones)
                                dato.can_gestiones_acumulada = acumulador;
                            }else if(dato.can_promesas){
                                acumulador += parseInt(dato.can_promesas)
                                dato.can_promesas_acumulada = acumulador;
                            }
                        
                        })
                    })
                    console.log({datosPorMes})
                    /*------------------------------------------------------------------- */
                    totalesPorMes.forEach((totalPorMes,index)=>{
                        const mes = totalPorMes.mes1;
                        //Obtener los datos de datosPorMes;
                        const datoPorMes = datosPorMes.find(dato=>dato.mes2 === mes);
                        //console.log({datoPorMes})
                        totalPorMes.totales.forEach(total=>{
                            const elemento = datoPorMes.datos.find(el=>el.fec==total.fec);
                            //console.log(elemento);
                            if(elemento){
                                total.division = Math.round10((total.can_clientes_acumulada/elemento.can_gestiones_acumulada)*100,-2);
                            }
                        })
                    })
                    console.log({totalesPorMes})

                    //Obteniendo los porcentajes de cada mes 
                    let arrDiasMes = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31];

                    let meses_chart = [];

                    arrDiasMes.forEach((_,index)=>{
                    meses1.forEach((_,index_mes)=>{
                        if(index===0) meses_chart[index_mes] = []
                        let diaMes = totalesPorMes[index_mes].totales.find(total=>parseInt(total.fec.substr(8,10)) === index + 1)
                        //meses_chart[index_mes][index] = diaMes? diaMes.porcentaje:meses_chart[index_mes][index-1]
                        meses_chart[index_mes][index] = diaMes? diaMes.division:null;
                    })
                    })

                    console.log({meses_chart})

                    let labels = totalesPorMes.map(totalPorMes=> totalPorMes.totales[0].fec.substr(0,7))

                    console.log({labels});

                    var speedCanvas = document.getElementById("speedChart");

                    Chart.defaults.global.defaultFontFamily = "Lato";
                    Chart.defaults.global.defaultFontSize = 14;

                    let colors = ['#E67E22','#27AE60','#A569BD']
                    let arr_data = [];
                    meses1.forEach((mes,index)=>{
                    arr_data[index] = {
                        label:labels[index],
                        data:meses_chart[index],
                        lineTension: 0,
                        fill:false,
                        borderColor: colors[index]
                    }
                    })
                    var speedData = {
                        labels: arrDiasMes,
                        datasets: arr_data
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
                }
            })
        }
    }
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