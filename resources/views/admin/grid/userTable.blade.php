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
<div class="modal fade" id="userGroup" tabindex="-1" role="dialog" aria-labelledby="userGroup">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">分组</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="exampleInputEmail1">请选择分组</label>
                    <select name="coin" id="group" class="form-control" class="coinName">
                        <option value="">请选择</option>

                        @if(getGroup())
                            @foreach(getGroup() as $key=>$item)
                                <option value="{{ $key }}">{{ $item }}</option>
                            @endforeach
                        @endif;
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary setGroup">确认</button>
            </div>
        </div>
    </div>
</div>
<script>
    $('.setGroup').click(function () {
        let selected = [];
        $('.grid-row-checkbox:checked').each(function(){
            selected.push($(this).data('id'));
        });
        var group =$('#group').val();
        $("#userGroup").modal('hide');
        swal({
            title: "你确定要执行该操作吗?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "确认",
            closeOnConfirm: false,
            cancelButtonText: "取消"
        },function(){
            $.ajax({
                method: 'post',
                url: '/admin/setUserGroup',
                data: {
                    _token:LA.token,
                    id: selected,
                    group:group,
                },
                success: function (res) {
                    $.pjax.reload('#pjax-container');
                    if(res.status==true){
                        swal('操作成功', '', 'success');
                    }else{
                        swal('操作失败', '', 'error');
                    }
                }
            });
        })
    });

</script>
