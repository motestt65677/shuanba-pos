@extends('layouts.app')

@section('custom_css')
<style>
    /* tr{
        border: 1px solid black;
    } */
</style>
@endsection
@section('content')

<h3 class="ui block header" style="position:inline-block;">
    廠商進貨分析
</h3>

<div class="ui vertical segment">
    <div class="ui grid" style="margin-bottom: 1rem;">
        <div class="four column row">
            <div class="left floated column">
                <select id="year_month_select" class="ui search selection dropdown">
                    @foreach($yearMonthSelect as $item)
                        @if($item == $nowMonthYear)
                            <option value="{{$item}}" selected>{{$item}}</option>
                        @elseif($item < $nowMonthYear)
                            <option value="{{$item}}">{{$item}}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="right floated column" style="text-align:right;">
                <button id="paid" class="ui button">單據付款</button>
                <button id="return" class="ui button ">單據退貨</button>
                <button id="delete_btn" class="ui button negative">刪除</button>
            </div>
        </div>
    </div>

    <table id="thisTable" class="display" style="width:100%">
        <thead>
            <tr>
                <th></th>
                <th></th>
                <th>單據編號</th>
                <th>廠商編號</th>
                <th>廠商名稱</th>
                <th>付款方式</th>
                <th>付款狀態</th>
                <th>單據日期</th>
                <th>應付金額</th>
            </tr>
        </thead>
    </table>
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

    $('#year_month_select').dropdown();
    $('#year_month_select').change(function(){
        data_table.ajax.reload(hideLoading);
    })
    //bind table
    let data_table = $('#thisTable').DataTable({
        ajax: {
            url: "/purchases/queryPurchases",
            dataSrc: 'data',
            data: function(d){
                const search = {payment_type: "monthly", voucher_year_month: $('#year_month_select').val()}
                d.search = search;
                const order = {is_paid: "asc"};
                d.order = order;
            },
            // beforeSend: showLoading,
            // data:{
            //     search: {
            //         "payment_type": "monthly", 
            //         "year_month": $('#year_month_select').val()
            //         },
            //     order: {"is_paid": "asc"}
            // },
            type: "POST",
            beforeSend: showLoading,
            complete: hideLoading
        },
        columns: [
            { 
                data: null,
                orderable: false,
                defaultContent: "<input type='checkbox'>",
                width: "3%"
            },
            { data: null, orderable:false, className: 'dt-body-center dt-head-center'}, 
            { data: "purchase_no", className: 'dt-body-center dt-head-center'},
            { data: "supplier_no", className: 'dt-body-center dt-head-center'},
            { data: "supplier_name", className: 'dt-body-center dt-head-center'},
            { data: "payment_type_text", orderable:false, searchable:false, className: 'dt-body-center dt-head-center'},
            { data: "is_paid_text", searchable:false, className: 'dt-body-center dt-head-center'},
            { data: "voucher_date", searchable:false, className: 'dt-body-center dt-head-center'},
            { data: "total", searchable:false, className: 'dt-body-center dt-head-center'},

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
        data_table.column(1, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
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
        }
    });


    $("#return").on('click', function(event){
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

        if(checked_row_id.length == 1){
            window.location.href = "/purchase_returns/create?purchase-id=" + checked_row_id[0];
        } else {
            alert('請選擇一項單據進行退貨');
        }
    });

    $("#delete_btn").click(function(){
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
            if(confirm("確定要刪除?")){

                let data = {
                    "purchase_ids": checked_row_id,
                };
                $.ajax({
                    type: "POST",
                    url: "/purchases/delete",
                    contentType: "application/json",
                    dataType: "json",
                    beforeSend: showLoading,
                    complete: hideLoading,
                    data: JSON.stringify(data),
                    success: function(response) {
                        if(response["error"].length > 0){
                            if(response["error"].length > 0){
                                let message = "錯誤訊息 \r\n";
                                for(let i = 0; i < response["error"].length; i++){
                                    message += response["error"][i] + "\r\n";
                                }
                                alert(message);
                            }
                        } else {
                            alert("刪除成功");
                        }
                        data_table.ajax.reload(hideLoading);
                    },
                    error: function(response) {
                        // console.log(response);
                    }
                });
                }

        } 
    })
});
</script>
@endsection