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
    .select2-container{
        height: 30px !important;
    }


</style>

<div id="divcontenedor" style="display: none">

    <section class="content-header">
        <div class="row">
            <h1 style="margin-left: 5px">Materiales</h1>
            <button type="button" style="margin-left: 15px" onclick="modalAgregar()" class="btn btn-primary btn-sm">
                <i class="fas fa-plus-square"></i>
                Nuevo Material
            </button>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Listado Catálogo de Materiales</h3>
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

    <div class="modal fade" id="modalAgregar">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nuevo Material</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo">
                        <div class="card-body">

                            <div class="form-group">
                                <label>Nombre *:</label>
                                <input type="text" class="form-control" autocomplete="off" onpaste="contarcaracteresIngreso();" onkeyup="contarcaracteresIngreso();" maxlength="300" id="nombre-nuevo" placeholder="Nombre del material">
                                <div id="res-caracter-nuevo" style="float: right">0/300</div>
                            </div>

                            <br>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Código:</label>
                                    <input type="text" class="form-control" autocomplete="off" id="codigo-nuevo" maxlength="12">
                                </div>
                            </div>


                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Unidad de Medida:</label>
                                    <br>
                                    <select width="60%"  class="form-control" id="select-unidad-nuevo">
                                        <option value="" selected>Seleccione una opción...</option>
                                        @foreach($lUnidad as $sel)
                                            <option value="{{ $sel->id }}">{{ $sel->medida }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="verificarGuardar()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- modal editar -->
    <div class="modal fade" id="modalEditar">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Material</h4>
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
                                        <label>Nombre *:</label>
                                        <input type="text" class="form-control" autocomplete="off" onpaste="contarcaracteresEditar();" onkeyup="contarcaracteresEditar();" maxlength="300" id="nombre-editar" placeholder="Nombre del material">
                                        <div id="res-caracter-editar" style="float: right">0/300</div>
                                    </div>

                                    <br>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Código:</label>
                                            <input type="text" class="form-control" autocomplete="off" id="codigo-editar" maxlength="12">
                                        </div>
                                    </div>


                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Unidad de Medida:</label>
                                            <br>
                                            <select style="width: 70%; height: 45px"  class="form-control" id="select-unidad-editar">
                                            </select>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="verificarEditar()">Actualizar</button>
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
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function(){
            var ruta = "{{ URL::to('/admin/materiales/tabla/index') }}";
            $('#tablaDatatable').load(ruta);

            $('#select-unidad-nuevo').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });

            $('#select-unidad-editar').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/materiales/tabla/index') }}";
            $('#tablaDatatable').load(ruta);
        }

        function modalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            document.getElementById('res-caracter-nuevo').innerHTML = '0/300 ';

            $('#select-codigo-nuevo').prop('selectedIndex', 0).change();
            $('#select-unidad-nuevo').prop('selectedIndex', 0).change();
            $('#select-clasi-nuevo').prop('selectedIndex', 0).change();

            $('#modalAgregar').modal({backdrop: 'static', keyboard: false})
        }

        function verificarGuardar(){
            Swal.fire({
                title: 'Guardar Material?',
                text: "",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    nuevo();
                }
            })
        }

        function verificarEditar(){
            Swal.fire({
                title: 'Actualizar Material?',
                text: "",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    editar();
                }
            })
        }

        function nuevo(){

            var nombre = document.getElementById('nombre-nuevo').value;
            var codigo = document.getElementById('codigo-nuevo').value;
            var unidad = document.getElementById('select-unidad-nuevo').value; // nullable

            if(nombre === ''){
                toastr.error('Nombre es requerido');
                return;
            }

            if(nombre.length > 300){
                toastr.error('Nombre máximo 300 caracteres');
                return;
            }

            if(codigo === ''){

            }else{
                if(codigo.length > 100){
                    toastr.error('Código máximo 100 caracteres');
                    return;
                }
            }

            openLoading();
            var formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('codigo', codigo);
            formData.append('unidad', unidad);

            axios.post(url+'/materiales/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Registrado correctamente');
                        $('#modalAgregar').modal('hide');
                        recargar();
                    }
                    else if(response.data.success === 3){

                        Swal.fire({
                            title: 'Material Repetido',
                            text: "El nuevo material con su unidad de medida ya se encuentra registrado",
                            icon: 'question',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            cancelButtonText: 'Cancelar',
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        });
                    }
                    else {
                        toastr.error('Error al registrar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al registrar');
                    closeLoading();
                });
        }

        function informacion(id){
            openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post(url+'/materiales/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal({backdrop: 'static', keyboard: false})

                        $('#id-editar').val(id);
                        $('#nombre-editar').val(response.data.material.nombre);
                        $('#codigo-editar').val(response.data.material.codigo);

                        contarcaracteresEditar();

                        document.getElementById("select-unidad-editar").options.length = 0;
                        $('#select-unidad-editar').append('<option value="" selected="selected">Seleccione una opción...</option>');

                        // unidad de medida
                        $.each(response.data.unidad, function( key, val ){
                            if(response.data.material.id_medida == val.id){
                                $('#select-unidad-editar').append('<option value="' +val.id +'" selected="selected">'+ val.medida +'</option>');
                            }else{
                                $('#select-unidad-editar').append('<option value="' +val.id +'">'+ val.medida +'</option>');
                            }
                        });

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
            var nombre = document.getElementById('nombre-editar').value;
            var codigo = document.getElementById('codigo-editar').value;
            var unidad = document.getElementById('select-unidad-editar').value; // nullable

            if(nombre === ''){
                toastr.error('Nombre es requerido');
                return;
            }

            if(nombre.length > 300){
                toastr.error('Nombre máximo 300 caracteres');
                return;
            }

            if(codigo === ''){

            }else{
                if(codigo.length > 100){
                    toastr.error('Código máximo 100 caracteres');
                    return;
                }
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('codigo', codigo);
            formData.append('unidad', unidad);

            axios.post(url+'/materiales/editar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Actualizado correctamente');
                        $('#modalEditar').modal('hide');
                        recargar();
                    }
                    else if(response.data.success === 3){

                        Swal.fire({
                            title: 'Material Repetido',
                            text: "El material a editar con su unidad de medida ya se encuentra registrado",
                            icon: 'question',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            cancelButtonText: 'Cancelar',
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        });
                    }
                    else {
                        toastr.error('Error al registrar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al registrar');
                    closeLoading();
                });
        }

        function contarcaracteresIngreso(){
            setTimeout(function(){
                var valor = document.getElementById('nombre-nuevo');
                var cantidad = valor.value.length;
                document.getElementById('res-caracter-nuevo').innerHTML = cantidad + '/300 ';
            },10);
        }

        function contarcaracteresEditar(){
            setTimeout(function(){
                var valor = document.getElementById('nombre-editar');
                var cantidad = valor.value.length;
                document.getElementById('res-caracter-editar').innerHTML = cantidad + '/300 ';
            },10);
        }


    </script>


@endsection
