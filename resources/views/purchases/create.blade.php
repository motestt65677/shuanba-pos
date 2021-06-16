@extends('layouts.app')

@section('custom_css')
<style>
    /* tr{
        border: 1px solid black;
    } */
</style>
@endsection
@section('content')
<h3 class="ui block header">廠商進貨維護</h3>

    <div class="ui form">
        <div style="text-align:right;">
            <button id="recent_record_btn" class="ui button ">近五筆輸入材料</button>
            <button id="submit" class="ui button primary submit">完成</button>
        </div>
        <div class=" fields">
            <div class="eight wide field disabled">
                <label>單據編號</label>
                <input type="text" value="AUTONUM">
            </div>
            <div class="eight wide field disabled">
                <label>總金額</label>
                <input id="total" type="text" value="0">
            </div>

        </div>
        
        <div class=" fields">
            <div class="six wide field">
                <label>單據日期</label>
                <input id="voucher_date" type="text" value="">
            </div>
            <div class="six wide field">
                <label>廠商</label>
                <select name="" id="supplier" class="ui search selection dropdown">

                </select>
            </div>
            <div class="six wide field">
                <label>付款方式</label>
                <select name="" id="payment_type" class="ui search selection dropdown">
                    <option value="" selected></option>
                    <option value="cash">現金</option>
                    <option value="monthly">月底結算</option>
                </select>
                {{-- <input type="text" placeholder="Middle Name"> --}}
            </div>
        </div>
       
        <div class="fields" style="display:none;">
            <div class="sixteen wide field">
                <label>備註一</label>
                <input id="note1" type="text" >
            </div>
        </div>
        <div class=" fields" style="display:none;">
            <div class="sixteen wide field">
                <label>備註二</label>
                <input id="note2" type="text" >
            </div>
        </div>
    </div>

    <div class="col-sm-12" >
        <button id="add_item_btn" class="ui primary basic button disabled">+</button>
        <table id="purchase_items" style="width:100%; text-align:center;" class="ui celled table">
            <thead>
                <tr>
                    <th>序號</th>
                    <th>廠商材料</th>
                    <th>廠商材料數量</th>
                    <th>材料</th>
                    <th>單位</th>
                    <th>數量</th>
                    <th>單價</th>
                    <th>金額</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>


    </div>





    <div id="recent_record_modal" class="ui large modal">
        <i class="close icon"></i>
        <div class="header">
            近五筆進貨材料
        </div>
        <div class="content">
            <table id="thisTable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th></th>
                        <th>材料編號</th>
                        <th>材料名稱</th>
                        <th>單位</th>
                        <th>數量</th>
                        <th>單價</th>
                        <th>金額</th>
                        <th>輸入日期</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    {{-- <div id="confirm_reset_modal" class="ui modal">
        <i class="close icon"></i>
        <div class="header">
            
        </div>
        <div class="image content">

            <div class="description">
                變更廠商會把設定過的材料刪除，確定要變更嗎？
            </div>
        </div>
        <div class="actions">
            <div id="reset_cancel"  class="ui button">Cancel</div>
            <div id="reset_confirm" class="ui button">OK</div>
        </div>
        </div> --}}
@endsection

