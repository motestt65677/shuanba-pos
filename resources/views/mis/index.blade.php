@extends('layouts.app')

@section('custom_css')
<style>
    table,
    th,
    td {
    padding: 10px;
    border: 1px solid black;
    border-collapse: collapse;
    }
</style>
@endsection
@section('content')

<h3 class="ui block header" style="position:inline-block;">
    系統維護工具
</h3>

<h4>月結進貨表單批量匯入</h4>
<div class="ui vertical segment">
    <div class="ui segment">
        <h5>Step1: 複製貼上資料</h5>
        <textarea name="" id="paste"  rows="10" style="width: 100%;"></textarea>
    </div>
    <div class="ui segment">
        <h5>Step2: 確認資料</h5>
        {{-- <div class="ui form">
            <div class=" fields">
                <div class="six wide field">
                    <label>帶入現有廠商</label>
                    <select name="" id="supplier" class="ui search selection dropdown">
                        <option value=""></option>
                    </select>
                </div>
            </div>
        </div> --}}

        <div id="review_container" style="margin-top: 1rem;">

        </div>
    </div>
    <div class="ui segment">
        <h5>Step3: 匯入資料</h5>
        <div>
            <button id="import_purchases_btn" class="ui button primary submit " >匯入資料</button>
        </div>
        <div id="result_container" style="margin-top: 1rem;">

        </div>
    </div>

</div>
@endsection

@section('custom_js')
<script>

