@extends('layouts.app')

@section('custom_css')
<style>
    /* tr{
        border: 1px solid black;
    } */
    #thisTable tr{
        cursor:pointer;
    }
</style>
@endsection
@section('content')

<h3 class="ui block header" style="position:inline-block;">
    庫存盤點維護
</h3>

<div class="ui vertical segment">
    <div class="ui grid" style="margin-bottom: 1rem;">
        <div class="two column row">
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
                <a href="/adjustments/create" class="ui secondary button">庫存調整</a>
                <button id="delete_btn" class="ui button negative">刪除</button>
            </div>
        </div>
    </div>

    <table id="thisTable" class="ui celled table" style="width:100%">
        <thead>
            <tr>
                <th></th>
                <th></th>
                <th>單據編號</th>
                {{-- <th>付款方式</th> --}}
                {{-- <th>付款狀態</th> --}}
                <th>單據日期</th>
                <th>備註</th>
            </tr>
        </thead>
    </table>
    <div class="ui message">
        <div class="header">
            <pre style="text-align: center;">滑鼠左鍵點一下表格資料列查看明細</pre>
        </div>
    </div>
    <div id="infoModal" class="ui modal large">
        <i class="close icon"></i>
        <div id="modal_title" class="header">
            庫存盤點明細
        </div>
        <div class="content">
            <div class="ui form">
                <div class=" fields">
                    <div class="eight wide field">
                        <label>單據編號</label>
                        <span id="adjustment_no">PR2016101001</span>
                    </div>
                    <div class="eight wide field">
                        <label>單據日期</label>
                        <span id="voucher_date">2021-07-01</span>
                    </div>
                </div>
            </div>
            <table id="adjustment_table" class="ui celled table">
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

    $('#year_month_select').dropdown();
    $('#year_month_select').change(function(){
        data_table.ajax.reload(hideLoading);
    })
    //bind table
    let data_table = $('#thisTable').DataTable({
        ajax: {
            url: "/adjustments/queryData",
            dataSrc: 'data',
            data: function(d){
                const search = {payment_type: "monthly", voucher_year_month: $('#year_month_select').val()}
                d.search = search;
                // const order = {};
                // d.order = order;
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
            { data: "adjustment_no", searchable:true, className: 'dt-body-center dt-head-center'},
            // { data: "payment_type_text", orderable:false, searchable:false, className: 'dt-body-center dt-head-center'},
            // { data: "is_paid_text", searchable:false, className: 'dt-body-center dt-head-center'},
            { data: "voucher_date", searchable:true, className: 'dt-body-center dt-head-center'},
            { data: "note", searchable: true, className: 'dt-body-center dt-head-center'},

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
                    "adjustment_ids": checked_row_id,
                };
                $.ajax({
                    type: "POST",
                    url: "/adjustments/delete",
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

    $('#thisTable tbody').on('click', 'td', function () {
        if($(this).find("input:checkbox").length == 0){
            var data = data_table.row($(this).closest("tr")).data();
            if(data["id"] == undefined)
                return;
            load_info(data["id"]);
            $('#infoModal').modal("show");
        } 
    } );

    function set_info_table(data){
        const table_name = "adjustment_table";
        const columns = ["#", "material_name_and_no", "material_unit", "amount", "unit_price", "adjustment_item_total"];
        const body = document.getElementById(table_name).getElementsByTagName('tbody')[0];
        let row_number = 0;
        $("#"+table_name+" tbody").html('');

        for(let i = 0; i < data.length; i++){
            const tr = document.createElement("tr");

            row_number += 1;
            this_row_data = data[i];
            for(let j = 0; j < columns.length; j++){
                const thisColumn = columns[j];
                const td = document.createElement("td");

                if(thisColumn == "#"){
                    td.appendChild(document.createTextNode(row_number));
                } else{
                    td.appendChild(document.createTextNode(this_row_data[columns[j]]));
                } 
                tr.appendChild(td);
            }
            $("#"+table_name+" tbody").append(tr);
        }
    }

    function load_info(adjustment_id){
        return $.ajax({
            type: "POST",
            url: "/adjustments/queryAdjustmentWithItems",
            contentType: "application/json",
            dataType: "json",
            beforeSend: showLoading,
            complete: hideLoading,
            data: JSON.stringify({"search": {"adjustment_id": adjustment_id}}),
            success: function(response) {
                if(response["data"].length > 0){
                    const first_row = response["data"][0];
                    $("#adjustment_no").html(first_row["adjustment_no"]);
                    $("#voucher_date").html(first_row["voucher_date"]);
                    set_info_table(response["data"]);
                }
            },
            error: function(response) {
                // console.log(response);
            }
        });
    }
});
</script>
@endsection