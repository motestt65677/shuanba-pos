@extends('layouts.app')

@section('custom_css')
<style>
    /* tr{
        border: 1px solid black;
    } */
</style>
@endsection
@section('content')
<h3 class="ui block header">單據異動分析</h3>
<div class="ui grid" style="margin-bottom: 1rem;">
    <div class="four column row">
        <div class="left floated column">
            
        </div>
    </div>
</div>
<table id="thisTable" class="display" style="width:100%">
    <thead>
        <tr>
            <th></th>
            <th>成本別</th>
            <th>單據編號</th>
            <th>單據日期</th>
            <th>原料</th>
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

    //bind table
    let data_table = $('#thisTable').DataTable({
        ajax: {
            url: "/transactions/queryData",
            dataSrc: 'data',
            data: function(d){
                // const search = {"voucher_date": $("#voucher_date").val()};
                // d.search = search;
                const order = {voucher_date:"desc"};
                d.order = order;
            },
            type: "POST",
            beforeSend: showLoading,
            complete: hideLoading
        },
        columns: [
            { data: null, orderable:false, className: 'dt-body-center dt-head-center'}, 
            { data: "type", orderable:true, searchable: true, className: 'dt-body-center dt-head-center'},
            { data: "no", orderable:true, searchable: true, className: 'dt-body-center dt-head-center'},
            { data: "voucher_date", orderable:true, searchable: true, className: 'dt-body-center dt-head-center'},
            { data: "material_name_and_no", orderable:true, searchable: true, className: 'dt-body-center dt-head-center'},
            { data: "amount", orderable:false, searchable: true, className: 'dt-body-center dt-head-center'},
            { data: "material_unit", orderable:true, searchable: false, className: 'dt-body-center dt-head-center'},
            { data: "unit_price", orderable:true, searchable: false, className: 'dt-body-center dt-head-center'},
            { data: "total", orderable:true, searchable: false, className: 'dt-body-center dt-head-center'},
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

});
</script>
@endsection