@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/main.css') }}" type="text/css" rel="stylesheet" />
@stop


<div class="content-wrapper" id="divcc" style="display: none">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">

        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="callout callout-info">
                        <h5><i class="fas fa-info"></i> Generar Reporte Catálogo de Llantas</h5>
                        <div class="card">
                            <form class="form-horizontal">
                                <div class="card-body">

                                    <h6>Reporte</h6>

                                    <div class="form-group row">
                                        <div class="col-sm-7">
                                            <div class="info-box shadow">
                                                <button type="button" onclick="generarPdf()" class="btn" style="margin-left: 10px; border-color: black; border-radius: 0.1px;">
                                                    <img src="{{ asset('images/logopdf.png') }}" width="55px" height="55px">
                                                    Generar PDF
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
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
    <script src="{{ asset('js/jquery.simpleaccordion.js') }}"></script>

    <script>
        $(document).ready(function() {
            document.getElementById("divcc").style.display = "block";
        });

    </script>

    <script>

        function generarPdf() {
            window.open("{{ URL::to('admin/reporte/catalogo/llantas') }}");
        }

    </script>


@endsection
