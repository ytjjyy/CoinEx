<div class="box">
    <div class="box-header">

        <h3 class="box-title"></h3>

        <div class="pull-right">
            {!! $grid->renderFilter() !!}
            {!! $grid->renderExportButton() !!}
            {!! $grid->renderCreateButton() !!}
        </div>

        <span>
            {!! $grid->renderHeaderTools() !!}
        </span>

    </div>
    <!-- /.box-header -->
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover">
            <tr>
                @foreach($grid->columns() as $column)
                    <th>{{$column->getLabel()}}{!! $column->sorter() !!}</th>
                @endforeach
            </tr>

            @foreach($grid->rows() as $row)
                    <tr {!! $row->getRowAttributes() !!} >
                        @foreach($grid->columnNames as $name)
                            <td {!! $row->getColumnAttributes($name) !!} >
                                {!! $row->column($name) !!}
                            </td>
                        @endforeach
                    </tr>
            @endforeach

            {!! $grid->renderFooter() !!}

        </table>
    </div>
    <div class="box-footer clearfix">
        {!! $grid->paginator() !!}
    </div>
    <!-- /.box-body -->
</div>
<script>
    {{--var is_reload = "{{ $grid->is_reload }}";--}}
    {{--if (is_reload) {--}}
        {{--setInterval(function () {--}}
            {{--$.get('/admin/grid', function (res) {--}}
                {{--$(".col-md-12").empty().append(res);--}}
            {{--});--}}
        {{--}, 1000);--}}
    {{--}--}}

</script>
