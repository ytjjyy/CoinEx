<div class="btn-group pull-right" style="margin-right: 10px">
    <a href="" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#filter-modal"><i
                class="fa fa-filter"></i>&nbsp;&nbsp;筛选</a>
    <a href="{{  $uri }}?_pjax=%23pjax-container" class="btn btn-sm btn-facebook"><i class="fa fa-undo"></i>&nbsp;&nbsp;重置</a>
</div>
<div class="modal fade in" id="filter-modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false"
>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">筛选</h4>
            </div>
            <form action="{{ $uri }}" method="get"
                  pjax-container="" id="form">
                <div class="modal-body">
                    <div class="form">
                        {!! $html !!}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary submit">提交</button>
                    {{--<button type="reset" class="btn btn-warning pull-left">重置</button>--}}
                    <a href="javascript:;" class="btn btn-warning pull-left rest"><i class="fa fa-undo"></i>&nbsp;&nbsp;重置</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function jsClearForm(objForm){
        if(objForm == undefined)
        {
            return;
        }

        for(var i=0; i<objForm.elements.length; i++)
        {
            if(objForm.elements[i].type == "text")
            {
                objForm.elements[i].value = "";
            }
            else if(objForm.elements[i].type == "password")
            {
                objForm.elements[i].value = "";
            }
            else if(objForm.elements[i].type == "radio")
            {
                objForm.elements[i].checked = false;
            }
            else if(objForm.elements[i].type == "checkbox")
            {
                objForm.elements[i].checked = false;
            }
            else if(objForm.elements[i].type == "select-one")
            {
                objForm.elements[i].options[0].selected = true;
            }
            else if(objForm.elements[i].type == "select-multiple")
            {
                for(var j = 0; j < objForm.elements[i].options.length; j++)
                {
                    objForm.elements[i].options[j].selected = false;
                }
            }
            else if(objForm.elements[i].type == "file")
            {
                //formObj.elements[i].select();
                //document.selection.clear();
                // for IE, Opera, Safari, Chrome
                var file = objForm.elements[i];
                if (file.outerHTML) {
                    file.outerHTML = file.outerHTML;
                } else {
                    file.value = "";  // FF(包括3.5)
                }
            }
            else if(objForm.elements[i].type == "textarea")
            {
                objForm.elements[i].value = "";
            }
        }
    }
    var objForm = document.getElementById("form");
    jsClearForm(objForm);
    $(".submit").click(function () {
        $('#filter-modal').modal('hide')
        $(".modal-backdrop").hide();
    });
    $(".rest").click(function () {
        $('#filter-modal').modal('hide')
        $(".modal-backdrop").hide();
        window.location.href = " {{  $uri }}" + "?_pjax=%23pjax-container";
    });
</script>