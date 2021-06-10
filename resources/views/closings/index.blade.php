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

<div class="ui vertical segment">
    <table id="thisTable" class="display" style="width:100%">
        <thead>
            <tr>
                <th></th>
                <th>統計年月</th>
                <th>關帳時間</th>
            </tr>
        </thead>
    </table>
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

    
    //bind table
    let data_table = $('#thisTable').DataTable({
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
            { data: null, orderable:false, className: 'dt-body-center dt-head-center', width: "3%"}, 
            { data: "year_month", orderable:false, className: 'dt-body-center dt-head-center'},
            { data: "created_at", orderable:false, className: 'dt-body-center dt-head-center'},
        ],
        // paging: false,
        // bPaginate: false,
        bLengthChange: false,
        pageLength: 3,
    // "bFilter": true,
    // "bInfo": false,
    // "bAutoWidth": false
        // paging: true,
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

    data_table.on( 'click', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
            data_table.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
    } );
   
});
</script>
@endsection