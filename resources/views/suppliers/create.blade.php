@extends('layouts.app')

@section('custom_css')
<style>
    /* tr{
        border: 1px solid black;
    } */
</style>
@endsection
@section('content')
<h3 class="ui block header">新增廠商</h3>

    <div id="this_form" class="ui form">

        <div style="text-align:right;">
            <a class="ui button" href="/suppliers/index">
                <i class="left chevron icon"></i>
                返回
            </a>
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
    hideLoading();

    //validation setting
    $('#this_form').form.settings.prompt.empty = "請填寫{name}";
    $('#this_form').form({
        inline : true,
        fields: {
            supplier_name: 'empty'
        }
    });

    $("#submit").click(function(){
        if( $('#this_form').form('is valid')) {
            let data = {
                "supplier_name": $("#supplier_name").val(),
                "supplier_phone": $("#supplier_phone").val(),
                "supplier_cellphone": $("#supplier_cellphone").val(),
                "supplier_address": $("#supplier_address").val(),
                "supplier_note1": $("#supplier_note1").val(),
                "supplier_tax_id": $("#supplier_tax_id").val(),
            };
            $.ajax({
                type: "POST",
                url: "/suppliers/store",
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