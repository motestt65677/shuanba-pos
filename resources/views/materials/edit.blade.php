@extends('layouts.app')

@section('custom_css')
<style>
    /* tr{
        border: 1px solid black;
    } */
</style>
@endsection
@section('content')
<h3 class="ui block header">編輯材料</h3>
<div class="ui form">
    <div style="text-align:right;">
        <button id="submit" class="ui button primary submit">儲存</button>
    </div>
    <div class=" fields">
        <div class="eight wide field disabled">
            <label>材料編號</label>
            <input id="material_no" type="text" value="">
        </div>
        <div class="eight wide field">
            <label>廠商</label>
            <select id="supplier"></select>
        </div>
    </div>

    <div class=" fields">
        <div class="eight wide field">
            <label>材料名稱</label>
            <input id="material_name" type="text" value="" placeholder="七星鱸魚片">
        </div>
        <div class="eight wide field">
            <label>材料單位</label>
            <input id="material_unit" type="text" value="" placeholder="個, 40/箱, 3kg">
        </div>
        <div class="eight wide field">
            <label>預設單價</label>
            <input id="material_unit_price" type="text" value="" placeholder="50, 8.5">
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
    $('.ui.form').form.settings.prompt.number = "{name}應為數字";
    $('.ui.form').form({
        inline : true,
        fields: {
            material_name: 'empty',
            material_name: 'empty',
            material_unit_price: ['empty', 'number']
        }
    });

    // const supplier_id = 

    
    $.ajax({
        type: "POST",
        url: "/materials/queryData",
        contentType: "application/json",
        dataType: "json",
        beforeSend: showLoading,
        complete: hideLoading,
        data: JSON.stringify({"search": {"material_id": get_url_id()}}),
        success: function(response) {
            if(response["data"].length > 0){
                const material = response["data"][0];
                $("#supplier").dropdown("set selected", material["supplier_id"])
                $("#material_name").val(material["material_name"]);
                $("#material_unit").val(material["material_unit"]);
                $("#material_unit_price").val(material["material_unit_price"]);
                $("#material_no").val(material["material_no"]);
            }
        },
        error: function(response) {
            // console.log(response);
        }
    });

    bind_supplier_select();
    $('#supplier').dropdown();

    $("#submit").click(function(){
        if( $('.ui.form').form('is valid')) {
            let data = {
                "material_id": get_url_id(),
                "supplier_id": $("#supplier").val(),
                "material_name": $("#material_name").val(),
                "material_unit": $("#material_unit").val(),
                "material_unit_price": $("#material_unit_price").val(),
            };

            $.ajax({
                type: "POST",
                url: "/materials/update",
                contentType: "application/json",
                dataType: "json",
                beforeSend: showLoading,
                complete: hideLoading,
                data: JSON.stringify(data),
                success: function(response) {
                    window.location.href = "/materials/index";
                },
                error: function(response) {
                    // console.log(response);
                }
            });
        }
    })

    function bind_supplier_select(){
        $.ajax({
            type: "POST",
            url: "/suppliers/queryData",
            contentType: "application/json",
            dataType: "json",
            beforeSend: showLoading,
            complete: hideLoading,
            // data: JSON.stringify(data),
            success: function(response) {
                const data = response["data"];
                let select = document.getElementById('supplier');
                select.innerHTML = "";
                for(let i = 0; i < data.length; i++){
                    let this_supplier = data[i];
                    let option = document.createElement('option');
                    option.value = this_supplier.supplier_id;
                    option.innerHTML = this_supplier.supplier_no + '(' + this_supplier.supplier_name + ')';
                    select.appendChild(option);
                }
            },
            error: function(response) {
                // console.log(response);
            }
        });
    }
});




    

</script>
@endsection