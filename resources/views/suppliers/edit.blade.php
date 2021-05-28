@extends('layouts.app')

@section('custom_css')
<style>
    /* tr{
        border: 1px solid black;
    } */
</style>
@endsection
@section('content')
<h3 class="ui block header">編輯廠商</h3>

    <div class="ui form">
        <div style="text-align:right;">
            <button id="submit" class="ui button primary submit">儲存</button>
        </div>
        <div class=" fields">
            <div class="eight wide field disabled">
                <label>廠商編號</label>
                <input type="text" value="AUTONUM">
            </div>
            <div class="eight wide field">
                <label>廠商名稱</label>
                <input id="supplier_name" type="text">
            </div>
        </div>
        
        <div class=" fields">
            <div class="six wide field">
                <label>電話</label>
                <input id="supplier_phone" type="text" value="">
            </div>
            <div class="six wide field">
                <label>手機</label>
                <input id="supplier_cellphone" type="text" value="">
            </div>
            <div class="six wide field">
                <label>統一編號</label>
                <input id="supplier_tax_id" type="text" value="">
            </div>

        </div>
       
        <div class="fields">
            <div class="sixteen wide field">
                <label>地址</label>
                <input id="supplier_address" type="text" >
            </div>
        </div>
        <div class=" fields">
            <div class="sixteen wide field">
                <label>備註一</label>
                <input id="supplier_note1" type="text" >
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
    //validation setting
    $('.ui.form').form.settings.prompt.empty = "請填寫{name}";
    $('.ui.form').form({
        inline : true,
        fields: {
            supplier_name: 'empty'
        }
    });

    // const supplier_id = 

    
    $.ajax({
        type: "POST",
        url: "/suppliers/queryData",
        contentType: "application/json",
        dataType: "json",
        beforeSend: showLoading,
        complete: hideLoading,
        data: JSON.stringify({"search": {"supplier_id": get_url_id()}}),
        success: function(response) {
            if(response["data"].length > 0){
                const supplier = response["data"][0]
                $("#supplier_name").val(supplier["supplier_name"]);
                $("#supplier_phone").val(supplier["supplier_phone"]);
                $("#supplier_cellphone").val(supplier["supplier_cellphone"]);
                $("#supplier_address").val(supplier["supplier_address"]);
                $("#supplier_note1").val(supplier["supplier_note1"]);
                $("#supplier_tax_id").val(supplier["supplier_tax_id"]);
            }

            // window.location.href = "/suppliers/index";
        },
        error: function(response) {
            // console.log(response);
        }
    });

    $("#submit").click(function(){
        if( $('.ui.form').form('is valid')) {
            let data = {
                "supplier_id": get_url_id(),
                "supplier_name": $("#supplier_name").val(),
                "supplier_phone": $("#supplier_phone").val(),
                "supplier_cellphone": $("#supplier_cellphone").val(),
                "supplier_address": $("#supplier_address").val(),
                "supplier_note1": $("#supplier_note1").val(),
                "supplier_tax_id": $("#supplier_tax_id").val(),
            };
            $.ajax({
                type: "POST",
                url: "/suppliers/update",
                contentType: "application/json",
                dataType: "json",
                beforeSend: showLoading,
                complete: hideLoading,
                data: JSON.stringify(data),
                success: function(response) {
                    window.location.href = "/suppliers/index";
                },
                error: function(response) {
                    // console.log(response);
                }
            });
        }
    })
});




    

</script>
@endsection