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
                <h2>Registro de Entradas</h2>
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
                                    <label>Fecha:</label>
                                    <input style="width: 35%; margin-left: 25px;" type="date" class="form-control" id="fecha">
                                </div>
                            </div>

                            <div style="margin-left: 15px; margin-right: 15px; margin-top: 15px;">
                                <div class="form-group">
                                    <label>Factura u Orden de Compra:</label>
                                    <input type="text" class="form-control" autocomplete="off" maxlength="50" id="factura">
                                </div>
                            </div>

                            <div style="margin-left: 15px; margin-right: 15px; margin-top: 15px;">
                                <div class="form-group">
                                    <label>Descripción:</label>
                                    <input type="text" class="form-control" autocomplete="off" maxlength="800" id="descripcion">
                                </div>
                            </div>

                            <div class="form-group" style="float: right">
                                <br>
                                <button type="button" onclick="abrirModal()" class="btn btn-primary btn-sm float-right" style="margin-top:10px; margin-right: 15px;">
                                    <i class="fas fa-plus" title="Agregar Repuesto"></i> Agregar Repuesto</button>
                            </div>
                        </div>

                    </div>

                </div>


                <div class="col-md-6">

                    <div class="card card-danger">
                        <div class="card-header">
                            <h3 class="card-title">Tipo de Ingreso</h3>
                        </div>
                        <div class="card-body">

                            <div class="row">

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="radio" id="check-nuevo"
                                               name="contact" value="Repuesto Nuevo" style="transform: scale(1.3);">
                                        <label for="contactChoice1"> Repuesto Nuevo</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="radio" id="check-inventario"
                                               name="contact" value="Repuesto en Inventario" style="transform: scale(1.3);">
                                        <label for="contactChoice2"> Repuesto en Inventario</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Documento (opcional)</label>
                                    <input type="file" id="documento" class="form-control" accept="image/jpeg, image/jpg, image/png, .pdf"/>
                                </div>

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
                    <h4 class="modal-title">Agregar Repuesto</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form id="formulario-repuesto">
                        <div class="card-body">

                            <div class="form-group">
                                <label class="control-label">Repuesto</label>

                                <table class="table" id="matriz-busqueda" data-toggle="table">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <input id="repuesto" data-info='0' class='form-control' style='width:100%' onkeyup='buscarMaterial(this)' maxlength='400'  type='text'>
                                            <div class='droplista' style='position: absolute; z-index: 9; width: 75% !important;'></div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="form-group" >
                                <label class="control-label">Cantidad</label>
                                <div class="col-md-6">
                                    <input id="cantidad" class='form-control' type='number' placeholder="0">
                                </div>
                            </div>

                            <div class="form-group" >
                                <label class="control-label">Seleccionar Equipo</label>
                                <div class="col-md-6">
                                    <select id="select-equipo" class="form-control">
                                        @foreach($equipos as $item)
                                            <option value="{{$item->id}}">{{ $item->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Precio $</label>
                                <div class="col-md-6">
                                    <input class='form-control' id="precio" type='number' placeholder="0.00">
                                </div>
                            </div>

                        </div>
                    </form>

                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="agregarFila()">Agregar</button>
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
                        <th style="width: 10%">Descripción</th>
                        <th style="width: 6%">Cantidad</th>
                        <th style="width: 6%">Precio</th>
                        <th style="width: 8%">Equipo</th>
                        <th style="width: 5%">Opciones</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>

            </div>
        </div>
    </section>

    <div class="modal-footer justify-content-between" style="margin-top: 25px;">
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

            document.getElementById("formulario-repuesto").reset();
            $('#select-equipo').prop('selectedIndex', 0).change();
            $('#modalRepuesto').css('overflow-y', 'auto');
            $('#modalRepuesto').modal({backdrop: 'static', keyboard: false})
        }

        function agregarFila(){
            var repuesto = document.querySelector('#repuesto');
            var nomRepuesto = document.getElementById('repuesto').value;
            var cantidad = document.getElementById('cantidad').value;
            var equipo = document.getElementById('select-equipo').value;
            var equipoNombre = $( "#select-equipo option:selected" ).text();
            var precio = document.getElementById('precio').value;

            if(repuesto.dataset.info == 0){
                toastr.error("Repuesto es requerido");
                return;
            }

            var reglaNumeroEntero = /^[0-9]\d*$/;
            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

            //*************

            if(cantidad === ''){
                toastr.error('Cantidad es requerida');
                return;
            }

            if(!cantidad.match(reglaNumeroEntero)) {
                toastr.error('Cantidad debe ser número Entero y no Negativo');
                return;
            }

            if(cantidad < 0){
                toastr.error('Cantidad no debe ser negativo');
                return;
            }

            if(cantidad.length > 10){
                toastr.error('Cantidad máximo 10 caracteres');
                return;
            }

            //*****************

            if(equipo === ''){
                toastr.error('Equipo es requerido');
                return;
            }

            //*****************

            if(precio === ''){
                toastr.error('Precio es requerido');
                return;
            }

            if(!precio.match(reglaNumeroDecimal)) {
                toastr.error('Precio debe ser número Decimal y no Negativo');
                return;
            }

            if(precio < 0){
                toastr.error('Precio no debe ser negativo');
                return;
            }

            if(precio.length > 10){
                toastr.error('Precio máximo 10 caracteres');
                return;
            }

            //**************

            var nFilas = $('#matriz >tbody >tr').length;
            nFilas += 1;

            var markup = "<tr>" +

                "<td>" +
                "<p id='fila" + (nFilas) + "' class='form-control' style='max-width: 65px'>" + (nFilas) + "</p>" +
                "</td>" +

                "<td>" +
                "<input name='descripcionArray[]' disabled data-info='" + repuesto.dataset.info + "' value='" + nomRepuesto + "' class='form-control' type='text'>" +
                "</td>" +

                "<td>" +
                "<input name='cantidadArray[]' disabled value='" + cantidad + "' class='form-control' type='number'>" +
                "</td>" +

                "<td>" +
                "<input name='precioArray[]' disabled value='" + precio + "' class='form-control' type='number'>" +
                "</td>" +

                "<td>" +
                "<input name='equipoArray[]' disabled data-info='" + equipo + "' value='" + equipoNombre + "' class='form-control' type='text'>" +
                "</td>" +

                "<td>" +
                "<button type='button' class='btn btn-block btn-danger' onclick='borrarFila(this)'>Borrar</button>" +
                "</td>" +

                "</tr>";

            $("#matriz tbody").append(markup);

            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Agregado',
                showConfirmButton: false,
                timer: 1500
            })

            $(txtContenedorGlobal).attr('data-info', '0');
            document.getElementById("formulario-repuesto").reset();
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
                }

                axios.post(url+'/buscar/material', {
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

        function modificarValor(edrop){

            // obtener texto del li
            let texto = $(edrop).text();
            // setear el input de la descripcion
            $(txtContenedorGlobal).val(texto);

            // agregar el id al atributo del input descripcion
            $(txtContenedorGlobal).attr('data-info', edrop.id);

            //$(txtContenedorGlobal).data("info");
        }

        function preguntaGuardar(){
            colorBlancoTabla();

            Swal.fire({
                title: 'Guardar Entrada?',
                text: "",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    guardarEntrada();
                }
            })
        }

        function guardarEntrada(){

            var fecha = document.getElementById('fecha').value;
            var descripc = document.getElementById('descripcion').value; // max 800
            var factura = document.getElementById('factura').value; // max 50
            var checkNuevo = document.getElementById('check-nuevo').checked;
            var checkInventario = document.getElementById('check-inventario').checked;

            var documento = document.getElementById('documento'); // null file

            if(documento.files && documento.files[0]){ // si trae doc
                if (!documento.files[0].type.match('image/jpeg|image/jpeg|image/png|.pdf')){
                    toastr.error('formato permitidos: .png .jpg .jpeg .pdf');
                    return;
                }
            }

            if(fecha === ''){
                toastr.error('Fecha es requerida');
            }

            if(factura === ''){

            }else{
                if(factura.length > 50){
                    toastr.error('factura máximo 50 caracteres');
                    return;
                }
            }

            if(descripc === ''){

            }else{
                if(descripc.length > 800){
                    toastr.error('descripción máximo 800 caracteres');
                    return;
                }
            }

            // 0: el repuesto es nuevo
            // 1: el repuesto ya estaba en bodega
            if(checkNuevo){
                var entrada = 0;
            }else if(checkInventario){
                var entrada = 1;
            }else{
                toastr.error('Seleccionar Tipo de Ingreso');
                return;
            }

            var reglaNumeroEntero = /^[0-9]\d*$/;
            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

            var nRegistro = $('#matriz > tbody >tr').length;
            let formData = new FormData();

            if (nRegistro <= 0){
                toastr.error('Registro Entrada son requeridos');
                return;
            }

            var descripcion = $("input[name='descripcionArray[]']").map(function(){return $(this).val();}).get();
            var descripcionAtributo = $("input[name='descripcionArray[]']").map(function(){return $(this).attr("data-info");}).get();
            var cantidad = $("input[name='cantidadArray[]']").map(function(){return $(this).val();}).get();
            var precio = $("input[name='precioArray[]']").map(function(){return $(this).val();}).get();

            var equipoNom = $("input[name='equipoArray[]']").map(function(){return $(this).val();}).get();
            var equipo = $("input[name='equipoArray[]']").map(function(){return $(this).attr("data-info");}).get();

            // unicamente no sera verificado con: APORTE PATRONAL (aporte mano de obra)

            for(var a = 0; a < cantidad.length; a++){

                let detalle = descripcionAtributo[a];
                let datoCantidad = cantidad[a];

                // identifica si el 0 es tipo number o texto
                if(detalle == 0){
                    colorRojoTabla(a);
                    alertaMensaje('info', 'No encontrado', 'En la Fila #' + (a+1) + " El material no se encuentra. Por favor buscar de nuevo el Material");
                    return;
                }

                if (datoCantidad === '') {
                    colorRojoTabla(a);
                    toastr.error('Fila #' + (a + 1) + ' Cantidad es requerida');
                    return;
                }

                if (!datoCantidad.match(reglaNumeroEntero)) {
                    colorRojoTabla(a);
                    toastr.error('Fila #' + (a + 1) + ' Cantidad debe ser entero y no negativo');
                    return;
                }

                if (datoCantidad <= 0) {
                    colorRojoTabla(a);
                    toastr.error('Fila #' + (a + 1) + ' Cantidad no debe ser negativo');
                    return;
                }

                if (datoCantidad.length > 10) {
                    colorRojoTabla(a);
                    toastr.error('Fila #' + (a + 1) + ' Cantidad máximo 10 caracteres');
                    return;
                }
            }

            for(var b = 0; b < descripcion.length; b++){

                var datoDescripcion = descripcion[b];

                if(datoDescripcion === ''){
                    colorRojoTabla(b);
                    toastr.error('Fila #' + (b+1) + ' la descripción es requerida');
                    return;
                }

                if(datoDescripcion.length > 300){
                    colorRojoTabla(b);
                    toastr.error('Fila #' + (b+1) + ' la descripción tiene más de 300 caracteres');
                }
            }

            for(var z = 0; z < equipoNom.length; z++){

                var datoDescripcion = equipoNom[z];
                let detalle = equipo[z];

                // identifica si el 0 es tipo number o texto
                if(detalle == 0){
                    colorRojoTabla(z);
                    alertaMensaje('info', 'No encontrado', 'En la Fila #' + (z+1) + " El Equipo no es encontrado");
                    return;
                }

                if(datoDescripcion === ''){
                    colorRojoTabla(z);
                    toastr.error('Fila #' + (z+1) + ' El equipo es requerido');
                    return;
                }
            }

            for(var f = 0; f < precio.length; f++){

                let datoCantidad = precio[f];

                if (datoCantidad === '') {
                    colorRojoTabla(f);
                    toastr.error('Fila #' + (f + 1) + ' Precio es requerido');
                    return;
                }

                if (!datoCantidad.match(reglaNumeroDecimal)) {
                    colorRojoTabla(f);
                    toastr.error('Fila #' + (f + 1) + ' Precio debe ser decimal y no negativo');
                    return;
                }

                if (datoCantidad <= 0) {
                    colorRojoTabla(f);
                    toastr.error('Fila #' + (f + 1) + ' Precio no debe ser negativo');
                    return;
                }

                if (datoCantidad.length > 10) {
                    colorRojoTabla(f);
                    toastr.error('Fila #' + (f + 1) + ' Precio máximo 10 caracteres');
                    return;
                }
            }

            //*******************

            // como tienen la misma cantidad de filas, podemos recorrer
            // todas las filas de una vez
            for(var p = 0; p < cantidad.length; p++){

                formData.append('cantidad[]', cantidad[p]);
                formData.append('precio[]', precio[p]);
                formData.append('equipo[]', equipo[p]);
                formData.append('datainfo[]', descripcionAtributo[p]);
            }

            openLoading();

            formData.append('fecha', fecha);
            formData.append('descripcion', descripc);
            formData.append('entrada', entrada);
            formData.append('factura', factura);
            formData.append('documento', documento.files[0]);

            axios.post(url+'/entrada/guardar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
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

        function limpiar(){
            document.getElementById('descripcion').value = '';
            document.getElementById('factura').value = '';

            document.getElementById('check-nuevo').checked = false;
            document.getElementById('check-inventario').checked = false;

            $("#matriz tbody tr").remove();
        }

    </script>


@endsection
