@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />

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


    <form id="formulario">
        <div class="card-body">

            <div class="col-md-4">
                <div class="form-group">
                    <label>Fecha:</label>
                    <input style="width:50%;" type="date" class="form-control" id="fecha">
                </div>
            </div>

            <div class="col-md-8">
                <div class="form-group">
                    <label>Descripción:</label>
                    <input type="text" class="form-control" autocomplete="off" maxlength="800" id="descripcion">
                </div>

                <div class="form-group" style="float: right">
                    <br>
                    <button type="button" onclick="addAgregarFila()" class="btn btn-primary btn-sm float-right" style="margin-top:10px;">
                        <i class="fas fa-plus" title="Agregar"></i> Agregar</button>
                </div>
            </div>
        </div>

        <div class="card-body">

            <div class="row" >
                <table class="table" id="matriz"  data-toggle="table">
                    <thead>
                    <tr>
                        <th style="width: 3%">#</th>
                        <th style="width: 5%">Cantidad</th>
                        <th style="width: 10%">Descripción</th>
                        <th style="width: 5%">Opciones</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </form>

</div>

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
        });
    </script>

    <script>

        function addAgregarFila(){

            var nFilas = $('#matriz >tbody >tr').length;
            nFilas += 1;

            var markup = "<tr>" +

                "<td>" +
                "<p id='fila" + (nFilas) + "' class='form-control' style='max-width: 65px'>" + (nFilas) + "</p>" +
                "</td>" +

                "<td>" +
                "<input name='cantidadArray[]' maxlength='10' class='form-control' type='number'>" +
                "</td>" +

                "<td>" +
                "<input name='descripcionArray[]' data-info='0' class='form-control' style='width:100%' onkeyup='buscarMaterial(this)' maxlength='400'  type='text'>" +
                "<div class='droplista' style='position: absolute; z-index: 9; width: 75% !important;'></div>" +
                "</td>" +

                "<td>" +
                "<button type='button' class='btn btn-block btn-danger' onclick='borrarFila(this)'>Borrar</button>" +
                "</td>" +

                "</tr>";

            $("#matriz tbody").append(markup);
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

            if(fecha === ''){
                toastr.error('Fecha es requerida');
            }

            if(descripc === ''){

            }else{
                if(descripc.length > 800){
                    toastr.error('descripción maxímo 800 caracteres');
                    return;
                }
            }

            var reglaNumeroEntero = /^[0-9]\d*$/;

            var nRegistro = $('#matriz > tbody >tr').length;
            let formData = new FormData();

            if (nRegistro <= 0){
                toastr.error('Registro Entrada son requeridos');
                return;
            }

            var cantidad = $("input[name='cantidadArray[]']").map(function(){return $(this).val();}).get();
            var descripcion = $("input[name='descripcionArray[]']").map(function(){return $(this).val();}).get();
            var descripcionAtributo = $("input[name='descripcionArray[]']").map(function(){return $(this).attr("data-info");}).get();

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

            // como tienen la misma cantidad de filas, podemos recorrer
            // todas las filas de una vez
            for(var p = 0; p < cantidad.length; p++){

                formData.append('cantidad[]', cantidad[p]);
                formData.append('datainfo[]', descripcionAtributo[p]);
            }

            openLoading();

            formData.append('fecha', fecha);
            formData.append('descripcion', descripc);

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
            $("#matriz tbody tr").remove();
        }

    </script>


@endsection