$(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // bind_supplier_select();
    // $('#supplier').dropdown();
    let purchases;
    $('#paste').bind('input', function() {
        const review_container = document.getElementById('review_container');
        review_container.innerHTML = "";
        const data = $(this).val() + "\n";
        let spread_sheet = [];
        let str = "";
        let this_row = [];

        for(let i = 0; i < data.length; i++){
            if(data.charCodeAt(i) == 9){
                this_row.push(str.trim());
                str = "";
                continue;
            }

            if(data.charCodeAt(i) == 10){
                spread_sheet.push(this_row);
                this_row = [];
                continue;
            }
            

            str += data[i];
        }

        let supplier = "";
        let purchase_date = "";
        purchases = [];
        for(let i = 0; i < spread_sheet.length; i++){
            const this_row = spread_sheet[i];
            for(let j = 0; j < this_row.length; j++){
                const this_cell = this_row[j];
                if(this_cell == "")
                    continue;
                if(supplier == ""){
                    supplier = this_cell.trim();
                    continue;
                }

                if (j == 0 && /\d{1,2}\/\d{1,2}/.test(this_cell)) {
                    purchase_date = this_cell.trim();
                    // it's a date
                }
            }
            if(purchase_date == "")
                continue;
            let this_purchase = {
                purchase_date: purchase_date, 
                material_name: this_row[1], 
                amount: this_row[2], 
                unit_price: this_row[3], 
                total: this_row[4],
                supplier: supplier
            };
            purchases.push(this_purchase);
        }

        const table = create_purchase_item_table(purchases);

        //insert supplier
        const h6 = document.createElement('h6');
        h6.innerHTML = "廠商名稱: ";
        const span = document.createElement('span');
        span.id = "supplier";
        span.innerHTML = supplier;
        h6.appendChild(span);
        h6.style.marginBottom = "1rem";
        review_container.appendChild(h6);
        review_container.appendChild(table);
        // document.getElementById('vendor_name').innerHTML = vendor;
    });


    $("#import_purchases_btn").click(function(){
        const data = {purchases: purchases};
        $.ajax({
            type: "POST",
            url: "/purchases/bulk_import",
            contentType: "application/json",
            dataType: "json",
            beforeSend: showLoading,
            complete: hideLoading,
            data: JSON.stringify(data),
            success: function(response) {
                if(response["error"] == "no supplier found"){
                    alert('查無廠商，請確認廠商名稱已經存在');
                }
                console.log(response);
                const table = create_purchase_item_table_with_error(response["purchase_items"]);

                // //insert supplier
                // const h6 = document.createElement('h6');
                // h6.innerHTML = "廠商名稱: ";
                // const span = document.createElement('span');
                // span.id = "supplier";
                // span.innerHTML = supplier;
                // h6.appendChild(span);
                // review_container.appendChild(h6);
                document.getElementById("result_container").appendChild(table);

            },
            error: function(response) {
                // console.log(response);
            }
        });
    })

    function create_purchase_item_table(purchases){
        const table = document.createElement('table');
        const tr_head = document.createElement('tr');
        const td_purchase_date =  document.createElement('th');
        td_purchase_date.innerHTML = "訂貨日期";
        const td_purchase_material_name =  document.createElement('th');
        td_purchase_material_name.innerHTML = "品項";
        const td_purchase_amount =  document.createElement('th');
        td_purchase_amount.innerHTML = "數量";
        const td_purchase_unit_price =  document.createElement('th');
        td_purchase_unit_price.innerHTML = "單價";
        const td_purchase_total =  document.createElement('th');
        td_purchase_total.innerHTML = "金額";

        tr_head.appendChild(td_purchase_date);
        tr_head.appendChild(td_purchase_material_name);
        tr_head.appendChild(td_purchase_amount);
        tr_head.appendChild(td_purchase_unit_price);
        tr_head.appendChild(td_purchase_total);

        table.appendChild(tr_head);


        for(let i = 0; i < purchases.length; i++){
            const tr_data = document.createElement('tr');
            const this_purchase = purchases[i];
            const td_purchase_date =  document.createElement('td');
            td_purchase_date.innerHTML = this_purchase["purchase_date"];
            const td_purchase_material_name =  document.createElement('td');
            td_purchase_material_name.innerHTML = this_purchase["material_name"];
            const td_purchase_amount =  document.createElement('td');
            td_purchase_amount.innerHTML = this_purchase["amount"];
            const td_purchase_unit_price =  document.createElement('td');
            td_purchase_unit_price.innerHTML = this_purchase["unit_price"];
            const td_purchase_total =  document.createElement('td');
            td_purchase_total.innerHTML = this_purchase["total"];

            tr_data.appendChild(td_purchase_date);
            tr_data.appendChild(td_purchase_material_name);
            tr_data.appendChild(td_purchase_amount);
            tr_data.appendChild(td_purchase_unit_price);
            tr_data.appendChild(td_purchase_total);
            table.appendChild(tr_data);
            table.style.width = "100%";
            table.style.textAlign = "center";
            table.border = "1";
        }
        return table;
    }

    function create_purchase_item_table_with_error(purchases){
        const table = document.createElement('table');
        const tr_head = document.createElement('tr');
        const td_purchase_date =  document.createElement('th');
        td_purchase_date.innerHTML = "訂貨日期";
        const td_purchase_material_name =  document.createElement('th');
        td_purchase_material_name.innerHTML = "品項";
        const td_purchase_amount =  document.createElement('th');
        td_purchase_amount.innerHTML = "數量";
        const td_purchase_unit_price =  document.createElement('th');
        td_purchase_unit_price.innerHTML = "單價";
        const td_purchase_total =  document.createElement('th');
        td_purchase_total.innerHTML = "金額";
        const td_purchase_status =  document.createElement('th');
        td_purchase_status.innerHTML = "結果";

        tr_head.appendChild(td_purchase_date);
        tr_head.appendChild(td_purchase_material_name);
        tr_head.appendChild(td_purchase_amount);
        tr_head.appendChild(td_purchase_unit_price);
        tr_head.appendChild(td_purchase_total);
        tr_head.appendChild(td_purchase_status);

        table.appendChild(tr_head);


        for(let i = 0; i < purchases.length; i++){
            const tr_data = document.createElement('tr');
            const this_purchase = purchases[i];
            const td_purchase_date =  document.createElement('td');
            td_purchase_date.innerHTML = this_purchase["purchase_date"];
            const td_purchase_material_name =  document.createElement('td');
            td_purchase_material_name.innerHTML = this_purchase["material_name"];
            const td_purchase_amount =  document.createElement('td');
            td_purchase_amount.innerHTML = this_purchase["amount"];
            const td_purchase_unit_price =  document.createElement('td');
            td_purchase_unit_price.innerHTML = this_purchase["unit_price"];
            const td_purchase_total =  document.createElement('td');
            td_purchase_total.innerHTML = this_purchase["total"];
            const td_purchase_status =  document.createElement('td');
            td_purchase_status.innerHTML = this_purchase["status"];
            console.log(this_purchase);
            tr_data.appendChild(td_purchase_date);
            tr_data.appendChild(td_purchase_material_name);
            tr_data.appendChild(td_purchase_amount);
            tr_data.appendChild(td_purchase_unit_price);
            tr_data.appendChild(td_purchase_total);
            tr_data.appendChild(td_purchase_status);

            table.appendChild(tr_data);
            table.style.width = "100%";
            table.style.textAlign = "center";
            table.border = "1";
        }
        return table;
    }
});


</script>
@endsection