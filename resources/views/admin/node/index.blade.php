@extends('admin.layout.iframe_main')

@section('content')

<nav class="breadcrumb">
    <i class="Hui-iconfont">&#xe67f;</i> 首页
    <span class="c-gray en">&gt;</span> 权限管理
    <a class="btn btn-success radius r  refresh-btn" style="line-height:1.6em;margin-top:3px" title="刷新">
        <i class="Hui-iconfont">&#xe68f;</i>
    </a>
</nav>
<div class="page-container">
    <div class="cl pd-5 bg-1 bk-gray">
		<span class="l">
            @if($level > 1)
            <a class="btn btn-primary radius" href="javascript:history.go(-1);">返回</a>
            @endif
            <a href="javascript:;" onclick="datadel()"
                   class="btn btn-danger radius"><i
                            class="Hui-iconfont">&#xe6e2;</i> 批量删除</a>
            <a class="btn btn-primary radius" href="javascript:;"
               onclick="addMenu('添加菜单', '{{route('permission.create',['pid' => $pid])}}', '800')">
                <i class="Hui-iconfont">&#xe600;</i> 添加菜单
            </a>

		</span>
    </div>
    <table class="table table-border table-bordered table-bg  table-hover radius">
        <thead>
        <tr class="text-c">
            <th width="25"><input type="checkbox" name="" value=""></th>
            <th>ID</th>
            <th>栏目名</th>
            <th>路由</th>
            <th>是否为目录</th>
            <th>创建时间</th>
            <th>更新时间</th>
            <th>排序</th>
            <th>状态</th>
            <th width="300px">操作</th>
        </tr>
        </thead>
        <tbody>
            @forelse($result as $row)
            <tr class="text-c">
                <td><input type="checkbox" value="{{$row->id}}" name="ids"></td>
                <td class="id">{{$row->id}}</td>
                <td><a class="btn btn-link" href="{{route('permission.lists',['parent_id' => $row->id])}}">{{$row->title}}</a></td>
                <td>{{$row->path}}</td>
                <td>{{trans('common.is_'.$row->is_menu)}}</td>
                <td>{{$row->created_at}}</td>
                <td>{{$row->updated_at}}</td>
                <td>
                    <input class="input-text set-sort" type="number" style="width: 50px;text-align: center" value="{{$row->sort}}" autocomplete="off">
                </td>
                <td>
                    <span class="label @if($row->status == 1) label-success @else label-danger @endif radius">
                    {{trans('common.status_'.$row->status)}}
                    </span>
                </td>
                <td class="f-14">
                    @if($row->status == 1)
                        <a class="set-btn" style="text-decoration:none" data-status="0" href="javascript:;"><i class="Hui-iconfont">&#xe631;</i></a>
                    @else
                        <a class="set-btn"  style="text-decoration:none" data-status="1" href="javascript:;"><i class="Hui-iconfont">&#xe615;</i></a>
                    @endif
                    <a title="编辑" href="javascript:;"
                       onclick="node_edit('权限编辑','{{route('permission.edit',['id' => $row->id])}}','800','500')"
                       class="ml-5"
                       style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
                    <a title="删除" href="javascript:;" onclick="node_del(this,'{{$row->id}}')" class="ml-5"
                       style="text-decoration:none">
                        <i class="Hui-iconfont">&#xe6e2;</i></a>
                </td>
            </tr>
                @empty
            <tr>
                <td colspan="100" style="color: red;text-align: center">无数据</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    {{$result->appends($requestData)->links()}}
</div>
@endsection
@section('script')
<script type="text/javascript">

    //  添加菜单
    function addMenu(title, url, weight, hight) {
        layer_show(title, url, weight, hight);
    }

    /*菜单-编辑*/
    function node_edit(title, url, w, h) {
        layer_show(title, url, w, h);
    }

    $(function () {
        // 设置状态
        $('.set-btn').click(function () {
            var status = $(this).data('status');
            var col_id = $(this).parents('tr').find('.id').text();
            var msg = '';
            if(status == 1){
                msg = '确认要启用吗？';
            }else {
                msg = '确认要禁用吗？';
            }
            layer.confirm(msg, function (index) {
                setField('status', status, col_id);
            });
            return true;
        })

        $('.set-sort').change(function () {
            var col_id = $(this).parents('tr').find('.id').text();
            setField('sort',$(this).val(),col_id);
            return true;
        });
    });

    // 设置单项属性
    function setField(field, value, col_id) {
        $.ajax({
            url: '{{route('permission.updateField')}}',
            data: {
                'field': field,
                'value': value,
                'id': col_id
            },
            dataType: 'json',
            type: 'post',
            success: function (data) {
                console.log(data);
                if (data.status == 'error') {
                    layer.msg(data.error.message, {icon: 2, time: 1500});
                    return false;
                }
                layer.msg('修改成功!',{icon:1,time:1000});
                location.reload();
                return true;
            }
        });
    }

    /*管理员-删除*/
    function node_del(obj, id) {
        layer.confirm('确认要删除吗？', function (index) {
            $.ajax({
                type: 'POST',
                url: '{{route('permission.destroy')}}',
                data: {
                    'id': id
                },
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    if (data.status == 'error') {
                        layer.msg(data.error.message, {icon: 2, time: 1500});
                        return false;
                    }
                    $(obj).parents("tr").remove();
                    layer.msg('已删除!', {icon: 1, time: 1000});
                }
            });
        });
    }

    // 批量删除
    function datadel() {
        var id_arr = new Array();
        $('input:checkbox[name=ids]:checked').each(function (i) {
            id_arr[i] = $(this).val();
        });
        if (id_arr.length === 0) {
            layer.msg('未选中数据', {icon: 2, time: 1500});
            return false;
        }
        layer.confirm('确认要删除吗？', function () {
            $.ajax({
                type: 'POST',
                url: '{{route('permission.destroy')}}',
                data: {
                    'id': id_arr
                },
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    if (data.status == 'error') {
                        layer.msg(data.error.message, {icon: 2, time: 1500});
                        return false;
                    }
                    layer.msg('已删除!', {icon: 1, time: 1000});
                    location.reload();
                }
            });
        });
    }

</script>
@endsection
