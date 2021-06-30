@extends('layouts.app')

@section('custom_css')
<style>
    /* tr{
        border: 1px solid black;
    } */
    .input-short{
        width: 100%;
        max-width: 7rem !important;
        text-align: center !important;
    }
    .ui.dropdown.search{
        min-width: 7rem;
    }
    .amount-label{
        margin: auto;
        font-size: 1.2rem;
    }
</style>
@endsection
@section('content')
<h3 class="ui block header">退貨單</h3>
    <div class="ui form">
        <div style="text-align:right;">
            <a class="ui button" href="/purchases/index">
                <i class="left chevron icon"></i>
                返回
            </a>
            <button id="submit" class="ui button primary submit">完成</button>
        </div>

        <div class="fields">
            <div class="six wide field disabled">
                <label>單據編號</label>
                <input type="text" value="AUTONUM">
            </div>
            <div class="six wide field">
                <label>單據日期</label>
                <input id="voucher_date" type="text" value="">
            </div>
            <div class="six wide field">
                <label>進貨編號</label>
                <select name="" id="purchase_no" class="ui search selection dropdown"></select>
            </div>
        </div>
        <div class=" fields">
            <div class="eight wide field disabled">
                <label>廠商</label>
                <input id="supplier" type="text" value="">
            </div>
            <div class="eight wide field disabled">
                <label>退貨總額</label>
                <input id="total" type="text">
            </div>
        </div>

        <div class="fields" style="display:none;">
            <div class="eight wide field disabled">
                <label>付款方式</label>
                <input id="payment_type" type="text" value="">
            </div>
            <div class="sixteen wide field disabled">
                <label>備註一</label>
                <input id="note1" type="text" >
            </div>
        </div>
        <div class=" fields" style="display:none;">
            <div class="sixteen wide field disabled">
                <label>備註二</label>
                <input id="note2" type="text" >
            </div>
        </div>
    </div>

    <div class="col-sm-12 form" >
        <table id="purchase_items" style="width:100%; text-align:center;" class="ui celled table">
            <thead>
                <tr>
                    <th>序號</th>
                    <th>材料</th>
                    <th>庫存單位</th>
                    <th>材料數量</th>
                    <th>單價</th>
                    <th>金額</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
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
    //bind table
    init();

    $("#submit").click(function(){

        let data = {
            "purchase_id": $("#purchase_no").val(),
            "voucher_date": $("#voucher_date").val(),
            "items": []
        };

        var table = document.getElementById("purchase_items");
        var items = [];
        for (var i = 1, row; row = table.rows[i]; i++) {
        //iterate through rows
        //rows would be accessed using the "row" variable assigned in the for loop
            // console.log(row);
            const data_purchase_item_id = $(row).find("[data-purchase-item-id]").data("purchase-item-id");
            const data_unit_price = $(row).find("[data-unit-price]").data("unit-price");
            const data_amount = $(row).find("[data-amount]").val();

            if(data_amount == 0)
                continue;
            if(data_unit_price == 0)
                continue;

            const this_item = {"data_purchase_item_id": data_purchase_item_id, "data_amount": data_amount, "data_unit_price": data_unit_price};
            
            items.push(this_item);
                // console.log(data_item_id[0]);
                // console.log(data_item_id[0].value);
            // console.log($($(row).data("unit")).get(0));
            
        }
        data.items = items;
        if(data.items.length == 0){
            alert("無法進行退款，剩餘材料不足或材料單價為0");
            return;
        }
        $.ajax({
            type: "POST",
            url: "/purchase_returns/store",
            contentType: "application/json",
            dataType: "json",
            // beforeSend: showLoading,
            // complete: hideLoading,
            data: JSON.stringify(data),
            success: function(response) {
                // console.log(response);
                if("message" in response)
                    alert(response["message"]);
                else
                    alert("登錄完成");
                window.location.href = "/purchase_returns/index";
            },
            error: function(response) {
                // console.log(response);
            }
        });
    })

    let row_number = 0;
    function add_purchase_item(data = {}){
        const table_name = "purchase_items";
        const columns = ["#", "material_name_and_no","unit", "amount", "unit_price", "total"];
        const body = document.getElementById(table_name).getElementsByTagName('tbody')[0];
        const tr = document.createElement("tr");
        let amount_input;
        tr.setAttribute("data-tr", "");
        row_number += 1;
        for(let i = 0; i < columns.length; i++){
            const thisColumn = columns[i];
            const td = document.createElement("td");
            // td.className = "ui input";
            if(thisColumn == "#"){
                td.appendChild(document.createTextNode(row_number));
            }else if (thisColumn == "material_name_and_no"){
                const label = document.createElement("label");
                label.innerHTML = data["material_name_and_no"];
                const hidden = document.createElement("input");
                hidden.type = "hidden";
                hidden.setAttribute("data-purchase-item-id", data["purchase_item_id"]);
                td.appendChild(label);
                td.appendChild(hidden);

                // $(select).trigger('change');
            }else if (thisColumn == "unit"){
                // td.setAttribute("data-unit", "");
                const label = document.createElement("label");
                label.innerHTML = data["material_unit"];
                td.appendChild(label);
            }else if (thisColumn == "amount"){
                amount_input = document.createElement("input");
                amount_input.type = "number";
                amount_input.className = "input-short";
                amount_input.max = data["item_amount"];
                amount_input.value = data["item_amount"];
                amount_input.oninput = amount_changed;
                amount_input.change = amount_changed;
                amount_input.setAttribute("data-amount", "");

                const label = document.createElement("label");
                label.innerHTML = " &nbsp/&nbsp" + data["item_amount"];
                label.className = "amount-label";
                const div = document.createElement("div");
                div.className = "ui input";
                div.appendChild(amount_input);
                div.appendChild(label);

                td.appendChild(div);

            }else if (thisColumn == "unit_price"){
                const label = document.createElement("label");
                label.setAttribute("data-unit-price", data["item_unit_price"]);
                label.innerHTML = data["item_unit_price"];
                td.appendChild(label);
            }else if (thisColumn == "total"){
                const label = document.createElement("label");
                label.innerHTML = data["item_total"];
                label.setAttribute("data-total", "");
                td.appendChild(label);
            }

            tr.appendChild(td);

        }

        $("#purchase_items tbody").append(tr);
        amount_input.change();
    }

    function amount_changed(){
        if(parseFloat(this.value) > parseFloat(this.max))
            this.value = this.max;
        if(parseFloat(this.value) < 0)
            this.value = 0;

        const unit_price = $(this).closest("tr").find("[data-unit-price]").html();
        const new_total = parseFloat(this.value) * parseFloat(unit_price);
        $(this).closest("tr").find("[data-total]").html(new_total);
        update_total();
    }

    function update_total(){
        let total = 0;
        $("[data-total]").each(function(){
            total += parseFloat($(this).html());
        })
        $("#total").val(total);
    }

    function load_purchase_data(purchase_id){
        // const urlParams = new URLSearchParams(window.location.search);
        // const purchase_id = urlParams.get('purchase-id')
        return $.ajax({
            type: "POST",
            url: "/purchases/queryPurchaseItemsWithReturns",
            contentType: "application/json",
            dataType: "json",
            // async: false,
            // beforeSend: showLoading,
            // complete: hideLoading,
            data: JSON.stringify(
                {
                    search: {purchase_id: purchase_id},
                    order: {}
                }
            ),
            success: function(response) {
                if(response["data"].length > 0){
                    // $("#purchase_no").val(response["data"][0]["purchase_no"]);
                    // $("#voucher_date").val(response["data"][0]["voucher_date"]);
                    $("#supplier").val(response["data"][0]["supplier_name_and_no"]);
                    // $("#payment_type").val(response["data"][0]["payment_type_text"]);
                    $("#note1").val(response["data"][0]["note1"]);
                    $("#note2").val(response["data"][0]["note2"]);
                    $("#purchase_items tbody").html('');
                    for(let i = 0; i < response["data"].length; i++){
                        const thisItem = response["data"][i];
                        add_purchase_item({
                            purchase_item_id: thisItem["purchase_item_id"],
                            material_name_and_no: thisItem["material_name_and_no"],
                            material_unit: thisItem["material_unit"],
                            item_amount: thisItem["item_amount"],
                            item_unit_price: thisItem["item_unit_price"],
                            item_total: thisItem["item_total"]
                        });
                    }
                }
                
            },
            error: function(response) {
                // console.log(response);
            }
        });
    }

    function bind_purchase_no_select(){
        return $.ajax({
            type: "POST",
            url: "/purchases/queryPurchases",
            contentType: "application/json",
            dataType: "json",
            data:JSON.stringify({search: {}, order: {}}),
            // beforeSend: showLoading,
            // complete: hideLoading,
            success: function(response) {
                const purchases = response["data"]
                const select = $("#purchase_no")[0];
                
                // select.classList = "ui search selection dropdown";
                select.appendChild(get_empty_option());

                for(var i = 0; i < purchases.length; i++){
                    const this_purchase = purchases[i];
                    const option = document.createElement("option");
                    option.value = this_purchase.id;
                    option.innerHTML = this_purchase.purchase_no;
                    // option.setAttribute("data-material-unit", this_purchase["material_unit"]);
                    select.appendChild(option);
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
    function purchase_no_changed (){
        load_purchase_data($("#purchase_no").val());
    }

    async function init(){
        // await load_page_data();
        await bind_purchase_no_select();
        $('#voucher_date').datetimepicker({
            timepicker:false,
            format: 'Y-m-d',
            // minDate: firstDay,
            // maxDate: lastDay,
            scrollMonth : false
        });
        $("#purchase_no").dropdown({selectedfullTextSearch: true});
        $("#purchase_no").change(purchase_no_changed);
        $('#voucher_date').val(getymd());
        // await set_import_conversion_select();
        // $("#add_item_btn").trigger('click');
    }



});
    


    

</script>
@endsection