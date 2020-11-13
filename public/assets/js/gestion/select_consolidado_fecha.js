console.log('Ready!!')

const contenedorTabla = document.getElementById('tabla');
const contenedorExport = document.getElementById('btn-export');
const cargando= document.getElementById('cargando');
const botonBuscar = document.getElementById('buscar');

const selectFecha = document.getElementById('fecha')

botonBuscar.addEventListener('click',(event)=>{event.preventDefault();reporteGestiones()});

function reporteGestiones(){
    contenedorTabla.innerHTML = '';
    cargando.style.display = '';
    const fecha = selectFecha.value;
    console.log({fecha})
    fetch('mostrar_consolidado_fecha/'+fecha)
        .then(res=>res.json())
        .then(res=>{
            cargando.style.display = 'none';
            console.log(res);
            let datos=res.data1;
            console.log(datos);
            let datos2=res.data2;
            console.log(datos2);

            contenedorTabla.innerHTML = '';
            contenedorExport.innerHTML = '';
            let totales = {contador:0,clientes:0,capital:0,deuda:0,importe:0,ct:0,cc:0,uc:0,unc:0,usg:0,it:0,ic:0,monto:0,cumplido:0,caido:0,vigente:0}
            let totales2 = {contador:0,clientes:0,capital:0,deuda:0,importe:0,ct:0,cc:0,uc:0,unc:0,usg:0,it:0,ic:0,monto:0,cumplido:0,caido:0,vigente:0}
            let htmlTable = `<table class="table table-sm table-bordered table-responsive-lg" id="tabla">
                                <thead class="cab-camp text-center" style="font-size:10px;">
                                    <tr>
                                        <th style="font-size:12px;">${datos[0].fecha_registro}</th>
                                    </tr>
                                    <tr style="border:1px solid black !important;">
                                        <th rowspan="2" style="vertical-align: middle;">CARTERA</th>
                                        <th rowspan="2" style="vertical-align: middle;">CLIENTES</th>
                                        <th rowspan="2" style="vertical-align: middle;">CAPITAL</th>
                                        <th rowspan="2" style="vertical-align: middle;">DEUDA</th>
                                        <th rowspan="2" style="vertical-align: middle;">IC</th>
                                        <th colspan="2">COBERTURA</th>
                                        <th colspan="3">UBICABILIDAD</th>
                                        <th colspan="2">INTENSIDAD</th>
                                        <th colspan="4">PDPS</th>
                                    </tr>
                                    <tr style="border:1px solid black !important;">
                                        <th class="th">CARTERA TOTAL</th>
                                        <th class="th">CARTERA CONTACTO</th>
                                        <th class="th">CONTACTO + ND</th>
                                        <th class="th">NO CONTACTO</th>
                                        <th class="th">SIN HISTÓRICO</th>
                                        <th class="th">CARTERA TOTAL</th>
                                        <th class="th">CARTERA CONTACTO</th>
                                        <th style="vertical-align: middle;">MONTO GENERADO</th>
                                        <th style="vertical-align: middle;">CUMPLIDO</th>
                                        <th style="vertical-align: middle;">CAÍDO</th>
                                        <th style="vertical-align: middle;">VIGENTE</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center" style="font-size:12px;">`
            datos.forEach((el)=>{
                totales.contador += 1;
                totales.clientes += parseInt(el.clientes);
                totales.capital  += parseFloat(el.capital);
                totales.deuda    += parseFloat(el.deuda);
                totales.importe  += parseFloat(el.importe);
                totales.ct       += el.cob_total>0? parseInt(el.cob_total):0;
                totales.cc       += el.cob_contacto>0? parseInt(el.cob_contacto):0;
                totales.uc       += el.ubic_contacto>0? parseFloat(el.ubic_contacto):0;
                totales.unc      += el.ubic_no_contacto>0? parseFloat(el.ubic_no_contacto):0;
                totales.usg      += el.sin_gestion>0? parseFloat(el.sin_gestion):0;
                totales.it       += el.int_total>0? parseFloat(el.int_total):0;
                totales.ic       += el.int_contacto>0? parseFloat(el.int_contacto):0;
                totales.monto    += el.monto>0? parseFloat(el.monto):0;
                totales.cumplido    += el.cumplido>0? parseFloat(el.cumplido):0;
                totales.caido    += el.caido>0? parseFloat(el.caido):0;
                totales.vigente   += el.vigente>0? parseFloat(el.vigente):0;


                    htmlTable+=     `<tr style="border:1px solid black !important;">
                                        <th style="font-size:10px !important;">${el.cartera}</th>
                                        <td>${formatoNumero(el.clientes)}</td>
                                        <td>S/.${el.capital>0? formatoNumero(el.capital):'0'}</td>
                                        <td>S/.${el.deuda>0? formatoNumero(el.deuda):'0'}</td>
                                        <td>S/.${el.importe>0? formatoNumero(el.importe):'0'}</td>
                                        <td>${el.cob_total>0? Math.round10(el.cob_total,-1):'0'}%</td>
                                        <td>${el.cob_contacto>0? Math.round10(el.cob_contacto,-1):'0'}%</td>
                                        <td>${el.ubic_contacto>0? Math.round10(el.ubic_contacto,-1):'0'}%</td>
                                        <td>${el.ubic_no_contacto>0? Math.round10(el.ubic_no_contacto,-1):'0'}%</td>
                                        <td>${el.sin_gestion>0? Math.round10(el.sin_gestion,-1):'0'}%</td>
                                        <td>${Math.round10(el.int_total,-1)}</td>
                                        <td>${Math.round10(el.int_contacto,-1)}</td>
                                        <td>S/.${el.monto>0? formatoNumero(el.monto):'0'}</td>
                                        <td>S/.${el.cumplido>0? formatoNumero(el.cumplido):'0'}</td>
                                        <td>S/.${el.caido>0? formatoNumero(el.caido):'0'}</td>
                                        <td>S/.${el.vigente>0? formatoNumero(el.vigente):'0'}</td>
                                    </tr>`
            })

                htmlTable += `</tbody>
                                <thead class="footer-total text-center">
                                    <tr style="font-size:11px !important;border:1px solid black !important;">
                                        <th>TOTAL</th>
                                        <th>${formatoNumero(totales.clientes)}</th>
                                        <th>S/.${formatoNumero(totales.capital)}</th>
                                        <th>S/.${formatoNumero(totales.deuda)}</th>
                                        <th>S/.${formatoNumero(totales.importe)}</th>
                                        <th>${Math.round10((totales.ct/totales.contador),-1)}%</th>
                                        <th>${Math.round10((totales.cc/totales.contador),-1)}%</th>
                                        <th>${Math.round10((totales.uc/totales.contador),-1)}%</th>
                                        <th>${Math.round10((totales.unc/totales.contador),-1)}%</th>
                                        <th>${Math.round10((totales.usg/totales.contador),-1)}%</th>
                                        <th>${Math.round10((totales.it/totales.contador),-1)}</th>
                                        <th>${Math.round10((totales.ic/totales.contador),-1)}</th>
                                        <th>S/.${formatoNumero(totales.monto)}</th>
                                        <th>S/.${formatoNumero(totales.cumplido)}</th>
                                        <th>S/.${formatoNumero(totales.caido)}</th>
                                        <th>S/.${formatoNumero(totales.vigente)}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td></td>
                                </tr>
                                </tbody>
                                <thead class="cab-camp text-center" style="font-size:10px;">                                   
                                    <tr>
                                        <th style="font-size:12px;">${datos2[0].fecha_registro}</th>
                                    </tr>
                                    <tr style="border:1px solid black !important;">
                                        <th rowspan="2" style="vertical-align: middle;">CARTERA</th>
                                        <th rowspan="2" style="vertical-align: middle;">CLIENTES</th>
                                        <th rowspan="2" style="vertical-align: middle;">CAPITAL</th>
                                        <th rowspan="2" style="vertical-align: middle;">DEUDA</th>
                                        <th rowspan="2" style="vertical-align: middle;">IC</th>
                                        <th colspan="2">COBERTURA</th>
                                        <th colspan="3">UBICABILIDAD</th>
                                        <th colspan="2">INTENSIDAD</th>
                                        <th colspan="4">PDPS</th>
                                    </tr>
                                    <tr style="border:1px solid black !important;">
                                        <th class="th">CARTERA TOTAL</th>
                                        <th class="th">CARTERA CONTACTO</th>
                                        <th class="th">CONTACTO + ND</th>
                                        <th class="th">NO CONTACTO</th>
                                        <th class="th">SIN HISTÓRICO</th>
                                        <th class="th">CARTERA TOTAL</th>
                                        <th class="th">CARTERA CONTACTO</th>
                                        <th style="vertical-align: middle;">MONTO GENERADO</th>
                                        <th style="vertical-align: middle;">CUMPLIDO</th>
                                        <th style="vertical-align: middle;">CAÍDO</th>
                                        <th style="vertical-align: middle;">VIGENTE</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center" style="font-size:12px;">`
            datos2.forEach((el)=>{
                totales2.contador += 1;
                totales2.clientes += parseInt(el.clientes);
                totales2.capital  += parseFloat(el.capital);
                totales2.deuda    += parseFloat(el.deuda);
                totales2.importe  += parseFloat(el.importe);
                totales2.ct       += el.cob_total>0? parseInt(el.cob_total):0;
                totales2.cc       += el.cob_contacto>0? parseInt(el.cob_contacto):0;
                totales2.uc       += el.ubic_contacto>0? parseFloat(el.ubic_contacto):0;
                totales2.unc      += el.ubic_no_contacto>0? parseFloat(el.ubic_no_contacto):0;
                totales2.usg      += el.sin_gestion>0? parseFloat(el.sin_gestion):0;
                totales2.it       += el.int_total>0? parseFloat(el.int_total):0;
                totales2.ic       += el.int_contacto>0? parseFloat(el.int_contacto):0;
                totales2.monto    += el.monto>0? parseFloat(el.monto):0;
                totales2.cumplido    += el.cumplido>0? parseFloat(el.cumplido):0;
                totales2.caido    += el.caido>0? parseFloat(el.caido):0;
                totales2.vigente   += el.vigente>0? parseFloat(el.vigente):0;


                    htmlTable+=     `<tr style="border:1px solid black !important;">
                                        <th style="font-size:10px !important;">${el.cartera}</th>
                                        <td>${formatoNumero(el.clientes)}</td>
                                        <td>S/.${el.capital>0? formatoNumero(el.capital):'0'}</td>
                                        <td>S/.${el.deuda>0? formatoNumero(el.deuda):'0'}</td>
                                        <td>S/.${el.importe>0? formatoNumero(el.importe):'0'}</td>
                                        <td>${el.cob_total>0? Math.round10(el.cob_total,-1):'0'}%</td>
                                        <td>${el.cob_contacto>0? Math.round10(el.cob_contacto,-1):'0'}%</td>
                                        <td>${el.ubic_contacto>0? Math.round10(el.ubic_contacto,-1):'0'}%</td>
                                        <td>${el.ubic_no_contacto>0? Math.round10(el.ubic_no_contacto,-1):'0'}%</td>
                                        <td>${el.sin_gestion>0? Math.round10(el.sin_gestion,-1):'0'}%</td>
                                        <td>${Math.round10(el.int_total,-1)}</td>
                                        <td>${Math.round10(el.int_contacto,-1)}</td>
                                        <td>S/.${el.monto>0? formatoNumero(el.monto):'0'}</td>
                                        <td>S/.${el.cumplido>0? formatoNumero(el.cumplido):'0'}</td>
                                        <td>S/.${el.caido>0? formatoNumero(el.caido):'0'}</td>
                                        <td>S/.${el.vigente>0? formatoNumero(el.vigente):'0'}</td>
                                    </tr>`
            })

                htmlTable += `</tbody>
                                <thead class="footer-total text-center">
                                    <tr style="font-size:11px !important;border:1px solid black !important;">
                                        <th>TOTAL</th>
                                        <th>${formatoNumero(totales2.clientes)}</th>
                                        <th>S/.${formatoNumero(totales2.capital)}</th>
                                        <th>S/.${formatoNumero(totales2.deuda)}</th>
                                        <th>S/.${formatoNumero(totales2.importe)}</th>
                                        <th>${Math.round10((totales2.ct/totales2.contador),-1)}%</th>
                                        <th>${Math.round10((totales2.cc/totales2.contador),-1)}%</th>
                                        <th>${Math.round10((totales2.uc/totales2.contador),-1)}%</th>
                                        <th>${Math.round10((totales2.unc/totales2.contador),-1)}%</th>
                                        <th>${Math.round10((totales.usg/totales2.contador),-1)}%</th>
                                        <th>${Math.round10((totales2.it/totales2.contador),-1)}</th>
                                        <th>${Math.round10((totales2.ic/totales2.contador),-1)}</th>
                                        <th>S/.${formatoNumero(totales2.monto)}</th>
                                        <th>S/.${formatoNumero(totales2.cumplido)}</th>
                                        <th>S/.${formatoNumero(totales2.caido)}</th>
                                        <th>S/.${formatoNumero(totales2.vigente)}</th>
                                    </tr>
                                </thead>
                    </table>`;

            contenedorTabla.innerHTML = htmlTable;
            let htmlExport=``;
            htmlExport=`<button type="submit" class="btn btn-success" id="btnExportar" onclick="tableToExcel('tabla', 'Resumen_Gestion');">Exportar</button>`;
            contenedorExport.innerHTML = htmlExport;
        })
}

