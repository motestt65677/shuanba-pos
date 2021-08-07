@extends('layouts.app')

@section('custom_css')
<style>
    /* tr{
        border: 1px solid black;
    } */
</style>
@endsection
@section('content')
<h3 class="ui block header">材料進貨分析</h3>
<div class="ui grid" style="margin-bottom: 1rem;">
    <div class="four column row">
        <div class="left floated column">
            <select id="material" class="ui search selection dropdown fluid">
                @foreach($materials as $item)
                    <option value="{{$item->id}}">{{$item->material_name_and_no}} </option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<table id="thisTable" class="ui celled table" style="width:100%">
    <thead>
        <tr>
            <th></th>
            <th>單據編號</th>
            {{-- <th>廠商編號</th> --}}
            {{-- <th>廠商名稱</th> --}}
            <th>廠商</th>
            <th>單據日期</th>
            {{-- <th>原料編號</th> --}}
            {{-- <th>原料名稱</th> --}}
            <th>材料</th>
            <th>數量</th>
            <th>庫存單位</th>
            <th>單價</th>
            <th>金額</th>
        </tr>
    </thead>
</table>
@endsection

@section('custom_js')
<script>
$(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('#material').dropdown(
        {        
            fullTextSearch: true,
            placeholder: false
        }
    );
    $('#material').change(function(){
        data_table.ajax.reload(hideLoading);
    })
    //bind table
    let data_table = $('#thisTable').DataTable({
        ajax: {
            url: "/purchases/queryPurchaseItemsWithSupplier",
            dataSrc: 'data',
            data: function(d){
                const search = {"material_id": $("#material").val()};
                d.search = search;
                const order = {"purchase_items.id": "asc"};
                d.order = order;
            },
            type: "POST",
            beforeSend: showLoading,
            complete: hideLoading
        },
        columns: [
            { data: null, orderable:false, className: 'dt-body-center dt-head-center'}, 
            { data: "purchase_no", orderable:false, className: 'dt-body-center dt-head-center'},
            // { data: "supplier_no", orderable:false, searchable: false, className: 'dt-body-center dt-head-center'},
            // { data: "supplier_name", orderable:false, searchable: false, className: 'dt-body-center dt-head-center'},
            { data: "supplier_name_and_no", orderable:false, searchable: false, className: 'dt-body-center dt-head-center'},


            { data: "voucher_date", className: 'dt-body-center dt-head-center'},
            // { data: "material_no", orderable:false, searchable: false, className: 'dt-body-center dt-head-center'},
            { data: "material_name_and_no", orderable:false, searchable: false, className: 'dt-body-center dt-head-center'},
            { data: "item_amount", orderable:false, className: 'dt-body-center dt-head-center'},
            { data: "material_unit", orderable:false, searchable: false, className: 'dt-body-center dt-head-center'},
            { data: "item_unit_price", orderable:false, className: 'dt-body-center dt-head-center'},
            { data: "item_total", orderable:false, className: 'dt-body-center dt-head-center'}
        ],
        paging: false,
        // searching: false,
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

    $("#paid").on('click', function(event){
        const all_data = data_table.rows().data();
        let checked_row_id = [];
        data_table.rows().every(function(index, element) {
            let row = $(this.node());
            //eq(col # of checkbox)
            let checkbox = row.find('td').eq(0).children("input:checkbox");
            if(checkbox.prop("checked")){
                checked_row_id.push(all_data[index]["id"]);
            }
        });

        if(checked_row_id.length > 0){
            $.ajax({
                type: "POST",
                url: "/purchases/paid",
                data: JSON.stringify({
                        ids: checked_row_id
                }),
                contentType: "application/json",
                dataType: "json",
                beforeSend: showLoading,
                complete: hideLoading,
                success: function(response) {
                    data_table.ajax.reload(hideLoading);
                }
            });
        } else {
        }

        
    });
});
</script>
@endsection