@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">

@stop

<style>
    table{
        /*Ajustar tablas*/
        table-layout:fixed;
    }
</style>

<div id="divcontenedor" style="display: none">

    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h2>Registro de Salidas para Aceites y Lubricantes</h2>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">

                <div class="col-md-6">

                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Información</h3>
                        </div>

                        <div class="card-body">

                            <div class="card-body">
                                <div class="row">
                                    <label>Fecha de Salida:</label>
                                    <input style="width: 35%; margin-left: 25px;" type="date" class="form-control" id="fecha">
                                </div>
                            </div>

                            <div style="margin-left: 15px; margin-right: 15px; margin-top: 15px;">
                                <div class="form-group">
                                    <label>Descripción (opcional):</label>
                                    <input type="text" class="form-control" autocomplete="off" maxlength="800" id="descripcion">
                                </div>
                            </div>

                            <div class="form-group" style="float: right">
                                <br>
                                <button type="button" onclick="abrirModal()" class="btn btn-primary btn-sm float-right" style="margin-top:10px; margin-right: 15px;">
                                    <i class="fas fa-plus" title="Agregar Repuesto"></i> Agregar Registro</button>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </section>


    <div class="modal fade" id="modalRepuesto" >
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Buscar Aceite o Lubricantes</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form id="formulario-repuesto">
                        <div class="card-body">

                            <div class="form-group">
                                <label class="control-label">Viscosidad</label>

                                <table class="table" id="matriz-busqueda" data-toggle="table">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <input id="repuesto" autocomplete="off" data-info='0' class='form-control' style='width:100%' onkeyup='buscarMaterial(this)' maxlength='400'  type='text'>
                                            <div class='droplista' style='position: absolute; z-index: 9; width: 75% !important;'></div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div style="margin-left: 15px; margin-right: 15px; margin-top: 15px;">
                                <div class="form-group">
                                    <label>Descripción (opcional):</label>
                                    <input type="text" class="form-control" autocomplete="off" maxlength="300" id="descripcion-aceite">
                                </div>
                            </div>


                            <!-- cargara vista de selección -->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="tablaRepuesto">

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>

                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h2>Detalle</h2>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Información de Ingreso</h3>
                </div>

                <table class="table" id="matriz" data-toggle="table" style="margin-right: 15px; margin-left: 15px;">
                    <thead>
                    <tr>
                        <th style="width: 3%">#</th>
                        <th style="width: 10%">Viscosidad</th>
                        <th style="width: 6%">Inventario</th>
                        <th style="width: 6%">Salida</th>
                        <th style="width: 8%">Descripción</th>
                        <th style="width: 5%">Opciones</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>

            </div>
        </div>
    </section>

    <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-success" onclick="preguntaGuardar()">Guardar</button>
    </div>

</div>

