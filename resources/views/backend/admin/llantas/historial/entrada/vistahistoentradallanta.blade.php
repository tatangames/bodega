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

    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Entradas Registradas para Llantas</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="tablaDatatable">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalRepuesto" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Agregar Documento</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form id="formulario-repuesto">
                        <div class="card-body">

                            <div class="form-group">
                                <label>Documento</label>
                                <input id="id-entrada" type="hidden">
                                <input type="file" id="documento" class="form-control" accept="image/jpeg, image/jpg, image/png, .pdf"/>
                            </div>

                        </div>
                    </form>

                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarDocumento()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- modal editar -->
    <div class="modal fade" id="modalEditar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="formulario-editar">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <input type="hidden" id="id-editar">
                                    </div>

                                    <div class="form-group">
                                        <label>Fecha</label>
                                        <input type="date" class="form-control" id="fecha-editar">
                                    </div>

                                    <div class="form-group">
                                        <label>Descripción</label>
                                        <input type="text" maxlength="800" autocomplete="off" class="form-control" id="descripcion-editar">
                                    </div>

                                    <div class="form-group">
                                        <label>Factura</label>
                                        <input type="text" maxlength="50" autocomplete="off" class="form-control" id="factura-editar">
                                    </div>

                                    <div class="form-group">
                                        <select class="form-control" id="select-editar">
                                            <option value="0">Llanta Nueva</option>
                                            <option value="1">Llanta de Inventario</option>
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="editar()">Guardar</button>
                </div>
            </div>
        </div>
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
            var ruta = "{{ URL::to('/admin/historial/llantas/tabla') }}";
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ URL::to('/admin/historial/llantas/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        function informacion(id){
            window.location.href="{{ url('/admin/historial/entrada/llantadetalle') }}/" + id;
        }

        function modalBorrarDoc(id){
            Swal.fire({
                title: 'Borrar Documento?',
                text: "",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    borrarDoc(id);
                }
            })
        }

        function borrarDoc(id){
            let formData = new FormData();
            formData.append('id', id);

            axios.post(url+'/entradas/historialllanta/borrar/documento', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastr.success('Borrado correctamente');
                        recargar();
                    }
                    else{
                        toastr.error('error al borrar');
                    }
                })
                .catch((error) => {
                    toastr.error('error al borrar');
                    closeLoading();
                });
        }

        function informacionBorrarRegistro(id){
            Swal.fire({
                title: 'Borrar Registro?',
                text: "Se eliminara el registro y su documento si lo tiene. Y se verificara que no haya ninguna salida con esta Entrada",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    borrarRegistro(id);
                }
            })
        }

        function borrarRegistro(id){
            let formData = new FormData();
            formData.append('id', id);

            axios.post(url+'/entradas/historialllanta/borrar/registro', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastr.success('Borrado correctamente');
                        recargar();
                    }
                    // no puede borrar porque ya hay una salida de esta entrada.
                    else if(response.data.success === 2){
                        Swal.fire({
                            title: 'Error al Borrar',
                            text: "No se puede borrar el Registro porque ya hay una Salida de una llanta de esta Entrada",
                            icon: 'warning',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                recargar();
                            }
                        })
                    }
                    else{
                        toastr.error('error al borrar');
                    }
                })
                .catch((error) => {
                    toastr.error('error al borrar');
                    closeLoading();
                });
        }

        function infoSubirDoc(id){
            document.getElementById("formulario-repuesto").reset();
            $('#id-entrada').val(id);
            $('#modalRepuesto').modal('show');
        }

        function guardarDocumento(){
            var documento = document.getElementById('documento');
            var id = document.getElementById('id-entrada').value;

            if(documento.files && documento.files[0]){ // si trae doc
                if (!documento.files[0].type.match('image/jpeg|image/jpeg|image/png|.pdf')){
                    toastr.error('formato permitidos: .png .jpg .jpeg .pdf');
                    return;
                }
            }else{
                toastr.error('Documento es requerido');
                return;
            }

            let formData = new FormData();
            formData.append('id', id);
            formData.append('documento', documento.files[0]);

            axios.post(url+'/entradas/historialllanta/guardar/documento', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        $('#modalRepuesto').modal('hide');
                        toastr.success('Documento guardado');
                        recargar();
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

        function infoEditar(id){

            openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post(url+'/entradas/historialllanta/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal('show');
                        $('#id-editar').val(id);
                        $('#fecha-editar').val(response.data.info.fecha);
                        $('#descripcion-editar').val(response.data.info.descripcion);
                        $('#factura-editar').val(response.data.info.factura);

                        if(response.data.info.inventario === 0){
                            $('#select-editar').prop('selectedIndex', 0).change();
                        }else{
                            $('#select-editar').prop('selectedIndex', 1).change();
                        }

                    }else{
                        toastr.error('Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }

        function editar(){
            var id = document.getElementById('id-editar').value;
            var fecha = document.getElementById('fecha-editar').value;
            var descripcion = document.getElementById('descripcion-editar').value;
            var factura = document.getElementById('factura-editar').value;
            var inventario = document.getElementById('select-editar').value;

            if(fecha === ''){
                toastr.error('Fecha es requerido');
                return;
            }

            if(descripcion === ''){

            }else{
                if(descripcion.length > 800){
                    toastr.error('Máximo 800 caracteres para descripción');
                    return;
                }
            }

            if(factura === ''){

            }else{
                if(factura.length > 50){
                    toastr.error('Máximo 50 caracteres para factura');
                    return;
                }
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('fecha', fecha);
            formData.append('descripcion', descripcion);
            formData.append('factura', factura);
            formData.append('inventario', inventario);

            axios.post(url+'/entradas/historialllanta/editar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastr.success('Actualizado correctamente');
                        $('#modalEditar').modal('hide');
                        recargar();
                    }
                    else {
                        toastr.error('Error al actualizar');
                    }

                })
                .catch((error) => {
                    toastr.error('Error al actualizar');
                    closeLoading();
                });
        }


    </script>


@endsection
