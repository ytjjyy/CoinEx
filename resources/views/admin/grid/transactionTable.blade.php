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
        <table class="table table-hover tradeManager">
            <tr>
                @foreach($grid->columns() as $column)
                    <th>{{$column->getLabel()}}{!! $column->sorter() !!}</th>
                @endforeach
            </tr>

            @foreach($grid->rows() as $row)
                @if($row->isRed == 1)
                    <tr {!! $row->getRowAttributes() !!} >
                        @foreach($grid->columnNames as $name)
                            <td {!! $row->getColumnAttributes($name) !!} @if($name!= '__row_selector__' && $name!='__actions__')style="color: red" @endif>
                                {!! $row->column($name) !!}
                            </td>
                        @endforeach
                    </tr>
                @else
                    <tr {!! $row->getRowAttributes() !!} >
                        @foreach($grid->columnNames as $name)
                            <td {!! $row->getColumnAttributes($name) !!} >
                                {!! $row->column($name) !!}
                            </td>
                        @endforeach
                    </tr>
                @endif
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
    var aa= null;
    var minute = "{{ $grid->minute }}"
    var is_reload = "{{ $grid->is_reload }}";
    if (is_reload) {
          aa = setInterval(function () {
            let pathname = window.location.pathname;
            if (pathname == '/admin/transactions') {
                let url = window.location.href;
                $('.modal-backdrop').hide();
                let param = '';
                if (url.indexOf('?') > 1) {
                    param = url.substring(url.indexOf('?'), url.length);
                }
                $.get('/admin/transactionsGrid' + param, function (res) {
                    if (pathname == '/admin/transactions') {
                        $(".tradeManager").empty().append(res);
                    }
                });
            }
        }, minute * 60000);
    }

</script>
