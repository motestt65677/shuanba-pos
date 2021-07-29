@extends('layouts.app')

@section('custom_css')
<style>
    /* tr{
        border: 1px solid black;
    } */
    .input-short{
        width: 100%;
        max-width: 7rem;
        text-align: center;
    }
</style>
@endsection
@section('content')
<h3 class="ui block header">編輯材料</h3>
<div id="this_form" class="ui form">
    <div style="text-align:right;">
        <a class="ui button" href="/materials/index">
            <i class="left chevron icon"></i>
            返回
        </a>
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
            <label>庫存單位</label>
            <input id="material_unit" type="text" value="" placeholder="個, 40/箱, 3kg">
        </div>
        <div class="eight wide field">
            <label>預設單價</label>
            <input id="material_unit_price" type="text" value="0">
        </div>
    </div>
</div>
<div class="ui segment">
    <div class="ui grid" style="margin-bottom: 1rem;">
        <div class="four column row">
            <div class="left floated column"><h5>單位換算</h5></div>
            <div class="right floated column"  style="text-align:right;">
                <button id="add_item_btn" class="ui primary basic button" style="float:right;">+</button>
            </div>
        </div>
    </div>
    <div class="col-sm-12" >
        <table id="conversions" style="width:100%; text-align:center;" class="ui celled table">
            <thead>
                <tr>
                    <th>序號</th>
                    <th>數量</th>
                    <th>換算單位</th>
                    <th>=</th>
                    <th>換算數量</th>
                    <th>庫存單位</th>
                    <th>價格</th>
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
            material_name: 'empty',
            material_name: 'empty',
            material_unit_price: ['number', 'empty'],
            material_unit: 'empty'
        }
    });

    // const supplier_id = 



    $("#submit").click(function(){
        if( $('#this_form').form('is valid')) {
            let data = {
                "material_id": get_url_id(),
                "supplier_id": $("#supplier").val(),
                "material_name": $("#material_name").val(),
                "material_unit": $("#material_unit").val(),
                "material_unit_price": $("#material_unit_price").val(),
            };

            var table = document.getElementById("conversions");
            var items = [];
            for (var i = 0, row; row = table.rows[i]; i++) {
                const data_import_amount = $(row).find("[data-import-amount]");
                const data_import_unit = $(row).find("[data-import-unit]");
                const data_material_amount = $(row).find("[data-material-amount]");
                const data_price = $(row).find("[data-price]");

                if(
                    data_price.length > 0 && 
                    data_import_amount.length > 0 &&
                    data_import_unit.length > 0 &&
                    data_material_amount.length > 0
                ){
                    const this_item = {
                        "import_price": data_price[0].value, 
                        "import_count": data_import_amount[0].value, 
                        "import_unit": data_import_unit[0].value,
                        "material_count": data_material_amount[0].value
                    };
                    items.push(this_item);
                }
            }
            data.items = items;

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
    $("#material_unit").change(function(){
        $("[data-material-unit]").html($("#material_unit").val());
    })

    let row_number = 0;
    $("#add_item_btn").click(add_import_conversion_row);


    function add_import_conversion_row (data = {}){
        const table_name = "conversions";
        const columns = ["#", "import_amount", "import_unit", "=", "material_amount", "material_unit", "price"];
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
            } else if (thisColumn == "import_amount"){
                const input = document.createElement("input");
                input.type = "number";
                input.min = 0;
                // input.placeholder = 1;
                input.setAttribute("data-import-amount", "");
                if("import_amount" in data)
                    input.value = data["import_amount"];
                input.className = "input-short";
                const div = document.createElement('div');
                div.className = "ui input";
                div.appendChild(input);
                td.appendChild(div);

            } else if (thisColumn == "import_unit"){
                const input = document.createElement("input");
                input.type = "text";
                // input.placeholder = "箱";
                input.setAttribute("data-import-unit", "");
                if("import_unit" in data)
                    input.value = data["import_unit"];
                input.className = "input-short";
                const div = document.createElement('div');
                div.className = "ui input";
                div.appendChild(input);
                td.appendChild(div);

            }  else if (thisColumn == "="){
                const label = document.createElement("label");
                label.innerHTML = "=";
                td.appendChild(label);

            } else if (thisColumn == "material_amount"){
                const input = document.createElement("input");
                input.type = "number";
                input.min = 0;
                // input.placeholder = 10;
                input.setAttribute("data-material-amount", "");
                if("material_amount" in data)
                    input.value = data["material_amount"];
                input.className = "input-short";
                const div = document.createElement('div');
                div.className = "ui input";
                div.appendChild(input);
                td.appendChild(div);

            } else if (thisColumn == "material_unit"){
                const label = document.createElement("label");
                label.innerHTML = $("#material_unit").val();
                label.setAttribute("data-material-unit", "");

                td.appendChild(label);

            }  else if (thisColumn == "price"){
                const input = document.createElement("input");
                input.type = "number";
                input.min = 0;
                // input.placeholder = 1200;
                input.setAttribute("data-price", "");
                if("import_price" in data)
                    input.value = data["import_price"];
                input.className = "input-short";
                const div = document.createElement('div');
                div.className = "ui input";
                div.appendChild(input);
                td.appendChild(div);

            }

            tr.appendChild(td);
        }

        
        $("#conversions tbody").append(tr);
        // document.getElementById("purchase_items").appendChild(tr);
    }
    function bind_supplier_select(){
        return $.ajax({
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

    function load_material(){
        return $.ajax({
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
                    $("#supplier").dropdown("set selected", material["supplier_id"]);
                    $("#material_name").val(material["material_name"]);
                    $("#material_unit").val(material["material_unit"]);
                    $("#material_unit_price").val(material["material_unit_price"]);
                    $("#material_no").val(material["material_no"]);
                }
                bind_import_conversion();
            },
            error: function(response) {
                // console.log(response);
            }
        });
    }

    function bind_import_conversion(){
        $.ajax({
            type: "POST",
            url: "/import_conversions/queryData",
            contentType: "application/json",
            dataType: "json",
            beforeSend: showLoading,
            complete: hideLoading,
            data: JSON.stringify({"search": {"material_id": get_url_id()}}),
            success: function(response) {
                const data = response["data"];
                for(let i = 0; i < data.length; i++){
                    add_import_conversion_row({
                        import_amount: data[i]["import_count"],
                        import_unit: data[i]["import_unit"],
                        material_amount: data[i]["material_count"],
                        import_price: data[i]["import_price"]
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
        await bind_supplier_select();
        await load_material();
        $('#supplier').dropdown();
    }
    
});




    

</script>
@endsection