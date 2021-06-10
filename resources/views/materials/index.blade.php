@extends('layouts.app')

@section('custom_css')
<style>
    tr{
        cursor:pointer;
    }
</style>
@endsection
@section('content')

<h3 class="ui block header" style="position:inline-block;">
    材料維護
</h3>
<div style="text-align:right;">
    <a id="submit" class="ui button primary submit" href="/materials/create">新增材料</a>
    <button id="delete_btn" class="ui button negative">刪除</button>
</div>
<div class="ui vertical segment">
    <table id="thisTable" class="display" style="width:100%">
        <thead>
            <tr>
                <th></th>
                <th></th>
                <th>廠商</th>
                <th>材料編號</th>
                <th>材料名稱</th>
                <th>單位</th>
                <th>單價</th>
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

    
    //bind table
    let data_table = $('#thisTable').DataTable({
        ajax: {
            url: "/materials/queryData",
            dataSrc: 'data',
            // data: function(d){
            //     const search = {payment_type: "monthly", voucher_year_month: $('#year_month_select').val()}
            //     d.search = search;
            //     const order = {is_paid: "asc"};
            //     d.order = order;
            // },
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
            { data: "supplier_name_and_no", orderable:false, className: 'dt-body-center dt-head-center'},
            { data: "material_no", orderable:false, className: 'dt-body-center dt-head-center'},
            { data: "material_name", orderable:false, searchable:false, className: 'dt-body-center dt-head-center'},
            { data: "material_unit", orderable:false, searchable:false, className: 'dt-body-center dt-head-center'},
            { data: "material_unit_price", orderable:false, searchable:false, className: 'dt-body-center dt-head-center'},
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

    data_table.on( 'click', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
            data_table.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
    } );

    $('#thisTable tbody').on('dblclick', 'tr', function () {
        var data = data_table.row( this ).data();
        window.location.href = "/materials/" + data["material_id"] + "/edit";
    } );
    
    $("#delete_btn").click(function(){
        const all_data = data_table.rows().data();
        let checked_row_id = [];
        data_table.rows().every(function(index, element) {
            let row = $(this.node());
            //eq(col # of checkbox)
            let checkbox = row.find('td').eq(0).children("input:checkbox");
            if(checkbox.prop("checked")){
                checked_row_id.push(all_data[index]["material_id"]);
            }
        });

        if(checked_row_id.length > 0){
            if(confirm("確定要刪除?")){

                let data = {
                    "material_ids": checked_row_id,
                };
                $.ajax({
                    type: "POST",
                    url: "/materials/delete",
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