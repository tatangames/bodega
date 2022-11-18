
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
        <label style="font-size: 17px">Fecha Salida de Bodega: {{ $fechasalida }}</label> <br>
        <label style="font-size: 17px">Viscosidad: {{ $viscosidad }}</label> <br>

        <button type="button" style="margin-left: 15px" onclick="modalAgregar()" class="btn btn-primary btn-sm">
            <i class="fas fa-plus-square"></i>
            Nuevo Detalle
        </button>

    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Detalle</h3>
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nueva Ubicación de Repuesto</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">


                                    <div class="form-group" >
                                        <label class="control-label">Fecha</label>
                                        <div class="col-md-6">
                                            <input type="date" class="form-control" id="fecha">
                                        </div>
                                    </div>

                                    <div class="form-group" >
                                        <label class="control-label">Hora (Opcional)</label>
                                        <div class="col-md-6">
                                            <input type="time" class="form-control" id="hora">
                                        </div>
                                    </div>

                                    <div class="form-group" >
                                        <label class="control-label">Seleccionar Equipo</label>
                                        <div class="col-md-12">
                                            <select id="select-equipo" class="form-control">
                                                @foreach($equipos as $item)
                                                    <option value="{{$item->id}}">{{ $item->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group" >
                                        <label class="control-label">Seleccionar Unidad de Medida</label>
                                        <div class="col-md-12">
                                            <select id="select-medida" class="form-control">
                                                @foreach($medidas as $item)
                                                    <option value="{{$item->id}}">{{ $item->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Cantidad</label>
                                        <input type="number" class="form-control" id="cantidad" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Descripción (opcional)</label>
                                        <input type="text" maxlength="800" class="form-control" id="descripcion" autocomplete="off">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="registrarSalida()">Guardar</button>
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
            let id = {{ $id }};
            var ruta = "{{ URL::to('/admin/aceiteylubricantes/detalle/usotabla') }}/" + id;
            $('#tablaDatatable').load(ruta);
            document.getElementById("divcontenedor").style.display = "block";
        });

        $('#select-equipo').select2({
            theme: "bootstrap-5",
            "language": {
                "noResults": function(){
                    return "Búsqueda no encontrada";
                }
            },
        });

        $('#select-medida').select2({
            theme: "bootstrap-5",
            "language": {
                "noResults": function(){
                    return "Búsqueda no encontrada";
                }
            },
        });

    </script>

    <script>

        function recargar(){
            let id = {{ $id }};
            var ruta = "{{ URL::to('/admin/aceiteylubricantes/detalle/usotabla') }}/" + id;
            $('#tablaDatatable').load(ruta);
        }

        function modalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        function registrarSalida(){

            Swal.fire({
                title: 'Registrar Salida?',
                text: "",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    guardar();
                }
            })
        }

        function guardar(){

            var fecha = document.getElementById('fecha').value;
            var hora = document.getElementById('hora').value;
            var equipo = document.getElementById('select-equipo').value;
            var medida = document.getElementById('select-medida').value;
            var cantidad = document.getElementById('cantidad').value;
            var descripcion = document.getElementById('descripcion').value; // max 800


            if(fecha === ''){
                toastr.error('Fecha es requerida');
                return;
            }

            if(equipo === ''){
                toastr.error('Equipo es requerida');
                return;
            }

            if(medida === ''){
                toastr.error('Medida es requerida');
                return;
            }

            if(descripcion === ''){

            }else{
                if(descripcion.length > 800){
                    toastr.error('Descripción máximo 800 caracteres');
                    return;
                }
            }

            var reglaNumeroDosDecimal = /^([0-9]+\.?[0-9]{0,2})$/;


            if (cantidad === '') {
                toastr.error('Cantidad es requerido');
                return;
            }

            if (!cantidad.match(reglaNumeroDosDecimal)) {
                toastr.error('Cantidad debe ser decimal y no negativo');
                return;
            }

            if (cantidad < 0) {
                toastr.error('Cantidad no debe ser negativo');
                return;
            }

            if (cantidad > 9000000) {
                toastr.error('Cantidad máximo 9 millones');
                return;
            }

            openLoading();

            let formData = new FormData();

            // ID SALIDA ACEITE DETALLE
            let id = {{ $id }};

            formData.append('fecha', fecha);
            formData.append('hora', hora);
            formData.append('equipo', equipo);
            formData.append('medida', medida);
            formData.append('cantidad', cantidad);
            formData.append('descripcion', descripcion);
            formData.append('id', id);

            axios.post(url+'/aceiteylubricantes/guardar/detalles', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastr.success('Registrado correctamente');
                        recargar();
                        $('#modalAgregar').modal('hide');
                    }
                    else{
                        toastr.error('Error al guardar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al guardar');
                    closeLoading();
                });

        }




    </script>


@endsection