@extends('backend.menus.footerjs')
@section('archivos-js')

    <script src="{{ asset('js/jquery.dataTables.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/dataTables.bootstrap4.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/bootstrap-input-spinner.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/custom-editors.js') }}" type="text/javascript"></script>



    <script type="text/javascript">
        $(document).ready(function(){
            document.getElementById("divcontenedor").style.display = "block";

            var fecha = new Date();
            document.getElementById('fecha').value = fecha.toJSON().slice(0,10);

            window.seguroBuscador = true;
            window.txtContenedorGlobal = this;

            $(document).click(function(){
                $(".droplista").hide();
            });

            $(document).ready(function() {
                $('[data-toggle="popover"]').popover({
                    placement: 'top',
                    trigger: 'hover'
                });
            });

            $('#select-equipo').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });
        });
    </script>

    <script>

        function abrirModal(){
            document.getElementById('tablaRepuesto').innerHTML = "";
            document.getElementById("formulario-repuesto").reset();
            $('#select-equipo').prop('selectedIndex', 0).change();
            $('#modalRepuesto').modal('show');
        }

        function verificarSalida(){
            var divs = document.getElementsByClassName('arraycolor');
            for (var i = 0; i < divs.length; i++) {
                $(divs[i]).css("background-color", "transparent");
            }

            Swal.fire({
                title: 'Verificar?',
                text: "",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    agregarFila();
                }
            })
        }

        function agregarFila(){

            var repuesto = document.querySelector('#repuesto');
            var nomRepuesto = document.getElementById('repuesto').value;
            var descripcionaceite = document.getElementById('descripcion-aceite').value;
            var reglaNumeroEntero = /^[0-9]\d*$/;

            // VERIFICACIONES
            if(repuesto.dataset.info == 0){
                toastr.error("Aceite o Lubricante es requerido");
                return;
            }

            if(nomRepuesto === ''){
                toastr.error('Aceite o Lubricante es requerido');
            }

            // id de entrada_material
            var inputidcantidad = $("input[name='arraysalida[]']").map(function(){return $(this).attr("data-idcantidad");}).get();
            // input cantidad a sacar
            var inputcantidad = $("input[name='arraysalida[]']").map(function(){return $(this).val();}).get();
            // input max cantidad
            var inputmaxcantidad = $("input[name='arraysalida[]']").map(function(){return $(this).attr("data-maxcantidad");}).get();

            for(var a = 0; a < inputcantidad.length; a++) {

                let datoNumero = inputcantidad[a];
                let detalleIdCantidad = inputidcantidad[a];
                let detalleMaxCantidad = inputmaxcantidad[a];

                // identifica si el 0 es tipo number o texto
                if(detalleIdCantidad == 0){
                    divColorRojo(a);
                    alertaMensaje('info', 'No encontrado', 'En el Bloque de salida #' + (a+1) + " No se encuentra el identificador. Volver a buscar el Aceite o Lubricante.");
                    return;
                }

                // identifica si el 0 es tipo number o texto
                if(datoNumero === ''){
                    divColorRojo(a);
                    toastr.error('Cantidad es requerida');
                    return;
                }

                if(!datoNumero.match(reglaNumeroEntero)) {
                    divColorRojo(a);
                    toastr.error('Cantidad debe ser número Entero y no Negativo');
                    return;
                }

                if(datoNumero < 0){
                    divColorRojo(a);
                    toastr.error('Cantidad no debe ser negativo');
                    return;
                }

                // ignorar si la cantidad actual es 0
                let maximodeta = parseInt(detalleMaxCantidad);

                if(maximodeta != 0){
                    // no superar la maxima cantidad

                    if(datoNumero > maximodeta){
                        divColorRojo(a);
                        toastr.error('La cantidad a Retirar no puede ser mayor a ' + maximodeta);
                        return;
                    }
                }
            }

            // agregar a fila cada iteración mayor a 0
            //**************
            for(var z = 0; z < inputcantidad.length; z++) {

                let datoNumero = inputcantidad[z];
                let detalleIdEntrDeta = inputidcantidad[z]; // id entrada_detalle
                let detalleMaxCantidad = inputmaxcantidad[z]; // cantidad en inventario de este bloque

                if(datoNumero > 0){

                    var nFilas = $('#matriz >tbody >tr').length;
                    nFilas += 1;

                    var markup = "<tr>" +

                        "<td>" +
                        "<p id='fila" + (nFilas) + "' class='form-control' style='max-width: 65px'>" + (nFilas) + "</p>" +
                        "</td>" +

                        "<td>" +
                        "<input name='identradadetalleArray[]' type='hidden' data-identradadetalle='" + detalleIdEntrDeta + "'>" +
                        "<input disabled value='" + nomRepuesto + "' class='form-control' type='text'>" +
                        "</td>" +

                        "<td>" +
                        "<input disabled value='" + detalleMaxCantidad + "' class='form-control' type='text'>" +
                        "</td>" +

                        "<td>" +
                        "<input name='salidaArray[]' disabled value='" + datoNumero + "' class='form-control' type='text'>" +
                        "</td>" +

                        "<td>" +
                        "<input name='descripcionArray[]' disabled value='" + descripcionaceite + "' class='form-control' type='text'>" +
                        "</td>" +

                        "<td>" +
                        "<button type='button' class='btn btn-block btn-danger' onclick='borrarFila(this)'>Borrar</button>" +
                        "</td>" +

                        "</tr>";

                    $("#matriz tbody").append(markup);
                }

                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Agregado al Detalle',
                    showConfirmButton: false,
                    timer: 1500
                })

                $(txtContenedorGlobal).attr('data-info', '0');
                document.getElementById('tablaRepuesto').innerHTML = "";
                $('#select-equipo').prop('selectedIndex', 0).change();
                document.getElementById("formulario-repuesto").reset();
            }
        }

        function divColorRojo(pos){
            var divs = document.getElementsByClassName('arraycolor');
            $(divs[pos]).css("background-color", "red");
        }

        function preguntaGuardar(){
            colorBlancoTabla();

            Swal.fire({
                title: 'Guardar Salida?',
                text: "",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    guardarSalida();
                }
            })
        }

        function borrarFila(elemento){
            var tabla = elemento.parentNode.parentNode;
            tabla.parentNode.removeChild(tabla);
            setearFila()
        }

        // cambiar # de fila cada vez que se borra la fila de
        // tabla nuevo material
        function setearFila(){

            var table = document.getElementById('matriz');
            var conteo = 0;
            for (var r = 1, n = table.rows.length; r < n; r++) {
                conteo +=1;
                var element = table.rows[r].cells[0].children[0];
                document.getElementById(element.id).innerHTML = ""+conteo;
            }
        }

        function buscarMaterial(e){

            // seguro para evitar errores de busqueda continua
            if(seguroBuscador){
                seguroBuscador = false;

                var row = $(e).closest('tr');
                txtContenedorGlobal = e;

                let texto = e.value;

                if(texto === ''){
                    // si se limpia el input, setear el atributo id
                    $(e).attr('data-info', 0);
                    document.getElementById('tablaRepuesto').innerHTML = "";
                }

                axios.post(url+'/aceiteylubricantes/buscar/material', {
                    'query' : texto
                })
                    .then((response) => {

                        seguroBuscador = true;
                        $(row).each(function (index, element) {
                            $(this).find(".droplista").fadeIn();
                            $(this).find(".droplista").html(response.data);
                        });
                    })
                    .catch((error) => {
                        seguroBuscador = true;
                    });
            }
        }

        function guardarSalida(){

            var fecha = document.getElementById('fecha').value;
            var descripc = document.getElementById('descripcion').value; // max 800

            if(fecha === ''){
                toastr.error('Fecha es requerida');
            }

            if(descripc === ''){

            }else{
                if(descripc.length > 800){
                    toastr.error('descripción máximo 800 caracteres');
                    return;
                }
            }

            var reglaNumeroEntero = /^[0-9]\d*$/;

            var nRegistro = $('#matriz > tbody >tr').length;
            let formData = new FormData();

            if (nRegistro <= 0){
                toastr.error('Registro Salida son requeridos');
                return;
            }

            var identradaDetalle = $("input[name='identradadetalleArray[]']").map(function(){return $(this).attr("data-identradadetalle");}).get();
            var salidaDetalle = $("input[name='salidaArray[]']").map(function(){return $(this).val();}).get();
            var descripcionDetalle = $("input[name='descripcionArray[]']").map(function(){return $(this).val();}).get();

            // verificar que id entrada detalle exista
            // verificar que salida array detalle exista
            for(var a = 0; a < salidaDetalle.length; a++){

                let detalleS = identradaDetalle[a];
                let datoCantidad = salidaDetalle[a];

                // identifica si el 0 es tipo number o texto
                if(detalleS == 0){
                    colorRojoTabla(a);
                    alertaMensaje('info', 'Error', 'En la Fila #' + (a+1) + " El identificador del repuesto no se encontró. Borrar la Fila y agregar de nuevo.");
                    return;
                }

                if (datoCantidad === '') {
                    colorRojoTabla(a);
                    toastr.error('Fila #' + (a + 1) + ' Cantidad de salida es requerida');
                    return;
                }

                if (!datoCantidad.match(reglaNumeroEntero)) {
                    colorRojoTabla(a);
                    toastr.error('Fila #' + (a + 1) + ' Cantidad de salida debe ser entero y no negativo');
                    return;
                }

                if (datoCantidad <= 0) {
                    colorRojoTabla(a);
                    toastr.error('Fila #' + (a + 1) + ' Cantidad de salida no debe ser negativo');
                    return;
                }

                if (datoCantidad > 9000000) {
                    colorRojoTabla(a);
                    toastr.error('Fila #' + (a + 1) + ' Cantidad de salida debe tener máximo 99 millones');
                    return;
                }
            }

            //*******************

            // como tienen la misma cantidad de filas, podemos recorrer
            // todas las filas de una vez
            for(var p = 0; p < salidaDetalle.length; p++){
                formData.append('salida[]', salidaDetalle[p]);
                formData.append('identrada[]', identradaDetalle[p]);
                formData.append('descripciondeta[]', descripcionDetalle[p]);
            }

            openLoading();

            formData.append('fecha', fecha);
            formData.append('descripcion', descripc);

            axios.post(url+'/aceiteylubricantes/salida/guardar', formData, {
            })
                .then((response) => {
                    closeLoading();


                    if(response.data.success === 1){

                        let fila = response.data.fila;
                        let cantidad = response.data.cantidad;
                        colorRojoTabla(fila);
                        Swal.fire({
                            title: 'Cantidad no Disponible',
                            text: "Fila #" + (fila+1) + ", el Material cuenta con: " + cantidad + " unidades disponible",
                            icon: 'question',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    }
                    else if(response.data.success === 2){
                        toastr.success('Registrado correctamente');
                        limpiar();
                    }
                    else{
                        toastr.error('error al guardar');
                    }
                })
                .catch((error) => {
                    toastr.error('error al guardar');
                    closeLoading();
                });
        }

        function colorRojoTabla(index){
            $("#matriz tr:eq("+(index+1)+")").css('background', '#F1948A');
        }

        function colorBlancoTabla(){
            $("#matriz tbody tr").css('background', 'white');
        }

        function modificarValor(edrop){

            // obtener texto del li
            let texto = $(edrop).text();
            // setear el input de la descripcion
            $(txtContenedorGlobal).val(texto);

            // agregar el id al atributo del input descripcion
            $(txtContenedorGlobal).attr('data-info', edrop.id);

            var ruta = "{{ URL::to('/admin/aceiteylubricantes/cantidad/bloque/') }}/" + edrop.id;
            $('#tablaRepuesto').load(ruta);

            //$(txtContenedorGlobal).data("info");
        }

        function limpiar(){
            document.getElementById('descripcion').value = '';

            $("#matriz tbody tr").remove();
        }



    </script>


@endsection
