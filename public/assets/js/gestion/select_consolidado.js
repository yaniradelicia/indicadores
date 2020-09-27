console.log('Ready!!')

const contenedorTabla = document.getElementById('tabla');
const botonBuscar = document.getElementById('buscar')
botonBuscar.addEventListener('click',(event)=>{event.preventDefault();reporteGestiones()});

function reporteGestiones(){
    fetch('mostrar_consolidado')
        .then(res=>res.json())
        .then(res=>{
            let gestiones=res.gestiones;
            console.log(gestiones);
            let pdps=res.pdps;
            console.log(pdps);

            gestiones.forEach(item=>{
                const itemPdp = pdps.find(el=>el.id == item.id);
                if(!itemPdp) return;
                item.monto = parseFloat(itemPdp.total_monto);
                item.cumplido = parseFloat(itemPdp.cumplido);
                item.caido = parseFloat(itemPdp.caidos);
                item.vigente = parseFloat(itemPdp.vigente);
            })
            
            console.log(gestiones);

            contenedorTabla.innerHTML = '';
            let totales = {contador:0,clientes:0,capital:0,deuda:0,importe:0,ct:0,cc:0,uc:0,unc:0,usg:0,it:0,ic:0,monto:0,cumplido:0,caido:0,vigente:0}
            let htmlTable = `<table class="table table-sm table-bordered table-responsive-lg">
                                <thead class="cab-camp text-center" style="font-size:10px;">
                                    <tr>
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
                                    <tr>
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
            gestiones.forEach((el)=>{
                totales.contador += 1;
                console.log(totales.contador);
                totales.clientes += parseInt(el.clientes_cartera_total);
                totales.capital  += parseFloat(el.capital);
                totales.deuda    += parseFloat(el.deuda);
                totales.importe  += parseFloat(el.importe);
                console.log(el.cobertura_total);
                totales.ct       += el.cobertura_total>0? parseInt(el.cobertura_total):0;
                console.log(totales.ct);
                totales.cc       += el.cobertura_contacto>0? parseInt(el.cobertura_contacto):0;
                totales.uc       += el.contactos>0? parseFloat(el.contactos):0;
                totales.unc      += el.no_contactos>0? parseFloat(el.no_contactos):0;
                totales.usg      += el.sin_gestion>0? parseFloat(el.sin_gestion):0;
                totales.it       += el.intensidad_total>0? parseFloat(el.intensidad_total):0;
                totales.ic       += el.intensidad_contacto>0? parseFloat(el.intensidad_contacto):0;
                totales.monto    += el.monto>0? parseFloat(el.monto):0;
                totales.cumplido    += el.monto>0? parseFloat(el.cumplido):0;
                totales.caido    += el.monto>0? parseFloat(el.caido):0;
                totales.vigente   += el.monto>0? parseFloat(el.vigente):0;


                    htmlTable+=     `<tr>
                                        <th style="font-size:10px !important;">${el.cartera}</th>
                                        <td>${formatoNumero(el.clientes_cartera_total)}</td>
                                        <td>S/.${el.capital>0? formatoMoneda(el.capital):'0'}</td>
                                        <td>S/.${el.deuda>0? formatoMoneda(el.deuda):'0'}</td>
                                        <td>S/.${el.importe>0? formatoMoneda(el.importe):'0'}</td>
                                        <td>${el.cobertura_total>0? el.cobertura_total:'0'}%</td>
                                        <td>${el.cobertura_contacto>0? el.cobertura_contacto:'0'}%</td>
                                        <td>${el.contactos>0? el.contactos:'0'}%</td>
                                        <td>${el.no_contactos>0? el.no_contactos:'0'}%</td>
                                        <td>${el.sin_gestion>0? el.sin_gestion:'0'}%</td>
                                        <td>${el.intensidad_total}</td>
                                        <td>${el.intensidad_contacto}</td>
                                        <td>S/.${el.monto>0? formatoMoneda(el.monto):'0'}</td>
                                        <td>S/.${el.cumplido>0? formatoMoneda(el.cumplido):'0'}</td>
                                        <td>S/.${el.caido>0? formatoMoneda(el.caido):'0'}</td>
                                        <td>S/.${el.vigente>0? formatoMoneda(el.vigente):'0'}</td>
                                    </tr>`
            })

                htmlTable += `</tbody>
                                <thead class="footer-total text-center">
                                    <tr style="font-size:11px !important;">
                                        <th>TOTAL</th>
                                        <th>${formatoNumero(totales.clientes)}</th>
                                        <th>S/.${formatoMoneda(totales.capital)}</th>
                                        <th>S/.${formatoMoneda(totales.deuda)}</th>
                                        <th>S/.${formatoMoneda(totales.importe)}</th>
                                        <th>${Math.round(totales.ct/totales.contador)}%</th>
                                        <th>${Math.round(totales.cc/totales.contador)}%</th>
                                        <th>${Math.round(totales.uc/totales.contador)}%</th>
                                        <th>${Math.round(totales.unc/totales.contador)}%</th>
                                        <th>${Math.round(totales.usg/totales.contador)}%</th>
                                        <th>${Math.round10((totales.it/totales.contador),-1)}</th>
                                        <th>${Math.round10((totales.ic/totales.contador),-1)}</th>
                                        <th>S/.${formatoMoneda(totales.monto)}</th>
                                        <th>S/.${formatoMoneda(totales.cumplido)}</th>
                                        <th>S/.${formatoMoneda(totales.caido)}</th>
                                        <th>S/.${formatoMoneda(totales.vigente)}</th>
                                    </tr>
                                </thead>
                    </table>`;

            contenedorTabla.innerHTML = htmlTable;
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