@section('custom_js')
<script>
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$(document).ready(function(){
    //bind table

    let data_table = $('#thisTable').DataTable({
        ajax: {
            url: "/purchases/queryPurchaseItems",
            dataSrc: 'data',
            data:{
                search: {"count": 5},
                order: {"id": "desc"}
            },
            type: "POST",
            beforeSend: showLoading,
            complete: hideLoading
        },
        columns: [
            { data: null, orderable:false, className: 'dt-body-center dt-head-center'}, 
            { data: "material_no", orderable:false, className: 'dt-body-center dt-head-center'},
            { data: "material_name", orderable:false, className: 'dt-body-center dt-head-center'},
            { data: "material_unit", orderable:false, className: 'dt-body-center dt-head-center'},
            { data: "amount", orderable:false, className: 'dt-body-center dt-head-center'},
            { data: "unit_price", orderable:false, className: 'dt-body-center dt-head-center'},
            { data: "total", orderable:false, className: 'dt-body-center dt-head-center'},
            { data: "created_date", orderable:false, className: 'dt-body-center dt-head-center'},
        ],
        paging: false,
        searching: false,
        info: false,
        language: {
            url: "/DataTables/localisation/zh_TW.json",
            zeroRecords: "查無紀錄"
        },
        // paging: true,
        fixedHeader: true,
        fnInitComplete: function(oSettings, json) {
            // hideLoading();
        }
    });
    data_table.on( 'order.dt search.dt', function () {
        data_table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        } );
    } ).draw();

    //validation setting
    $('.ui.form').form.settings.prompt.empty = "請填寫{name}";
    $('.ui.form').form({
        inline : true,
        fields: {
            payment_type: 'empty',
            voucher_date: 'empty',
            supplier: 'empty',

        }
    });

    $('#voucher_date').datetimepicker({
        timepicker:false,
        format: 'Y-m-d',
        // minDate: firstDay,
        // maxDate: lastDay,
        scrollMonth : false
    });

    $('#voucher_date').val(getymd());
    // $("#supplier").select2();
    bind_supplier_select();
    $('#supplier').dropdown();
    $('#supplier').change(function(){
        if($("#supplier").val() != ""){
            $("#add_item_btn").removeClass("disabled");
        } else {
            $("#add_item_btn").addClass("disabled");
        }
        // set_material_set_select();

        if($("[data-tr]").length != 0){
            row_number = 0;
            $("[data-tr]").remove();
        } 
        set_row_dropdowns();
    })


    $('#payment_type').dropdown({
        // clearable: true,
        fullTextSearch: true
    })

    $("#submit").click(function(){
        if( $('.ui.form').form('is valid')) {
            let data = {
                "voucher_date": $("#voucher_date").val(),
                "supplier": $("#supplier").val(),
                "payment_type": $("#payment_type").val(),
                "note1": $("#note1").val(),
                "note2": $("#note2").val(),
                "items": []
            };

            var table = document.getElementById("purchase_items");
            var items = [];
            for (var i = 0, row; row = table.rows[i]; i++) {
            //iterate through rows
            //rows would be accessed using the "row" variable assigned in the for loop
                // console.log(row);
                const data_item_id = $(row).find("[data-item-id]");
                const data_amount = $(row).find("[data-amount]");
                const data_unit_price = $(row).find("[data-unit-price]");

                // console.log(data_item_id);
                if(data_item_id.length > 0){
                    if(data_amount[0].value == 0)
                        continue;
                    if(data_unit_price[0].value == 0)
                        continue;
                    const item_id = data_item_id[0].value;
                    const amount = data_amount[0].value;
                    const unit_price = data_unit_price[0].value;

                    const this_item = {"item_id": item_id, "amount": amount, "unit_price": unit_price};
                    items.push(this_item);
                    // console.log(data_item_id[0]);
                    // console.log(data_item_id[0].value);
                }
                // console.log($($(row).data("unit")).get(0));
                
            }
            data.items = items;
            $.ajax({
                type: "POST",
                url: "/purchases/store",
                contentType: "application/json",
                dataType: "json",
                // beforeSend: showLoading,
                // complete: hideLoading,
                data: JSON.stringify(data),
                success: function(response) {
                    // console.log(response);
                    alert("登錄完成")
                    window.location.href = "/purchases/create";
                },
                error: function(response) {
                    // console.log(response);
                }
            });
            
        }

        
    })
    let material_select;
    let material_set_select;

    let row_number = 0;
    $("#recent_record_btn").click(function(){
        $('#recent_record_modal').modal('show')
    })

    $("#add_item_btn").click(function(){
        const table_name = "purchase_items";
        const columns = ["#", "material_set", "material_set_count", "item_id", "unit", "amount", "unit_price", "total"];
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
            }else if (thisColumn == "item_id"){
                select = material_select.cloneNode(true);
                select.setAttribute("data-item-id", "");
                select.onchange = update_unit_price;
                td.appendChild(select);
                // $(select).trigger('change');
            }else if (thisColumn == "unit"){
                // td.setAttribute("data-unit", "");
                td.appendChild(document.createTextNode("個"));
            }else if (thisColumn == "amount"){
                const input = document.createElement("input");
                input.type = "number";
                input.min = 0;
                input.value = 0;
                input.setAttribute("data-amount", "");
                input.oninput = material_amount_changed;
                input.onchange = material_amount_changed;

                td.appendChild(input);
            }else if (thisColumn == "unit_price"){
                const input = document.createElement("input");
                input.type = "number";
                input.min = 0;
                input.value = 0;
                input.setAttribute("data-unit-price", "");
                input.oninput = material_unit_price_changed;
                input.onchange = material_unit_price_changed;

                td.appendChild(input);
            }else if (thisColumn == "total"){
                // td.setAttribute("data-total", "");
                const span = document.createElement("span");
                span.innerHTML = 0;
                span.setAttribute("data-total-price", "");
                td.appendChild(span);
            } else if (thisColumn == "material_set"){
                const set_select = material_set_select.cloneNode(true);
                set_select.onchange = material_set_changed;

                // set_select.addEventListener("change", update_unit_price, false);
                td.appendChild(set_select);
            } else if (thisColumn == "material_set_count"){
                const input = document.createElement("input");
                input.setAttribute("data-set-count", "");
                input.type = "number";
                input.min = 0;
                input.value = 0;
                input.oninput = material_set_count_changed;
                input.onchange = material_set_count_changed;

                const div = document.createElement("div");
                div.className = "ui disabled input";
                div.appendChild(input);
                // input.className = "ui disabled input";
                // input.disabled = true;
                // input.setAttribute("data-set-count", "");
                // input.addEventListener("change", update_row_price, false);
                td.appendChild(div);
            }

            tr.appendChild(td);


        }
        $("#purchase_items tbody").append(tr);
        // document.getElementById("purchase_items").appendChild(tr);
        $('.dropdown').dropdown({
            // clearable: true,
            fullTextSearch: true
        })

        // console.log(select);
        if ("createEvent" in document) {
            var evt = document.createEvent("HTMLEvents");
            evt.initEvent("change", false, true);
            select.dispatchEvent(evt);
        }
        else
            select.fireEvent("onchange");
    })
    
    function update_row_price(row){
        const row_amount = $(row).parent().parent().find("[data-amount]")[0];
        const row_unit_price = $(row).parent().parent().find("[data-unit-price]")[0];
        const row_total_price = $(row).parent().parent().find("[data-total-price]")[0];
        row_total_price.innerHTML = parseFloat(row_amount.value) * parseFloat(row_unit_price.value);
        
    }
    function update_total(){
        let total = 0;
        $("[data-total-price]").each(function(){
            total += parseFloat($(this).html());
        })
        $("#total").val(total);
    }

    function material_unit_price_changed(){
        update_row_price(this);
        update_total();
    }
    function material_amount_changed(){
        update_row_price(this);
        update_total();
    }

    function update_unit_price(){
        const row_item = this;
        let selected_option = $(this).find(":selected")[0];
        if(selected_option == undefined)
            selected_option = this.options[0];
        const unit_price = selected_option.getAttribute('data_unit_price');
        const row_unit_price = $(this).parent().parent().parent().find("[data-unit-price]")[0];
        row_unit_price.value = unit_price;
    }

    function set_material_select(){

        $.ajax({
            type: "POST",
            url: "/materials/queryData",
            contentType: "application/json",
            dataType: "json",
            // async: false,
            // beforeSend: showLoading,
            // complete: hideLoading,
            data: JSON.stringify(
                {
                    search: {supplier_id: $("#supplier").val()}
                }
            ),
            success: function(response) {
                const materials = response["data"]
                const select = document.createElement("select");
                
                select.classList = "ui search selection dropdown";

                for(var i = 0; i < materials.length; i++){
                    const this_material = materials[i];
                    const option = document.createElement("option");
                    option.value = this_material.material_id;
                    option.innerHTML = this_material.material_name;
                    option.setAttribute('data_unit_price', this_material.material_unit_price);
                    select.appendChild(option);
                }
                material_select = select;
            },
            error: function(response) {
                // console.log(response);
            }
        });
        return true;
    }
    function set_material_set_select(){
        return $.ajax({
            type: "POST",
            url: "/material_sets/queryData",
            contentType: "application/json",
            dataType: "json",
            // async: false,
            // beforeSend: showLoading,
            // complete: hideLoading,
            data: JSON.stringify(
                {
                    search: {supplier_id: $("#supplier").val()}
                }
            ),
            success: function(response) {
                const sets = response["data"]
                const select = document.createElement("select");
                
                select.classList = "ui search selection dropdown";

                const option = document.createElement("option");
                option.value = "";
                option.innerHTML = "";
                select.appendChild(option);

                for(var i = 0; i < sets.length; i++){
                    const this_set = sets[i];
                    const option = document.createElement("option");
                    option.value = this_set.set_id;
                    option.innerHTML = this_set.set_name;
                    option.setAttribute('data-material-count', this_set.material_count);
                    option.setAttribute('data-material-id', this_set.material_id);
                    select.appendChild(option);
                }
                material_set_select = select;
            },
            error: function(response) {
                // console.log(response);
            }
        });
        return true;
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
                $("#add_item_btn").removeClass("disabled");
                
                set_row_dropdowns();                

            },
            error: function(response) {
                // console.log(response);
            }
        });
    }

    function material_set_changed(){
        const material_set_select = $(this).find("option:selected");
        const material_select = $(this).parent().parent().parent().find("[data-item-id]");
        const set_count_input = $(this).parent().parent().parent().find("[data-set-count]");

        if(material_set_select.length > 0){

            
            const material_id = material_set_select.data("material-id")
            material_select.val(material_id).change();

            set_count_input.parent().removeClass("disabled");
            set_count_input.val("1").change();
            
            

            // set_count.value = $(this).find("option:selected").data("material-count");
        }

    }

    function material_set_count_changed(){
        const tr = $(this).parent().parent().parent();
        console.log(tr[0]);
        const material_set_select = tr.find("option:selected");
        const set_count_input = tr.find("[data-set-count]");
        const material_count_per_item = material_set_select.data("material-count");
        const material_count = material_count_per_item * set_count_input.val();
        const material_amount_input = tr.find("[data-amount]");
        material_amount_input.val(material_count).change();
    }

    

    async function set_row_dropdowns(){
        await set_material_select();
        await set_material_set_select();
        setTimeout(function(){
            $("#add_item_btn").trigger('click')
        }, "500");
    }



});
    


    

</script>
@endsection