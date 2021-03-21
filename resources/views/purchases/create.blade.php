@extends('layouts.app')

@section('custom_css')
<style>
    /* tr{
        border: 1px solid black;
    } */
</style>
@endsection
@section('content')
    <div style="text-align:right;">
        <button id="submit" class="ui button primary" type="submit">Submit</button>
    </div>

    <div class="ui form">
        <div class=" fields">
            <div class="eight wide field disabled">
                <label>單據編號</label>
                <input type="text" value="AUTONUM">
            </div>
            <div class="eight wide field disabled">
                <label>金額</label>
                <input id="total" type="text" value="100">
            </div>

        </div>
        
        <div class=" fields">
            <div class="six wide field">
                <label>單據日期</label>
                <input id="purchase_date" type="text" value="">
            </div>
            <div class="six wide field">
                <label>廠商</label>

                <select name="" id="supplier" class="ui search selection dropdown">
                    <option value=""></option>
                    @foreach($suppliers as $supplier)
                        <option value="{{$supplier->id}}">{{$supplier->supplier_no}}({{$supplier->name}})</option>
                    @endforeach
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
       
        <div class=" fields">
            <div class="sixteen wide field">
                <label>備註一</label>
                <input id="note1" type="text" >
            </div>
        </div>
        <div class=" fields">
            <div class="sixteen wide field">
                <label>備註二</label>
                <input id="note2" type="text" >
            </div>
        </div>
    </div>

    <div class="col-sm-12">
        <button id="add_item_btn" class="ui primary basic button disabled">+</button>
        <table id="purchase_items" style="width:100%; text-align:center;" class="ui celled table">
            <tr>
                <th>序號</th>
                <th>產品</th>
                <th>單位</th>
                <th>數量</th>
                <th>單價</th>
                <th>金額</th>
            </tr>
        </table>
    </div>

    {{-- <div id="confirm_reset_modal" class="ui modal">
        <i class="close icon"></i>
        <div class="header">
            
        </div>
        <div class="image content">

            <div class="description">
                變更廠商會把設定過的產品刪除，確定要變更嗎？
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

    $('#purchase_date').datetimepicker({
        timepicker:false,
        format: 'Y-m-d',
        // minDate: firstDay,
        // maxDate: lastDay,
        scrollMonth : false
    });

    $('#purchase_date').val(getymd());
    // $("#supplier").select2();
    $('#supplier').dropdown();
    $('#supplier').change(function(){
        if($("#supplier").val() != ""){
            $("#add_item_btn").removeClass("disabled");
        } else {
            $("#add_item_btn").addClass("disabled");
        }

        if($("[data-tr]").length != 0){
            row_number = 0;
            $("[data-tr]").remove();
            set_material_select(function(){
                $("#add_item_btn").trigger('click');
            });
        

        } else {
            set_material_select(function(){
                $("#add_item_btn").trigger('click');
            });
        }

    })

    $('#payment_type').dropdown({
        // clearable: true,
        fullTextSearch: true
    })

    $("#submit").click(function(){
        let data = {
            "purchase_date": $("#purchase_date").val(),
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
                // window.location.href = "/pettyCash";
            },
            error: function(response) {
                // console.log(response);
            }
        });
        
    })


    let material_select;
    let row_number = 0;


    $("#add_item_btn").click(function(){
        const table_name = "purchase_items";
        const columns = ["#", "item_id", "unit", "amount", "unit_price", "total"];
        const body = document.getElementById(table_name).getElementsByTagName('tbody')[0];
        const tr = document.createElement("tr");
        tr.setAttribute("data-tr", "");
        row_number += 1;
        for(let i = 0; i < columns.length; i++){
            const thisColumn = columns[i];
            const td = document.createElement("td");

            if(thisColumn == "#"){
                td.appendChild(document.createTextNode(row_number));
            }else if (thisColumn == "item_id"){
                const select = material_select.cloneNode(true);
                select.setAttribute("data-item-id", "");
                td.appendChild(select);
            }else if (thisColumn == "unit"){
                // td.setAttribute("data-unit", "");
                td.appendChild(document.createTextNode("個"));
            }else if (thisColumn == "amount"){
                const input = document.createElement("input");
                input.type = "number";
                input.min = 0;
                input.value = 0;
                input.setAttribute("data-amount", "");
                td.appendChild(input);
            }else if (thisColumn == "unit_price"){
                const input = document.createElement("input");
                input.type = "number";
                input.min = 0;
                input.value = 0;
                input.setAttribute("data-unit-price", "");
                td.appendChild(input);
            }else if (thisColumn == "total"){
                // td.setAttribute("data-total", "");
                td.appendChild(document.createTextNode("0"));
            }

            tr.appendChild(td);
        }
        $("#purchase_items tbody").append(tr);
        // document.getElementById("purchase_items").appendChild(tr);
        $('.dropdown').dropdown({
            // clearable: true,
            fullTextSearch: true
        })
    })

    // $("#supplier").onChange(function(){
        

    // })
    function query_materials(){
        
    }
    function set_material_select(callback){
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
                    supplier_id: $("#supplier").val()
                }
            ),
            success: function(materials) {
                const select = document.createElement("select");
                select.classList = "ui search selection dropdown";
                for(var i = 0; i < materials.length; i++){
                    const this_material = materials[i];
                    const option = document.createElement("option");
                    option.value = this_material.id;
                    option.innerHTML = this_material.name;
                    select.appendChild(option);
                }
                material_select = select;
                callback();
            },
            error: function(response) {
                // console.log(response);
            }
        });
        return true;
    }

    

</script>
@endsection