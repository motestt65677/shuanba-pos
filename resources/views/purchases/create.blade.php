@extends('layouts.app')

@section('custom_css')
<style>
    /* tr{
        border: 1px solid black;
    } */
    .input-short{
        width: 100%;
        min-width: 7rem;
        text-align: center;
    }
    .ui.dropdown.search{
        min-width: 7rem;
    }
</style>
@endsection
@section('content')
<h3 class="ui block header">廠商進貨維護</h3>

    <div class="ui form">
        <div style="text-align:right;">
            <button id="recent_record_btn" class="ui button">近五筆輸入材料</button>
            <button id="submit" class="ui button primary submit">完成</button>
        </div>
        <div class="fields">
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
                    <th>材料</th>
                    <th>換算單位</th>
                    <th>數量</th>
                    <th>=</th>
                    <th>庫存單位</th>
                    <th>換算數量</th>
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
            if(data.items.length == 0){
                return;
            }
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
                    if("message" in response)
                        alert(response["message"]);
                    else
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
    let import_unit_select;

    let row_number = 0;
    $("#recent_record_btn").click(function(){
        $('#recent_record_modal').modal('show')
    })

    $("#add_item_btn").click(function(){
        const table_name = "purchase_items";
        const columns = ["#", "item_id", "import_unit", "import_count", "=","unit", "amount", "unit_price", "total"];
        const body = document.getElementById(table_name).getElementsByTagName('tbody')[0];
        const tr = document.createElement("tr");
        let select;

        tr.setAttribute("data-tr", "");
        row_number += 1;
        for(let i = 0; i < columns.length; i++){
            const thisColumn = columns[i];
            const td = document.createElement("td");
            // td.className = "ui input";
            if(thisColumn == "#"){
                td.appendChild(document.createTextNode(row_number));
            }else if (thisColumn == "item_id"){
                select = material_select.cloneNode(true);
                select.setAttribute("data-item-id", "");
                select.onchange = material_changed;

                td.appendChild(select);
                $(select).dropdown({
                    fullTextSearch: true,
                    placeholder: false
                });
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
                input.className = "input-short";
                const div = document.createElement("div");
                div.className = "ui input";
                div.appendChild(input);
                td.appendChild(div);
            }else if (thisColumn == "unit_price"){
                const input = document.createElement("input");
                input.type = "number";
                input.min = 0;
                input.value = 0;
                input.setAttribute("data-unit-price", "");
                input.oninput = material_unit_price_changed;
                input.onchange = material_unit_price_changed;
                input.className = "input-short";
                const div = document.createElement("div");
                div.className = "ui input";
                div.appendChild(input);

                td.appendChild(div);
            }else if (thisColumn == "total"){
                // td.setAttribute("data-total", "");
                const span = document.createElement("span");
                span.innerHTML = 0;
                span.setAttribute("data-total-price", "");
                td.appendChild(span);
            } else if (thisColumn == "import_unit"){
                // const set_select = import_unit_select.cloneNode(true);
                const select = document.createElement("select");
                select.setAttribute("data-import-unit", "");
                select.onchange = import_unit_changed;
                select.className = "ui search selection dropdown fluid";

                // set_select.addEventListener("change", update_unit_price, false);
                td.appendChild(select);
                // $(select).dropdown({
                //     fullTextSearch: true
                // });
            } else if (thisColumn == "import_count"){
                const input = document.createElement("input");
                input.setAttribute("data-import-count", "");
                input.type = "number";
                input.min = 0;
                input.value = 0;
                input.oninput = import_count_changed;
                input.onchange = import_count_changed;
                input.className = "input-short";

                const div = document.createElement("div");
                div.className = "ui disabled input";
                div.appendChild(input);
                // input.className = "ui disabled input";
                // input.disabled = true;
                // input.setAttribute("data-set-count", "");
                // input.addEventListener("change", update_row_price, false);
                td.appendChild(div);
            } else if (thisColumn == "="){
                const label = document.createElement("label");
                label.innerHTML = "=";
                td.appendChild(label);
            }

            tr.appendChild(td);


        }
        $("#purchase_items tbody").append(tr);
        // document.getElementById("purchase_items").appendChild(tr);
        // $('.dropdown').dropdown({
        //     // clearable: true,
        //     fullTextSearch: true
        // })

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
        const tr = $(row).closest("tr");
        const row_amount = tr.find("[data-amount]")[0];
        const row_unit_price = tr.find("[data-unit-price]")[0];
        const row_total_price = tr.find("[data-total-price]")[0];

        const import_conversion_select = tr.find("[data-import-unit]");
        const selected_option = $(import_conversion_select).find(":selected");
        const import_unit_material_count = parseFloat(selected_option.data("import-unit-material-count"));

        
        let total = 0;
        if(parseFloat(row_amount.value) % import_unit_material_count == 0){
            const count = parseFloat(row_amount.value) / parseFloat(import_unit_material_count);
            const import_unit_import_price = parseFloat(selected_option.data("import-unit-import-price"));
            total = count * import_unit_import_price;
        } else {
            total = parseFloat(row_amount.value) * parseFloat(row_unit_price.value);
        }
        
        row_total_price.innerHTML = total.toFixed(2);
        
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
    function material_changed(event){
        update_unit_price(event.target);
        set_import_conversion_select(event.target);
        const tr = $(event.target).closest("tr");
        const row_import_count = tr.find("[data-import-count]");
        row_import_count.parent().addClass('disabled');
        row_import_count.val('0').change();

    }

    function update_unit_price(select){
        let selected_option = $(select).find(":selected")[0];
        if(selected_option == undefined)
            selected_option = select.options[0];
        const unit_price = selected_option.getAttribute('data_unit_price');
        const tr = $(select).closest("tr");
        const row_unit_price = tr.find("[data-unit-price]")[0];
        row_unit_price.value = parseFloat(unit_price).toFixed(2);
    }

    function set_import_conversion_select(select){
        let selected_option = $(select).find(":selected")[0];
        if(selected_option == undefined)
            selected_option = select.options[0];

        $.ajax({
            type: "POST",
            url: "/import_conversions/queryData",
            contentType: "application/json",
            dataType: "json",
            beforeSend: showLoading,
            complete: hideLoading,
            data: JSON.stringify(
                {
                    search: {material_id: selected_option.value}
                }
            ),
            success: function(response) {
                const tr = $(select).closest("tr");
                const import_unit = tr.find("[data-import-unit]");

                const import_unit_select = import_unit[0];
                import_unit_select.innerHTML = "";
                const conversions = response["data"]
                $(import_unit_select).dropdown('clear');

                const empty_option = document.createElement("option");
                empty_option.innerHTML = "請選擇";
                import_unit_select.appendChild(empty_option);
                for(var i = 0; i < conversions.length; i++){
                    const this_conversion = conversions[i];
                    const option = document.createElement("option");
                    // option.value = this_conversion.material_id;
                    option.innerHTML = this_conversion.import_unit;
                    option.setAttribute('data-import-unit', this_conversion.import_unit);
                    option.setAttribute('data-import-unit-conversion-ratio', parseFloat(this_conversion.material_count) / parseFloat(this_conversion.import_count) );
                    // option.setAttribute('data-import-unit-material-unit-price', parseFloat(this_conversion.import_price) / parseFloat(this_conversion.material_count) );
                    option.setAttribute('data-import-unit-import-price', this_conversion.import_price);
                    option.setAttribute('data-import-unit-import-count', this_conversion.import_count);
                    option.setAttribute('data-import-unit-material-count', this_conversion.material_count);
                    
                    // option.setAttribute('data-import-unit-import-count', parseFloat(this_conversion.import_price) / parseFloat(this_conversion.material_count));
                    // option.setAttribute('data-import-unit-material-unit-price', parseFloat(this_conversion.import_price) / parseFloat(this_conversion.material_count) );


                    import_unit_select.appendChild(option);
                }
                import_unit.change();
            },
            error: function(response) {
                // console.log(response);
            }
        });
        return true;
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
                    search: {supplier_id: $("#supplier").val()}
                }
            ),
            success: function(response) {
                const materials = response["data"]
                const select = document.createElement("select");
                
                select.classList = "ui search selection dropdown fluid";
                            

                select.appendChild(get_empty_option());

                for(var i = 0; i < materials.length; i++){
                    const this_material = materials[i];
                    const option = document.createElement("option");
                    option.value = this_material.material_id;
                    option.innerHTML = this_material.material_name;
                    option.setAttribute('data_unit_price', this_material.material_unit_price);
                    select.appendChild(option);
                    select.onchange = "set_import_conversion_select(this.id)";
                }
                material_select = select;
            },
            error: function(response) {
                // console.log(response);
            }
        });
        return true;
    }
    // function set_material_set_select(){
    //     return $.ajax({
    //         type: "POST",
    //         url: "/material_sets/queryData",
    //         contentType: "application/json",
    //         dataType: "json",
    //         // beforeSend: showLoading,
    //         // complete: hideLoading,
    //         data: JSON.stringify(
    //             {
    //                 search: {supplier_id: $("#supplier").val()}
    //             }
    //         ),
    //         success: function(response) {
    //             const sets = response["data"]
    //             const select = document.createElement("select");
                
    //             select.classList = "ui search selection dropdown";

    //             const option = document.createElement("option");
    //             option.value = "";
    //             option.innerHTML = "";
    //             select.appendChild(option);

    //             for(var i = 0; i < sets.length; i++){
    //                 const this_set = sets[i];
    //                 const option = document.createElement("option");
    //                 option.value = this_set.set_id;
    //                 option.innerHTML = this_set.set_name;
    //                 option.setAttribute('data-material-count', this_set.material_count);
    //                 option.setAttribute('data-material-id', this_set.material_id);
    //                 select.appendChild(option);
    //             }
    //             material_set_select = select;
    //         },
    //         error: function(response) {
    //             // console.log(response);
    //         }
    //     });
    //     return true;
    // }

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
    function get_empty_option(){
        const empty_option = document.createElement("option");
        empty_option.innerHTML = "請選擇";
        empty_option.value = "";
        return empty_option;
    }
    function import_unit_changed(){
        const option = $(this).find("option:selected");
        const tr = $(this).closest("tr");
        const import_count_input = tr.find("[data-import-count]");
        const unit_price_input = tr.find("[data-unit-price]");
        const material_count_input = tr.find("[data-amount]");
        const total_price_label = tr.find("[data-total-price]");

        


        //     return;

        const material_id = option.data("material-id")
        const import_count = option.data("import-unit-import-count");
        const material_count = option.data("import-unit-material-count");
        const material_price = option.data("import-unit-import-price");
        const material_unit_price = parseFloat(material_price) / parseFloat(material_count);

        if(option.data('import-unit') == undefined){
            import_count_input.parent().addClass("disabled");
            import_count_input.val(0).change();
            import_count_input.attr( "step", 0);
            unit_price_input.val(0);
            // material_count_input.val(0);
            // total_price_label.html("NaN");
        } else {
            import_count_input.parent().removeClass("disabled");
            import_count_input.val(import_count).change();
            import_count_input.attr( "step", import_count);
            unit_price_input.val(material_unit_price.toFixed(2));

        }



    }

    function import_count_changed(){
        const tr = $(this).closest("tr");
        const data_material_count = tr.find("[data-material-count]");
        const import_count = tr.find("[data-import-count]");
        const conversion_ratio = tr.find("[data-import-unit] option:selected").data('import-unit-conversion-ratio');
        const material_count = parseFloat(conversion_ratio) * parseFloat(import_count.val());
        const material_amount_input = tr.find("[data-amount]");
        material_amount_input.val(material_count.toFixed(2)).change();
    }

    

    async function set_row_dropdowns(){
        await set_material_select();
        // await set_import_conversion_select();
        $("#add_item_btn").trigger('click');


    }



});
    


    

</script>
@endsection