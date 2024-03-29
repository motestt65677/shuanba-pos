@extends('layouts.app')

@section('custom_css')
<style>
    /* tr{
        border: 1px solid black;
    } */
    .label{
        display: block;
        margin: 0em 0em 0.28571429rem 0em;
        color: rgba(0, 0, 0, 0.87);
        font-size: 0.92857143em;
        font-weight: bold;
        text-transform: none;
    }
</style>
@endsection
@section('content')
<h3 class="ui block header">編輯帳號</h3>
    <div id="this_form" class="ui form">
        <div style="text-align:right;">
            <a class="ui button" href="/users/index">
                <i class="left chevron icon"></i>
                返回
            </a>
            <button id="submit" class="ui button primary submit">儲存</button>
        </div>
        <div class=" fields">
            <div class="eight wide field">
                <label>姓名</label>
                <input id="name" type="text" value="{{$user->name}}">
            </div>
        </div>
        <div class="eight wide field">
            <label>分公司</label>
            <select id="branch"></select>
        </div>
        <div class="ui relaxed list">
            <div class="label">權限</div>
            <div class="item">
                <div class="ui master checkbox">
                <input type="checkbox" name="fruits">
                <label>全選</label>
                </div>
                <div id="roles" class="list">
                    @foreach($roles as $role)
                    <div class="item">
                        <div class="ui child checkbox">
                            <input type="checkbox" name="{{$role->id}}" @if(in_array($role->id, $userRoles)) checked @endif>
                            <label>{{$role->role}}</label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom_js')
<script>

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$(document).ready(function(){
    hideLoading();
    bind_branch_select();
    $('#branch').dropdown();

    //validation setting
    $('#this_form').form.settings.prompt.empty = "請填寫{name}";
    $('#this_form').form({
        inline : true,
        fields: {
            name: 'empty',
            branch: ['empty']
        }
    });

    $("#submit").click(function(){
        if( $('#this_form').form('is valid')) {


            let roles = [];
            $("#roles").find('input[type=checkbox]').each(function () {
                if(this.checked)
                    roles.push(this.name);
            });


            let data = {
                "user_id": get_url_id(),
                "name": $("#name").val(),
                "branch": $("#branch").val(),
                "roles": roles
            };
            
            
            $.ajax({
                type: "POST",
                url: "/users/update",
                contentType: "application/json",
                dataType: "json",
                beforeSend: showLoading,
                complete: hideLoading,
                data: JSON.stringify(data),
                success: function(response) {
                    if(response["status"] != "200"){
                        alert("系統錯誤");
                    }

                    if(response["error"].length > 0){
                        for(let i = 0; i < response["error"].length; i++){
                            alert(response["error"][i]);
                        }
                    } else {
                        alert('儲存成功');
                        // window.location.href = "/users/index";
                    }
                },
                error: function(response) {
                    // console.log(response);
                }
            });
        }
    })

    function bind_branch_select(){
        $.ajax({
            type: "POST",
            url: "/branches/queryData",
            contentType: "application/json",
            dataType: "json",
            beforeSend: showLoading,
            complete: hideLoading,
            // data: JSON.stringify(data),
            success: function(response) {
                const data = response["data"];
                let select = document.getElementById('branch');
                select.innerHTML = "";
                for(let i = 0; i < data.length; i++){
                    let this_item = data[i];
                    let option = document.createElement('option');
                    option.value = this_item.id;
                    option.innerHTML = this_item.name;
                    select.appendChild(option);
                }
            },
            error: function(response) {
                // console.log(response);
            }
        });
    }

    $('.list .master.checkbox').checkbox({
        // check all children
        onChecked: function() {
            $(this).closest('.checkbox').siblings('.list').find('.checkbox').checkbox('check');
        },
        // uncheck all children
        onUnchecked: function() {
            $childCheckbox  = $(this).closest('.checkbox').siblings('.list').find('.checkbox').checkbox('uncheck');
        }
    });
    
});
    


    

</script>
@endsection