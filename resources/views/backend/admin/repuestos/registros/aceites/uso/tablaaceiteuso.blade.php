<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="width: 9%">Fecha Salida Bodega</th>
                                <th style="width: 13%">Licitación</th>
                                <th style="width: 13%">Viscosidad</th>
                                <th style="width: 10%">Cantidad</th>
                                <th style="width: 10%">Unidad Medida</th>
                                <th style="width: 11%">Tipo</th>
                                <th style="width: 14%">Descripción</th>
                                <th style="width: 15%">Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($lista as $dato)
                                <tr>
                                    <td>{{ $dato->fecha }}</td>
                                    <td>{{ $dato->empresa }}</td>
                                    <td>{{ $dato->viscosidad }}</td>
                                    <td>{{ $dato->cantidad }}</td>
                                    <td>{{ $dato->medida }}</td>
                                    @if($dato->tipo == 1)
                                        <td>Aceites</td>
                                    @else
                                        <td>Lubricantes</td>
                                    @endif
                                    <td>{{ $dato->descripcion }}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-xs" onclick="informacion({{ $dato->id }})">
                                            <i class="fas fa-edit" title="Detalle"></i>&nbsp; Detalle
                                        </button>


                                        <button type="button" class="btn btn-danger btn-xs" onclick="infoFinalizar({{ $dato->id }})">
                                            <i class="fas fa-check" title="Finalizar"></i>&nbsp; Finalizar
                                        </button>
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
