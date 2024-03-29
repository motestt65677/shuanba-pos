@extends('layouts.app')

@section('custom_css')
<style>
    /* . table,
    th,
    td {
        padding: 10px;
        border: 1px solid black;
        border-collapse: collapse;
    } */
</style>
@endsection
@section('content')

<h3 class="ui block header" style="position:inline-block;">
    系統維護工具
</h3>

<div class="ui top attached tabular menu">
    <a class="item" data-tab="first">月結進貨表單批量匯入</a>
    <a class="active item" data-tab="second">Qlieer 產品報表批量匯入</a>
</div>
<div class="ui bottom attached tab segment" data-tab="first">
    <div id="monthly_purchase" class="ui vertical segment">
        <h4>月結進貨表單批量匯入</h4>
        <div class="ui segment">
            <h5>Step1: 複製貼上資料</h5>
            <textarea name="" id="paste"  rows="10" style="width: 100%;"></textarea>
        </div>
        <div class="ui segment">
            <h5>Step2: 確認資料</h5>
            <div id="review_container" style="margin-top: 1rem;"></div>
        </div>
        <div class="ui segment">
            <h5>Step3: 匯入資料</h5>
            <div>
                <button id="import_purchases_btn" class="ui button primary submit " >匯入資料</button>
            </div>
            <div id="result_container" style="margin-top: 1rem;"></div>
        </div>
    </div>
</div>
<div class="ui bottom attached active tab segment " data-tab="second">
    <div id="qlieer_order" class="ui vertical segment ">
        <h4>Qlieer 產品報表批量匯入</h4>
        <div class="ui segment">
            <h5>Step1: 複製貼上資料</h5>
            <textarea name="" id="paste_qlieer_order"  rows="10" style="width: 100%;"></textarea>
        </div>
        <div class="ui segment">
            <h5>Step2: 選擇日期/確認資料</h5>
            <div id="qlieer_order_form" class="ui form">
                <div class=" fields">
                    <div class="sixteen wide field">
                        <label>單據日期</label>
                        <input id="qlieer_voucher_date" type="text" value="">
                    </div>
                </div>
            </div>
        </div>
        <div class="ui segment">
            <h5>Step3: 匯入資料</h5>
            <div>
                <button id="import_purchases_btn_qlieer_order" class="ui button primary submit " >匯入資料</button>
            </div>
        </div>
        <div class="ui segment">
            <h5>資料</h5>
            <div id="review_container_qlieer_order" style="margin-top: 1rem;"></div>
            <div id="result_container_qlieer_order" style="margin-top: 1rem;"></div>
        </div>

    
    </div>
