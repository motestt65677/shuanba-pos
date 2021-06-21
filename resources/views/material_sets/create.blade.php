@extends('layouts.app')

@section('custom_css')
<style>
    /* tr{
        border: 1px solid black;
    } */
</style>
@endsection
@section('content')
<h3 class="ui block header">新增進貨產品</h3>

    <div class="ui form">
        <div style="text-align:right;">
            <a class="ui button" href="/material_sets/index">
                <i class="left chevron icon"></i>
                返回
            </a>
            <button id="submit" class="ui button primary submit">儲存</button>
        </div>
        <div class=" fields">
            <div class="eight wide field">
                <label>廠商</label>
                <select id="supplier" class="ui search selection dropdown"></select>
            </div>
            <div class="eight wide field">
                <label>材料</label>
                <select id="material" class="ui search selection dropdown"></select>
            </div>

        </div>

        <div class=" fields">
            <div class="eight wide field">
                <label>產品名稱</label>
                <input id="set_name" type="text" value="" placeholder="七星鱸魚片一箱">
            </div>
            <div class="eight wide field">
                <label>產品單價</label>
                <input id="set_unit_price" type="text" value="" placeholder="450, 880">
            </div>
            <div class="eight wide field">
                <label>內含材料數量</label>
                <input id="material_count" type="text" value="" placeholder="50, 8.5">
            </div>
        </div>
        <div class=" fields">
            <div class="eight wide field disabled">
                <label>庫存單位</label>
                <input id="material_unit" type="text" value="" readonly>
            </div>
            <div class="eight wide field disabled">
                <label>材料單價</label>
                <input id="material_unit_price" type="text" value="0" readonly>
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
            supplier: 'empty',
            material: 'empty',
            set_name: 'empty',
            set_unit_price: ['empty', 'number'],
            material_count: ['empty', 'number']
        }
    });

    bind_supplier_select();
    $('#supplier').dropdown({fullTextSearch: true});
    $('#material').dropdown({fullTextSearch: true});

    $("#submit").click(function(){
        if( $('.ui.form').form('is valid')) {
            let data = {
                "supplier_id": $("#supplier").val(),
                "material_id": $("#material").val(),
                "set_name": $("#set_name").val(),
                "set_unit_price": $("#set_unit_price").val(),
                "material_count": $("#material_count").val(),
            };

            $.ajax({
                type: "POST",
                url: "/material_sets/store",
                contentType: "application/json",
                dataType: "json",
                beforeSend: showLoading,
                complete: hideLoading,
                data: JSON.stringify(data),
                success: function(response) {
                    window.location.href = "/material_sets/index";
                },
                error: function(response) {
                    // console.log(response);
                }
            });
        }
    })

    $("#supplier").change(function(){
        bind_material_select(update_material_unit);
    })
    $("#material").change(update_material_unit);
    $("#set_unit_price, #material_count").change(update_material_unit_price);

});


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
                option.innerHTML = this_supplier.supplier_name_and_no;
                select.appendChild(option);
            }
            bind_material_select(update_material_unit);
        },
        error: function(response) {
            // console.log(response);
        }
    });
}

function bind_material_select(callback){
    $.ajax({
        type: "POST",
        url: "/materials/queryData",
        contentType: "application/json",
        dataType: "json",
        // async: false,
        beforeSend: showLoading,
        complete: hideLoading,
        data: JSON.stringify(
            {
                search: {supplier_id: $("#supplier").val()}
            }
        ),
        success: function(response) {
            const materials = response["data"]
            let select = document.getElementById("material");
            select.innerHTML = "";
            // select.classList = "ui search selection dropdown";

            for(var i = 0; i < materials.length; i++){
                let this_material = materials[i];
                let option = document.createElement("option");
                option.value = this_material.material_id;
                option.innerHTML = this_material.material_name_and_no;
                option.setAttribute("data-unit", this_material["material_unit"]);
                select.appendChild(option);
            }
            callback();
            // document.getElementById("material").appendChild(select);
        },
        error: function(response) {
            // console.log(response);
        }
    });
}

function update_material_unit(){
    $("#material_unit").val($("#material option:selected").attr("data-unit"));
}

function update_material_unit_price(){
    const set_unit_price = parseFloat($("#set_unit_price").val());
    const material_count = parseFloat($("#material_count").val());
    const material_unit_price = (set_unit_price / material_count).toFixed(2);
    $("#material_unit_price").val(material_unit_price);
}

    

</script>
@endsection