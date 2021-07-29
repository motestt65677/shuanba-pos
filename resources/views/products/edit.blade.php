@extends('layouts.app')

@section('custom_css')
<style>

</style>
@endsection
@section('content')
<h3 class="ui block header">編輯產品</h3>
<div id="this_form" class="ui form">
    <div style="text-align:right;">
        <a class="ui button" href="/products/index">
            <i class="left chevron icon"></i>
            返回
        </a>
        <button id="submit" class="ui button primary submit">儲存</button>
    </div>
    <div class=" fields">
        <div class="eight wide field disabled">
            <label>產品編號</label>
            <input id="product_no" type="text" value="">
        </div>
        <div class="eight wide field">
            <label>產品名稱</label>
            <input id="product_name" type="text" value="">
        </div>
    </div>

    <div class=" fields">
        <div class="eight wide field">
            <label>說明</label>
            <input id="product_description" type="text" value="">
        </div>
        <div class="eight wide field">
            <label>價格</label>
            <input id="product_price" type="text">
        </div>
    </div>
</div>
<div class="ui segment">
    <div class="ui grid" style="margin-bottom: 1rem;">
        <div class="four column row">
            <div class="left floated column"><h5>材料成分</h5></div>
            <div class="right floated column"  style="text-align:right;">
                <button id="add_item_btn" class="ui primary basic button" style="float:right;">+</button>
            </div>
        </div>
    </div>
    <div class="col-sm-12" >
        <table id="product_materials" style="width:100%; text-align:center;" class="ui celled table">
            <thead>
                <tr>
                    <th>序號</th>
                    <th>材料</th>
                    <th>材料數量</th>
                    <th>庫存單位</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
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
    init();
    //validation setting
    $('#this_form').form.settings.prompt.empty = "請填寫{name}";
    $('#this_form').form.settings.prompt.number = "{name}應為數字";
    $('#this_form').form({
        inline : true,
        fields: {
            product_name: 'empty'
        }
    });

    let material_select;

    $("#submit").click(function(){
        if( $('#this_form').form('is valid')) {
            let data = {
                "product_id": get_url_id(),
                "product_name": $("#product_name").val(),
                "product_description": $("#product_description").val(),
                "product_price": $("#product_price").val(),
            };

            var table = document.getElementById("product_materials");
            var items = [];
            for (var i = 1, row; row = table.rows[i]; i++) {
                const material_select = $(row).find("[data-material]");
                console.log(row);
                console.log($(row).find("[data-material]"));

                const material_id = material_select.find(":selected")[0].value;
                const material_count = $(row).find("[data-material-count]")[0].value;

                if(
                    material_id != undefined && 
                    material_count != undefined
                ){
                    const this_item = {
                        "material_id": material_id, 
                        "material_count": material_count 
                    };
                    items.push(this_item);
                }
            }
            data.items = items;
            $.ajax({
                type: "POST",
                url: "/products/update",
                contentType: "application/json",
                dataType: "json",
                beforeSend: showLoading,
                complete: hideLoading,
                data: JSON.stringify(data),
                success: function(response) {
                    window.location.href = "/products/index";
                },
                error: function(response) {
                    // console.log(response);
                }
            });
        }
    })

    let row_number = 0;
    $("#add_item_btn").click(add_product_material_row);

    function add_product_material_row (data = {}){
        const table_name = "product_materials";
        const columns = ["#", "material", "material_count", "material_unit"];
        const body = document.getElementById(table_name).getElementsByTagName('tbody')[0];
        const tr = document.createElement("tr");
        let select;

        tr.setAttribute("data-tr", "");
        row_number += 1;
        for(let i = 0; i < columns.length; i++){
            const thisColumn = columns[i];
            const td = document.createElement("td");

            if(thisColumn == "#"){
                td.appendChild(document.createTextNode(row_number));
            } else if (thisColumn == "material"){
                select = material_select.cloneNode(true);
                select.setAttribute("data-material", "");
                td.appendChild(select);
                $(select).dropdown({selectedfullTextSearch: true, placeholder: false});
                select.onchange = material_changed;
                setTimeout(function(){
                    if("material_id" in data){
                        $(select).val(data["material_id"]).change();
                        // $(select).dropdown({"set exactly": "1"});
                    }
                }, 200);
            } else if (thisColumn == "material_count"){
                const input = document.createElement("input");
                input.type = "number";
                input.min = 0;
                input.setAttribute("data-material-count", "");
                const div = document.createElement('div');
                div.className = "ui input";
                div.appendChild(input);
                td.appendChild(div);
                if("material_count" in data){
                    input.value = data["material_count"];
                }
            }  else if (thisColumn == "material_unit"){
                const label = document.createElement("label");
                label.setAttribute("data-material-unit", "");

                td.appendChild(label);
            }

            tr.appendChild(td);
        }

        
        $("#" + table_name + " tbody").append(tr);
        // document.getElementById("purchase_items").appendChild(tr);
    }
    
    function material_changed(){
        const tr = $(this).closest("tr");
        const material_unit_label = tr.find("[data-material-unit]");
        const material_select = tr.find("[data-material]");
        const selected_option = material_select.find(":selected");
        material_unit_label.html(selected_option.data('material-unit'));
    }

    function set_material_select(){
        return $.ajax({
            type: "POST",
            url: "/materials/queryData",
            contentType: "application/json",
            dataType: "json",
            // async: false,
            // beforeSend: showLoading,
            // complete: hideLoading,
            data: JSON.stringify(
                {
                    search: {}
                }
            ),
            success: function(response) {
                const materials = response["data"]
                const select = document.createElement("select");
                
                select.classList = "ui search selection dropdown";
                            

                select.appendChild(get_empty_option());

                for(var i = 0; i < materials.length; i++){
                    const this_material = materials[i];
                    const option = document.createElement("option");
                    option.value = this_material.material_id;
                    option.innerHTML = this_material.material_name;
                    option.setAttribute("data-material-unit", this_material["material_unit"]);
                    select.appendChild(option);
                    select.onchange = material_changed;
                }
                material_select = select;
            },
            error: function(response) {
                // console.log(response);
            }
        });
        return true;
    }

    function load_product(){
        return $.ajax({
            type: "POST",
            url: "/products/queryData",
            contentType: "application/json",
            dataType: "json",
            beforeSend: showLoading,
            complete: hideLoading,
            data: JSON.stringify({"search": {"product_id": get_url_id()}}),
            success: function(response) {
                if(response["data"].length > 0){
                    const product = response["data"][0];
                    $("#product_name").val(product["product_name"]);
                    $("#product_description").val(product["product_description"]);
                    $("#product_price").val(product["product_price"]);
                }
            },
            error: function(response) {
                // console.log(response);
            }
        });
    }

    function get_empty_option(){
        const empty_option = document.createElement("option");
        empty_option.innerHTML = "請選擇";
        empty_option.value = "";
        return empty_option;
    }

    function bind_product_materials(){
        return $.ajax({
            type: "POST",
            url: "/product_materials/queryData",
            contentType: "application/json",
            dataType: "json",
            beforeSend: showLoading,
            complete: hideLoading,
            data: JSON.stringify({"search": {"product_id": get_url_id()}}),
            success: function(response) {
                const data = response["data"];
                for(let i = 0; i < data.length; i++){
                    add_product_material_row({
                        material_id: data[i]["material_id"],
                        material_count: data[i]["material_count"],
                    });
                }
                // let select = document.getElementById('supplier');
                // select.innerHTML = "";
                // for(let i = 0; i < data.length; i++){
                //     let this_supplier = data[i];
                //     let option = document.createElement('option');
                //     option.value = this_supplier.supplier_id;
                //     option.innerHTML = this_supplier.supplier_no + '(' + this_supplier.supplier_name + ')';
                //     select.appendChild(option);
                // }
            },
            error: function(response) {
                // console.log(response);
            }
        });
    }

    async function init(){
        await set_material_select();
        await load_product();
        await bind_product_materials();
        // await set_import_conversion_select();
        // $("#add_item_btn").trigger('click');
    }
});




    

</script>
@endsection