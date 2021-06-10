@extends('layouts.app')

@section('custom_css')
<style>
    tr{
        cursor: pointer;
    }

</style>
@endsection
@section('content')

<h3 class="ui block header" style="position:inline-block;">
    進耗存別關帳
</h3>
<div style="height:85vh;">
    <input type="hidden" id="closing_id" value="">
    <div style="width: 25%; height:100%; overflow-y: scroll; display:inline-block;">
        <table id="thisTable" class="display" style="width:100%">
            <thead>
                <tr>
                    {{-- <th></th> --}}
                    <th>統計年月</th>
                    <th>關帳時間</th>
                </tr>
            </thead>
        </table>
    </div>
    <div style="width: 73%; height:100%; overflow-y: scroll; display:inline-block;">
        <table id="closing_item_table" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>材料</th>
                    <th>進貨數量</th>
                    <th>進貨金額</th>
                    <th>銷貨數量</th>
                    <th>銷貨金額</th>
                    <th>銷貨成本</th>
                    <th>期末數量</th>
                    <th>期末金額</th>
                </tr>
            </thead>
        </table>
    </div>
</div>


<div class="ui vertical segment">
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

    let closing_item_table;
    let data_table;
    init();

    // data_table.on( 'order.dt search.dt', function () {
    //     data_table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
    //         cell.innerHTML = i+1;
    //     } );
    // } ).draw();
    data_table.on( 'click', 'tr', function () {
        $("#closing_id").val(data_table.row( this ).data()["id"]);
        closing_item_table.ajax.reload();
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
            data_table.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
    });

    function bind_closing_table(){
        data_table = $('#thisTable').DataTable({
            ajax: {
                url: "/closings/queryClosings",
                dataSrc: 'data',
                data: function(d){
                    const search = {}
                    d.search = search;
                    const order = {year_month: "desc"};
                    d.order = order;
                },
                type: "POST",
                beforeSend: showLoading,
                complete: hideLoading
            },
            columns: [
                // { data: null, orderable:false, className: 'dt-body-center dt-head-center', width: "3%"}, 
                { data: "year_month", orderable:false, className: 'dt-body-center dt-head-center'},
                { data: "created_at", orderable:false, className: 'dt-body-center dt-head-center'},
            ],
            paging: false,
            aaSorting: [],
            searching: false,
            info: false,
            language: {
                url: "/DataTables/localisation/zh_TW.json",
                zeroRecords: "查無紀錄"
            },
            // paging: true,
            fixedHeader: true,
            // fnInitComplete: function(oSettings, json) {
            //     $("#closing_id").val(json["data"][0]["id"]);

            //     bind_item_table();
            // }
        });
        return data_table;
    }

    function bind_item_table(){
        closing_item_table = $('#closing_item_table').DataTable({
            ajax: {
                url: "/closing_items/queryItems",
                dataSrc: 'data',
                data: function(d){
                    const search = {closing_id: $("#closing_id").val()}
                    d.search = search;
                    const order = {};
                    d.order = order;
                },
                type: "POST",
                beforeSend: showLoading,
                complete: hideLoading
            },
            columns: [
                // { data: null, orderable:false, className: 'dt-body-center dt-head-center', width: "3%"}, 
                { data: "material_name_and_no", orderable:false, className: 'dt-body-center dt-head-center'},
                { data: "purchase_count", orderable:false, className: 'dt-body-center dt-head-center'},
                { data: "purchase_total", orderable:false, className: 'dt-body-center dt-head-center'},
                { data: "order_count", orderable:false, className: 'dt-body-center dt-head-center'},
                { data: "order_total", orderable:false, className: 'dt-body-center dt-head-center'},
                { data: "order_cost", orderable:false, className: 'dt-body-center dt-head-center'},
                { data: "closing_count", orderable:false, className: 'dt-body-center dt-head-center'},
                { data: "closing_total", orderable:false, className: 'dt-body-center dt-head-center'}
            ],
            paging: false,
            // bPaginate: false,
            // bLengthChange: false,
            // pageLength: 3,
            // "bFilter": true,
            // "bInfo": false,
            // "bAutoWidth": false
            // paging: true,
            aaSorting: [],
            searching: false,
            info: false,
            language: {
                url: "/DataTables/localisation/zh_TW.json",
                zeroRecords: "查無紀錄"
            },
            // paging: true,
            fixedHeader: true,
            fnInitComplete: function(oSettings, json) {
                setTimeout(function(){$("#thisTable tr")[1].click()}, 500);
            }
        });
        return closing_item_table;
    }
    function init(){
        bind_closing_table();
        bind_item_table();
    }
});
</script>
@endsection