</div>
<div id="edit_product_material_modal" class="ui large modal">
    <i class="close icon"></i>
    <div class="header">
        Qlieer產品和庫存材料配對
    </div>
    <div class="content">
        <div class="description">
            <p><h5>Qlieer產品名稱:</h5> <span id="product_name"></span></p>
            <div>
                <div class="ui grid" style="margin-bottom: 1rem;">
                    <div class="four column row">
                        <div class="left floated column"><h5>材料成分</h5></div>
                        <div class="right floated column"  style="text-align:right;">
                            <button id="add_item_btn" class="ui primary basic button" style="float:right;">+</button>
                        </div>
                    </div>
                </div>
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
    </div>
    <div class="actions">
        {{-- <button class="ui black deny button">
            取消
        </button> --}}
        <button id="next_product" class="ui secondary right labeled icon button">
            編輯下一個項目
            <i class="checkmark icon"></i>
        </button>
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
    $('.menu .item').tab();
    $('#qlieer_voucher_date').datetimepicker({
        timepicker:false,
        format: 'Y-m-d',
        scrollMonth : false
    });
    $('#qlieer_order_form').form.settings.prompt.empty = "請填寫{name}";
    $('#qlieer_order_form').form({
        inline : true,
        fields: {
            qlieer_voucher_date: 'empty',
        }
    });

    $('#qlieer_voucher_date').val(getymd());
    //***********************monthly purchase********************************
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
                } else if (response["error"] == "date format error"){
                    alert('日期格式錯誤');
                } else if ("error" in response){
                    alert(response["error"]);
                }
                const table = create_purchase_item_table_with_error(response["purchase_items"]);

                // //insert supplier
                // const h6 = document.createElement('h6');
                // h6.innerHTML = "廠商名稱: ";
                // const span = document.createElement('span');
                // span.id = "supplier";
                // span.innerHTML = supplier;
                // h6.appendChild(span);
                // review_container.appendChild(h6);
                const result_container = document.getElementById("result_container");
                result_container.innerHTML = "";
                result_container.appendChild(table);

            },
            error: function(response) {
                // console.log(response);
            }
        });
    })


    function create_purchase_item_table(purchases){
        const table = document.createElement('table');
        table.className = "ui celled table";
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
        table.className = "ui celled table";
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

    //***********************qlieer_order********************************
    let qlieer_order_items;
    let qlieer_order_total;
    $('#paste_qlieer_order').bind('input', function() {
        const review_container = document.getElementById('review_container_qlieer_order');
        review_container.innerHTML = "";
        const data = $(this).val() + "\n";
        let spread_sheet = convert_to_spreadsheet(data);
        let set_name = "";
        qlieer_order_total = 0;
        // let purchase_date = "";
        qlieer_order_items = [];
        for(let i = 0; i < spread_sheet.length; i++){
            const this_row = spread_sheet[i];
            for(let j = 0; j < this_row.length; j++){
                const this_cell = this_row[j];
                if(j == 0 && is_normal_integer(this_cell)){
                    set_name = this_row[1];
                    qlieer_order_total += parseFloat(this_row[3]);
                    continue;
                }
            }

            if(set_name == ""){
                continue;
            }

            let this_order_item = {
                product_name: this_row[1], 
                product_count: this_row[2], 
                set_name: set_name
            };
            qlieer_order_items.push(this_order_item);
        }
        const table = create_qlieer_order_items_table(qlieer_order_items);

        // //insert supplier
        // const h6 = document.createElement('h6');
        // h6.innerHTML = "廠商名稱: ";
        // const span = document.createElement('span');
        // span.id = "supplier";
        // span.innerHTML = supplier;
        // h6.appendChild(span);
        // h6.style.marginBottom = "1rem";
        // review_container.appendChild(h6);
        review_container.appendChild(table);
        // document.getElementById('vendor_name').innerHTML = vendor;
    });

    let material_select;
    let product_material_edit_index = 0;
    let product_material_not_exists = [];
    let product_material_exists = [];
    let row_number = 0;
    let product_id;
    set_material_select();

    $("#add_item_btn").click(add_product_material_row);
    $("#next_product").click(function(){
        //update product_materials to db
        let data = {
            "product_id": product_id,
            "items": []
        };

        var table = document.getElementById("product_materials");
        var items = [];
        for (var i = 1, row; row = table.rows[i]; i++) {
            const material_select = $(row).find("[data-material]");
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
        if(items.length == 0){
            next_product();
            return;
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
                console.log(response);
                next_product();
                // window.location.href = "/products/index";
            },
            error: function(response) {
                // console.log(response);
            }
        });
        //setup next product edit modal
    });
    $("#import_purchases_btn_qlieer_order").click(function(){
        //check for product without product material and allow user to edit
        const data = {
            qlieer_order_items: qlieer_order_items,
            qlieer_order_total: qlieer_order_total,
            voucher_date: $("#qlieer_voucher_date").val()
        };

        $.ajax({
            type: "POST",
            url: "/orders/bulkImportProductCheck",
            contentType: "application/json",
            dataType: "json",
            beforeSend: showLoading,
            complete: hideLoading,
            data: JSON.stringify(data),
            success: function(response) {
                console.log(response);
                //set to -1 so next product index is 0
                product_material_edit_index = -1;
                product_material_not_exists = response["qlieer_order_items"]["product_material_not_exists"];
                product_material_exists = response["qlieer_order_items"]["product_material_exists"];

                next_product();

                // const table = create_qlieer_order_items_table(response["order_items"]);
                // document.getElementById("result_container_qlieer_order").appendChild(table);
                // document.getElementById('review_container_qlieer_order').innerHTML = "";
            },
            error: function(response) {
                // console.log(response);
            }
        });
    })

    function import_qlieer(){
        $('#qlieer_order_form').form('validate form');
        if($('#qlieer_order_form').form('is valid')) {
            const data = {
                qlieer_order_items: qlieer_order_items,
                qlieer_order_total: qlieer_order_total,
                voucher_date: $("#qlieer_voucher_date").val()
            };

            $.ajax({
                type: "POST",
                url: "/orders/bulkImportQlieerOrders",
                contentType: "application/json",
                dataType: "json",
                beforeSend: showLoading,
                complete: hideLoading,
                data: JSON.stringify(data),
                success: function(response) {
                    const table = create_qlieer_order_items_table(response["order_items"]);
                    document.getElementById("result_container_qlieer_order").appendChild(table);
                    document.getElementById('review_container_qlieer_order').innerHTML = "";
                    alert('匯入完成')
                },
                error: function(response) {
                    // console.log(response);
                }
            });
        }

    }

    function next_product(){
        if(product_material_edit_index >= product_material_not_exists.length - 1){
            alert("所有產品都已經和庫存材料配對完成，現在匯入資料");
            $("#edit_product_material_modal").modal('hide');
            import_qlieer();
            return
        }
        clear_product_material_row();
        product_material_edit_index++;
        product_id = product_material_not_exists[product_material_edit_index]["product_id"];
        update_product_material_modal(product_material_not_exists[product_material_edit_index]);
        $("#edit_product_material_modal").modal('show');
    }
    function clear_product_material_row(){
        const table_name = "product_materials";
        row_number = 0;
        $("#" + table_name + " tbody").html('');
    }

    function get_empty_option(){
        const empty_option = document.createElement("option");
        empty_option.innerHTML = "請選擇";
        empty_option.value = "";
        return empty_option;
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

    function material_changed(){
        const tr = $(this).closest("tr");
        const material_unit_label = tr.find("[data-material-unit]");
        const material_select = tr.find("[data-material]");
        const selected_option = material_select.find(":selected");
        material_unit_label.html(selected_option.data('material-unit'));
    }

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
                $(select).dropdown({fullTextSearch: true, placeholder: false});
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

    function update_product_material_modal(data){
        $("#product_name").html(data["product_name"]);
    }


    function create_qlieer_order_items_table(qlieer_order_items){
        const table = document.createElement('table');
        table.className = "ui celled table";
        const tr_head = document.createElement('tr');

        const td_set_name =  document.createElement('th');
        td_set_name.innerHTML = "套餐名稱";
        const td_product_name =  document.createElement('th');
        td_product_name.innerHTML = "商品名稱";
        const td_product_count =  document.createElement('th');
        td_product_count.innerHTML = "數量";
        const td_message =  document.createElement('th');
        td_message.innerHTML = "訊息";

        tr_head.appendChild(td_set_name);
        tr_head.appendChild(td_product_name);
        tr_head.appendChild(td_product_count);
        tr_head.appendChild(td_message);


        table.appendChild(tr_head);
        // set_name: set_name
        // product_name: this_row[1], 
        // product_count: this_row[2], 

        for(let i = 0; i < qlieer_order_items.length; i++){
            const tr_data = document.createElement('tr');
            const this_order_item = qlieer_order_items[i];
            const td_set_name =  document.createElement('td');
            td_set_name.innerHTML = this_order_item["set_name"];
            const td_product_name =  document.createElement('td');
            td_product_name.innerHTML = this_order_item["product_name"];
            const td_product_count =  document.createElement('td');
            td_product_count.innerHTML = this_order_item["product_count"];
            const td_message =  document.createElement('td');
            if("message" in this_order_item)
                td_message.innerHTML = this_order_item["message"];

            tr_data.appendChild(td_set_name);
            tr_data.appendChild(td_product_name);
            tr_data.appendChild(td_product_count);
            tr_data.appendChild(td_message);


            table.appendChild(tr_data);
            table.style.width = "100%";
            table.style.textAlign = "center";
            table.border = "1";
        }
        return table;
    }



    function convert_to_spreadsheet(data){
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
                this_row.push(str.trim());
                str = "";
                spread_sheet.push(this_row);
                this_row = [];
                continue;
            }
            

            str += data[i];
        }
        return spread_sheet;
    }

    function is_normal_integer(str) {
        var n = Math.floor(Number(str));
        return n !== Infinity && String(n) === str && n >= 0;
    }
});


</script>
@endsection