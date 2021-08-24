@extends('layouts.app')

@section('custom_css')
<style>
    /* tr{
        border: 1px solid black;
    } */
</style>
@endsection
@section('content')
<h3 class="ui block header">庫存調整</h3>

<div id="this_form" class="ui form">
    <div style="text-align:right;">
        <a class="ui button" href="/adjustments/index">
            <i class="left chevron icon"></i>
            返回
        </a>
        <button id="submit" class="ui button primary submit">儲存</button>
    </div>
    <div class=" fields">
        <div class="eight wide field disabled">
            <label>庫存盤點編號</label>
            <input type="text" value="AUTONUM">
        </div>
        <div class="eight wide field">
            <label>單據日期</label>
            <input id="voucher_date" type="text" value="" disabled>
        </div>
    </div>
    <div class=" fields">
        <div class="eight wide field">
            <label>備註</label>
            <input id="note" type="text">
        </div>
    </div>
</div>
<div class="ui segment">

    <div class="ui grid" style="margin-bottom: 1rem;">
        <div class="four column row">
            <div class="left floated column"><h5>單位換算</h5></div>
            <div class="right floated column" style="text-align:right;">
                <button id="add_item_btn" class="ui primary basic button">+</button>
            </div>
        </div>
    </div>
    <div class="col-sm-12" >
        <table id="adjustments" style="width:100%; text-align:center;" class="ui celled table">
            <thead>
                <tr>
                    <th>序號</th>
                    <th>材料</th>
                    <th>單位</th>
                    <th>調增/調減</th>
                    <th>數量</th>
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
    let material_select;
    let row_number = 0;
    set_material_select();
    $('#voucher_date').val(getymd());

    //validation setting
    // $('#this_form').form.settings.prompt.empty = "請填寫{name}";
    // $('#this_form').form.settings.prompt.number = "{name}應為數字";

    // $('#this_form').form({
    //     inline : true,
    //     fields: {
    //         voucher_date: 'empty',
    //     }
    // });

    $("#submit").click(function(){
        let data = {
            voucher_date: $("#voucher_date").val(),
            note: $("#note").val()
        };
        var table = document.getElementById("adjustments");
        var items = [];
        for (var i = 0, row; row = table.rows[i]; i++) {
            const data_material_amount = $(row).find("[data-material-amount]");
            const data_adjust_type = $(row).find("[data-adjust-type]");
            const data_material = $(row).find("[data-material]");

            if(
                data_material_amount.length > 0 && 
                data_adjust_type.length > 0 &&
                data_material.length > 0 
            ){
                const this_item = {
                    "amount": data_material_amount[0].value, 
                    "adjustment_type": data_adjust_type[0].value, 
                    "material_id": data_material[0].value
                };
                items.push(this_item);
            }
        }

        data.items = items;

        $.ajax({
            type: "POST",
            url: "/adjustments/store",
            contentType: "application/json",
            dataType: "json",
            beforeSend: showLoading,
            complete: hideLoading,
            data: JSON.stringify(data),
            success: function(response) {
                if("message" in response){
                    alert(response["message"]);
                    return;
                }

                window.location.href = "/adjustments/index";
            },
            error: function(response) {
                // console.log(response);
            }
        });
    })

    $("#add_item_btn").click(function(){
        // if($("#material_unit").val() == ""){
        //     alert("請先填寫庫存單位");
        //     return;
        // }
        add_adjustment_row();
    });

    function add_adjustment_row (data = {}){
        const table_name = "adjustments";
        const columns = ["#", "materials", "material_unit", "adjust_type", "amount"];
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
            } else if (thisColumn == "materials"){
                select = material_select.cloneNode(true);
                select.setAttribute("data-material", "");
                select.onchange = material_changed;

                // td.className = "warning";
                td.appendChild(select);
                $(select).dropdown({
                    fullTextSearch: true,
                    placeholder: false
                });
            } else if (thisColumn == "adjust_type"){
                const select = document.createElement("select");
                select.classList = "ui search selection dropdown fluid";

                const empty_option = get_empty_option();
                const increase_option = document.createElement("option");
                increase_option.innerHTML = "調增";
                increase_option.value = "increase";

                const decrease_option = document.createElement("option");
                decrease_option.innerHTML = "調減";
                increase_option.value = "decrease";

                select.appendChild(empty_option);
                select.appendChild(increase_option);
                select.appendChild(decrease_option);
                select.setAttribute("data-adjust-type", "");

                td.appendChild(select);
                $(select).dropdown({
                    fullTextSearch: true,
                    placeholder: false
                });

            }  else if (thisColumn == "amount"){
                const input = document.createElement("input");
                input.type = "number";
                input.min = 0;
                // input.placeholder = 10;
                input.setAttribute("data-material-amount", "");
                input.style.width = "100%";
                // input.className = "input-short";
                const div = document.createElement('div');
                div.className = "ui input";
                div.style.width = "100%";
                div.appendChild(input);
                td.appendChild(div);
            } else if (thisColumn == "material_unit"){
                const label = document.createElement("label");
                label.setAttribute("data-unit", "");
                td.appendChild(label);
            }

            tr.appendChild(td);
        }

        $("#"+table_name+" tbody").append(tr);
        // document.getElementById("purchase_items").appendChild(tr);
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
            // data: JSON.stringify(
            //     {
            //         search: {supplier_id: $("#supplier").val()}
            //     }
            // ),
            success: function(response) {
                const materials = response["data"]
                const select = document.createElement("select");
                
                select.classList = "ui search selection dropdown fluid";
                select.appendChild(get_empty_option());

                for(var i = 0; i < materials.length; i++){
                    const this_material = materials[i];
                    const option = document.createElement("option");
                    option.value = this_material.material_id;
                    option.innerHTML = this_material.material_name_and_no;
                    option.setAttribute('data-material-unit', this_material.material_unit);
                    select.appendChild(option);
                    select.onchange = "set_import_conversion_select(this.id)";
                }
                
                material_select = select;
            }
        });
        return true;
    }

    function material_changed(event){
        update_unit(event.target)
    }

    function update_unit(select){
        let selected_option = $(select).find(":selected")[0];
        if(selected_option == undefined)
            selected_option = select.options[0];
        const material_unit = selected_option.getAttribute('data-material-unit');
        const tr = $(select).closest("tr");
        const row_unit = tr.find("[data-unit]")[0];

        row_unit.innerHTML = material_unit;
    }

    function get_empty_option(){
        const empty_option = document.createElement("option");
        empty_option.innerHTML = "請選擇";
        empty_option.value = "";
        return empty_option;
    }
});
    


    

</script>
@endsection