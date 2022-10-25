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
            <h1 style="margin-left: 5px">Catálogo de Aceites y Lubricantes</h1><br>
            @can('btn.registros.repuestos.material.nuevo')
                <button type="button" style="margin-left: 15px" onclick="modalAgregar()" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-square"></i>
                    Nuevo Aceite o Lubricante
                </button>
            @endcan
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Listado de Aceites y Lubricantes</h3>
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
                    <h4 class="modal-title">Nuevo Ingreso</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo">
                        <div class="card-body">

                            <div class="form-group">
                                <label>Viscosidad:</label>
                                <input type="text" class="form-control" autocomplete="off" onpaste="contarcaracteresIngreso();" onkeyup="contarcaracteresIngreso();" maxlength="300" id="nombre-nuevo" placeholder="Nombre del material">
                                <div id="res-caracter-nuevo" style="float: right">0/300</div>
                            </div>

                            <br>

                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Tipo:</label>
                                    <br>
                                    <select width="60%"  class="form-control" id="select-tipo-nuevo">
                                        <option value="1" selected>Aceite</option>
                                        <option value="2">Lubricante</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Unidad de Medida:</label>
                                    <br>
                                    <select width="60%"  class="form-control" id="select-unidad-nuevo">
                                        @foreach($lUnidad as $sel)
                                            <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
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
                                        <label>Viscosidad:</label>
                                        <input type="text" class="form-control" autocomplete="off" onpaste="contarcaracteresEditar();" onkeyup="contarcaracteresEditar();" maxlength="300" id="nombre-editar" placeholder="Nombre del material">
                                        <div id="res-caracter-editar" style="float: right">0/300</div>
                                    </div>

                                    <br>

                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Tipo:</label>
                                            <br>
                                            <select width="60%"  class="form-control" id="select-tipo-editar">
                                                <option value="1" selected>Aceite</option>
                                                <option value="2">Lubricante</option>
                                            </select>
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
            var ruta = "{{ URL::to('/admin/catalogo/aceiteylubricantes/tabla/index') }}";
            $('#tablaDatatable').load(ruta);

            $('#select-unidad-nuevo').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Búsqueda no encontrada";
                    }
                },
            });

            $('#select-unidad-editar').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Búsqueda no encontrada";
                    }
                },
            });

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/catalogo/aceiteylubricantes/tabla/index') }}";
            $('#tablaDatatable').load(ruta);
        }

        function modalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            document.getElementById('res-caracter-nuevo').innerHTML = '0/300 ';

            $('#select-unidad-nuevo').prop('selectedIndex', 0).change();

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
                    nuevoRegistro();
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

        function nuevoRegistro(){

            var nombre = document.getElementById('nombre-nuevo').value;
            var unidad = document.getElementById('select-unidad-nuevo').value;
            var tipo = document.getElementById('select-tipo-nuevo').value;

            if(nombre === ''){
                toastr.error('Viscosidad es requerido');
                return;
            }

            if(nombre.length > 300){
                toastr.error('Viscosidad máximo 300 caracteres');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('unidad', unidad);
            formData.append('tipo', tipo);

            axios.post(url+'/catalogo/aceiteylubricantes/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        Swal.fire({
                            title: 'Material Repetido',
                            text: "El nuevo material, con su unidad de medida y Tipo ya se encuentra registrado",
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
                    else if(response.data.success === 2){
                        toastr.success('Registrado correctamente');
                        $('#modalAgregar').modal('hide');
                        recargar();
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

            axios.post(url+'/catalogo/aceiteylubricantes/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal({backdrop: 'static', keyboard: false})

                        $('#id-editar').val(id);
                        $('#nombre-editar').val(response.data.material.nombre);

                        contarcaracteresEditar();

                        document.getElementById("select-unidad-editar").options.length = 0;

                        // unidad de medida
                        $.each(response.data.unidad, function( key, val ){
                            if(response.data.material.id_medida == val.id){
                                $('#select-unidad-editar').append('<option value="' +val.id +'" selected="selected">'+ val.nombre +'</option>');
                            }else{
                                $('#select-unidad-editar').append('<option value="' +val.id +'">'+ val.nombre +'</option>');
                            }
                        });

                        if(response.data.material.tipo === 1){
                            // aceites
                            $('#select-tipo-editar').prop('selectedIndex', 0).change();
                        }else{
                            $('#select-tipo-editar').prop('selectedIndex', 1).change();
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
            var nombre = document.getElementById('nombre-editar').value;
            var unidad = document.getElementById('select-unidad-editar').value;
            var tipo = document.getElementById('select-tipo-editar').value;

            if(nombre === ''){
                toastr.error('Viscosidad es requerido');
                return;
            }

            if(nombre.length > 300){
                toastr.error('Viscosidad máximo 300 caracteres');
                return;
            }




            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('unidad', unidad);
            formData.append('tipo', tipo);

            axios.post(url+'/catalogo/aceiteylubricantes/editar', formData, {
            })
                .then((response) => {
                    closeLoading();
                   if(response.data.success === 1){

                        Swal.fire({
                            title: 'Material Repetido',
                            text: "El materia a Actualizar, con su unidad de medida y Tipo ya se encuentra registrado uno similar",
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
                   else if(response.data.success === 2){
                       toastr.success('Actualizado correctamente');
                       $('#modalEditar').modal('hide');
                       recargar();
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

        // mostrara que materiales quedan aun
        function infoDetalle(id){
            window.location.href="{{ url('/admin/detalle/material/cantidad') }}/" + id;
        }

    </script>


@endsection
