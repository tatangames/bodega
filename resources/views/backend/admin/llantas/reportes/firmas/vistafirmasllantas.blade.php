@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />

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

            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Firmas</li>
                    <li class="breadcrumb-item active">Ajuste</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid" style="margin-left: 15px">
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-green">
                        <div class="card-header">
                            <h3 class="card-title">Ajuste de Firmas</h3>
                        </div>
                        <form>
                            <div class="card-body">

                                <labe>Firma 1</labe>

                                <div class="form-group">
                                    <input type="text" maxlength="100" class="form-control" value="{{ $lista->nombre_1 }}" id="nombre1" placeholder="Nombre">
                                </div>

                                <div class="form-group">
                                    <input type="text" maxlength="100" class="form-control" value="{{ $lista->nombre_2 }}" id="nombre2" placeholder="Nombre">
                                </div>


                                <hr>

                                <labe>Firma 2</labe>

                                <div class="form-group">
                                    <input type="text" maxlength="100" class="form-control" value="{{ $lista->nombre_3 }}" id="nombre3" placeholder="Nombre">
                                </div>


                                <div class="form-group">
                                    <input type="text" maxlength="100" class="form-control" value="{{ $lista->nombre_4 }}" id="nombre4" placeholder="Nombre">
                                </div>

                                <hr>

                                <labe>Firma 3</labe>

                                <div class="form-group">
                                    <input type="text" maxlength="100" class="form-control" value="{{ $lista->nombre_5 }}" id="nombre5" placeholder="Nombre">
                                </div>

                                <div class="form-group">
                                    <input type="text" maxlength="100" class="form-control" value="{{ $lista->nombre_6 }}" id="nombre6" placeholder="Nombre">
                                </div>


                                <label style="font-weight: bold">Distancia en PX</label>

                                <div class="form-group">
                                    <input type="number" class="form-control" value="{{ $lista->distancia }}" id="distancia" placeholder="0">
                                </div>

                                <label style="font-weight: bold">Distancia Bloque 2 en PX</label>

                                <div class="form-group">
                                    <input type="number" class="form-control" value="{{ $lista->distancia2 }}" id="distancia2" placeholder="0">
                                </div>

                                <div class="form-group">
                                    <label style="font-weight: bold">Salto de Página para Bloque de Firmas</label> <br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="toggle-editar">
                                        <div class="slider round">
                                            <span class="on">Si</span>
                                            <span class="off">No</span>
                                        </div>
                                    </label>
                                </div>


                            </div>

                            <div class="card-footer" style="float: right;">
                                <button type="button" class="btn btn-success" onclick="actualizar()">Actualizar</button>
                            </div>
                        </form>
                    </div>

                </div>

            </div>
        </div>
    </section>


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
            var ruta = "{{ URL::to('/admin/ajuste/firmallanta/tabla/index') }}";
            $('#tablaDatatable').load(ruta);

            let dato = {{ $lista->saltopagina }};
            if(dato === 0){
                $("#toggle-editar").prop("checked", false);
            }else{
                $("#toggle-editar").prop("checked", true);
            }

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/ajuste/firmallanta/tabla/index') }}";
            $('#tablaDatatable').load(ruta);
        }

        function modalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        function actualizar(){

            var nombre1 = document.getElementById('nombre1').value;
            var nombre2 = document.getElementById('nombre2').value;

            var nombre3 = document.getElementById('nombre3').value;
            var nombre4 = document.getElementById('nombre4').value;

            var nombre5 = document.getElementById('nombre5').value;
            var nombre6 = document.getElementById('nombre6').value;

            var distancia = document.getElementById('distancia').value;
            var distancia2 = document.getElementById('distancia2').value;

            var t = document.getElementById('toggle-editar').checked;
            var toggle = t ? 1 : 0;


            if(nombre1 === ''){
                toastr.error('Firma 1: nombre es requerido');
                return;
            }

            if(nombre2 === ''){
                toastr.error('Firma 1: nombre es requerido');
                return;
            }

            if(nombre3 === ''){
                toastr.error('Firma 2: nombre es requerido');
                return;
            }

            if(nombre4 === ''){
                toastr.error('Firma 2: nombre es requerido');
                return;
            }

            if(nombre5 === ''){
                toastr.error('Firma 3: nombre es requerido');
                return;
            }

            if(nombre6 === ''){
                toastr.error('Firma 3: nombre es requerido');
                return;
            }

            if(distancia === ''){
                toastr.error('Distancia es Requerida');
                return;
            }

            if(distancia2 === ''){
                toastr.error('Distancia bloque 2 es Requerida');
                return;
            }

            var reglaNumeroEntero = /^[0-9]\d*$/;

            if(!distancia.match(reglaNumeroEntero)) {
                toastr.error('Distancia debe ser número Entero y no Negativo');
                return;
            }

            if(distancia < 0){
                toastr.error('Distancia no puede ser negativo');
                return;
            }

            if(distancia > 1000){
                toastr.error('Distancia máximo 1000 PX');
                return;
            }



            if(!distancia2.match(reglaNumeroEntero)) {
                toastr.error('Distancia bloque 2 debe ser número Entero y no Negativo');
                return;
            }

            if(distancia2 < 0){
                toastr.error('Distancia bloque 2 no puede ser negativo');
                return;
            }

            if(distancia2 > 1000){
                toastr.error('Distancia bloque 2 máximo 1000 PX');
                return;
            }


            openLoading();
            var formData = new FormData();
            formData.append('nombre1', nombre1);
            formData.append('nombre2', nombre2);
            formData.append('nombre3', nombre3);
            formData.append('nombre4', nombre4);
            formData.append('nombre5', nombre5);
            formData.append('nombre6', nombre6);
            formData.append('distancia', distancia);
            formData.append('distancia2', distancia2);
            formData.append('toggle', toggle);

            axios.post(url+'/ajuste/firmallanta/editar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastr.success('Actualizado correctamente');
                        $('#modalEditar').modal('hide');
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