/*const botonExportar = document.getElementById('btnExportar');
botonExportar.addEventListener('click',(event)=>{event.preventDefault();exportarArchivoExcel()});
function exportarArchivoExcel(){
    tableToExcel('tabla', 'prueba');
}*/

var tableToExcel = (function() {
    var uri = 'data:application/vnd.ms-excel;base64,'
      , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
      , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
      , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
    return function(table, name) {
      if (!table.nodeType) table = document.getElementById(table)
      var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
      window.location.href = uri + base64(format(template, ctx))
    }
})()
/*const btnExportar = document.querySelector("#btnExportar");
const tabla = document.querySelector("#tabla");*/

//new TableExport(document.getElementsByTagName("tabla"));
/*new TableExport(document.getElementsByTagName("tabla"), {
    headers: true,                      // (Boolean), display table headers (th or td elements) in the <thead>, (default: true)
    footers: true,                      // (Boolean), display table footers (th or td elements) in the <tfoot>, (default: false)
    formats: ["xlsx", "csv", "txt"],    // (String[]), filetype(s) for the export, (default: ['xlsx', 'csv', 'txt'])
    filename: "id",                     // (id, String), filename for the downloaded file, (default: 'id')
    bootstrap: true,                   // (Boolean), style buttons using bootstrap, (default: true)
    exportButtons: false,                // (Boolean), automatically generate the built-in export buttons for each of the specified formats (default: true)
    position: "bottom",                 // (top, bottom), position of the caption element relative to table, (default: 'bottom')
    ignoreRows: null,                   // (Number, Number[]), row indices to exclude from the exported file(s) (default: null)
    ignoreCols: null,                   // (Number, Number[]), column indices to exclude from the exported file(s) (default: null)
    trimWhitespace: true,               // (Boolean), remove all leading/trailing newlines, spaces, and tabs from cell text in the exported file(s) (default: false)
    RTL: false,                         // (Boolean), set direction of the worksheet to right-to-left (default: false)
    sheetname: "id"                     // (id, String), sheet name for the exported spreadsheet, (default: 'id')
  });*/


/*btnExportar.addEventListener("click", function() {
    let tableExport = new TableExport(tabla, {
        exportButtons: false, // No queremos botones
        filename: "Mi tabla de Excel", //Nombre del archivo de Excel
        sheetname: "Mi tabla de Excel", //Título de la hoja
    });
    let datos = tableExport.getExportData();
    let preferenciasDocumento = datos.tabla.xlsx;
    tableExport.export2file(preferenciasDocumento.data, preferenciasDocumento.mimeType, preferenciasDocumento.filename, preferenciasDocumento.fileExtension, preferenciasDocumento.merges, preferenciasDocumento.RTL, preferenciasDocumento.sheetname);
});*/


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

function formatoNumero(monto){
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
  
    return montoFormateado;
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



