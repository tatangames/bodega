<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                                    <tr>
                                        <th style="width: 5%">Fecha</th>
                                        <th style="width: 10%">Factura</th>
                                        <th style="width: 12%">Tipo Ingreso</th>
                                        <th style="width: 20%">Descripción</th>
                                        <th style="width: 5%">Opciones</th>
                                    </tr>
                                </thead>
                            <tbody>

                            @foreach($lista as $dato)
                                <tr>
                                    <td>{{ $dato->fecha }}</td>
                                    <td>{{ $dato->factura }}</td>
                                    <td>
                                    @if($dato->inventario == 0)
                                        <span class="badge bg-transparent">Repuesto Nuevo</span>
                                    @else
                                        <span class="badge bg-warning">Repuesto de Inventario</span>
                                    @endif
                                    </td>
                                    <td>{{ $dato->descripcion }}</td>
                                    <td>
                                        <button type="button" class="btn btn-success btn-xs" onclick="informacion({{ $dato->id }})">
                                            <i class="fas fa-file" title="Detalle"></i>&nbsp; Detalle
                                        </button>

                                        @can('btn.historial.entrada.btn.editar')
                                        <br><br>
                                        <button type="button" class="btn btn-primary btn-xs" onclick="infoEditar({{ $dato->id }})">
                                            <i class="fas fa-edit" title="Editar"></i>&nbsp; Editar
                                        </button>
                                        @endcan

                                        @if($dato->documento != null)
                                            <br><br>
                                            <a class="btn btn-primary btn-xs" href="{{ url('/admin/entradas/documento/'.$dato->id) }}">
                                                <i class="fas fa-eye" title="Documento"></i> Ver Documento </a>

                                            @can('btn.historial.entrada.btn.borrardocumento')
                                                <br><br>
                                                <button type="button" class="btn btn-danger btn-xs" onclick="modalBorrarDoc({{ $dato->id }})">
                                                    <i class="fas fa-trash-alt" title="Borrar"></i>&nbsp; Borrar Documento
                                                </button>
                                            @endcan
                                        @else
                                            @can('btn.historial.entrada.btn.agregardocumento')
                                                <br><br>
                                                <button type="button" class="btn btn-success btn-xs" onclick="infoSubirDoc({{ $dato->id }})">
                                                    <i class="fas fa-upload" title="Cargar Documento"></i>&nbsp; Cargar Documento
                                                </button>
                                            @endcan
                                        @endif

                                        @can('btn.historial.entrada.btn.borrarregistro')
                                            @if($dato->btnbloqueo)
                                                <br><br>
                                                <button type="button" class="btn btn-danger btn-xs" onclick="informacionBorrarRegistro({{ $dato->id }})">
                                                    <i class="fas fa-trash-alt" title="Borrar"></i>&nbsp; Borrar Registro
                                                </button>
                                                @endcan
                                            @endif
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
    $(function () {
        $("#tabla").DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "pagingType": "full_numbers",
            "lengthMenu": [[10, 25, 50, 100, 150, -1], [10, 25, 50, 100, 150, "Todo"]],
            "language": {

                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }

            },
            "responsive": true, "lengthChange": true, "autoWidth": false,
        });
    });


</script>
