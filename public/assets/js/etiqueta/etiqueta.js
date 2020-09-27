function validaForm(){
    //campo select
    if($("#cartera").val() == ""){
        alert("Seleccionar Cartera.");
        $("#cartera").focus();       // Esta función coloca el foco de escritura del usuario en el campo Nombre directamente.
        return false;
    }
    // Campos de texto
    if($("#etiqueta").val() == ""){
        alert("Colocar Nombre a la Etiqueta.");
        $("#etiqueta").focus();       // Esta función coloca el foco de escritura del usuario en el campo Nombre directamente.
        return false;
    }
    if($("#clientes").val() == ""){
        alert("Completar el campo Clientes.");
        $("#clientes").focus();
        return false;
    }
    return true;
}

function limpiarFormulario() {
    $('#cartera').val("");
    $('#etiqueta').val("");
    $('#clientes').val("");
}
const botonInsertar = document.getElementById('insertar')
botonInsertar.addEventListener('click',(event)=>{event.preventDefault();selectInsertar()});

function selectInsertar(){
    const selectCartera = document.getElementById('cartera')
    const selectEtiqueta = document.getElementById('etiqueta')
    const selectClientes = document.getElementById('clientes')

    const contenedorMensaje = document.getElementById('mensaje');

    const valorSelectCartera = selectCartera.options[selectCartera.selectedIndex].value;
    const valorSelectEtiqueta = selectEtiqueta.value;
    const valorSelectClientes = selectClientes.value;

    console.log({valorSelectCartera})
    console.log({valorSelectEtiqueta})
    console.log({valorSelectClientes})

    if(validaForm()){
        //fetch('/campana/ver?usuarios='+(arrayUsuarios || null))
        //fetch('/etiqueta/insertar?car='+(valorSelectCartera || null)+'&etiqueta='+(valorSelectEtiqueta || null)+'&clientes='+(valorSelectClientes || null))
        fetch('/indicadores/public/etiqueta/insertar?car='+(valorSelectCartera || null)+'&etiqueta='+(valorSelectEtiqueta || null)+'&clientes='+(valorSelectClientes || null))
        .then(res=>res.json())
            .then(res=>{
                console.log(res);
                let html=``;
                if(res==true){
                html = `<div class="alert alert-success alert-dismissible" data-auto-dismiss="10000">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h6><i class="icon fa fa-check"></i> MENSAJE DE SISTEMA DE REGISTRO</h6>
                            <ul>
                                <li>Registro con éxito</li>
                            </ul>
                        </div>`
                }else{
                    html = `<div class="alert alert-danger alert-dismissible" data-auto-dismiss="10000">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h6><i class="icon fa fa-check"></i> MENSAJE DE SISTEMA DE REGISTRO</h6>
                            <ul>
                                <li>Error al Registrar</li>
                            </ul>
                        </div>`
                }
                contenedorMensaje.innerHTML = html
                limpiarFormulario()
                $(document).ready(function () {
                    //Cerrar Las Alertas Automaticamente
                    $('.alert[data-auto-dismiss]').each(function (index, element) {
                        const $element = $(element),
                            timeout = $element.data('auto-dismiss') || 10000;
                        setTimeout(function () {
                            $element.alert('close');
                        }, timeout);
                    });
                });
            })
    }
}