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
<div style="text-align:right; margin-bottom: 1rem;">
    <button id="manual_closing_btn" class="ui button ">手動關帳</button>
</div>


<div style="height:85vh;" class="ui segments">
    <input type="hidden" id="closing_id" value="">
    <div class="ui segment" style="width: 100%; max-height: 50rem;  display:inline-block; ">
        <table id="thisTable" class="ui celled table" style="width:100%">
            <thead>
                <tr>
                    {{-- <th></th> --}}
                    <th>統計年月</th>
                    <th>關帳時間</th>
                </tr>
            </thead>
        </table>
    </div>

    <div class="ui secondary segment" style="width: 100%; display:inline-block; ">
        <div class="ui warning message">
            <div class="header">
                <pre style="text-align: center;"> 期初 + 進貨 - 退貨 - 銷貨 = 期末</pre>
            </div>
        </div>
        <table id="closing_item_table" class="ui structured celled table" style="width:100%">
            <thead>
                <tr>
                    <th rowspan="2">材料</th>
                    <th colspan="5" style="text-align:center;">數量</th>
                    <th colspan="5" style="text-align:center;">金額</th>
                </tr>
                <tr>
                    <th>期初</th>
                    <th>進貨</th>
                    <th>退貨</th>
                    <th>銷貨</th>
                    <th>期末</th>
                    {{-- <th>期初數量</th>
                    <th>進貨數量</th>
                    <th>退貨數量</th>
                    <th>銷貨數量</th>
                    <th>期末數量</th> --}}
                    <th>期初</th>
                    <th>進貨</th>
                    <th>退貨</th>
                    <th>銷貨</th>
                    <th>期末</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<div class="ui modal">
    <i class="close icon"></i>
    <div class="header">
        手動關帳
    </div>
    <div class="content">
        <div class="ui grid">
            <div class="sixteen wide column">
                <h3>關帳月份</h3>
                <div class="ui fluid search selection dropdown">
                    <input id="year_month_select" type="hidden" name="year_month">
                    <i class="dropdown icon"></i>
                    <div class="default text">請選擇</div>
                    <div class="menu">
                        @foreach($yearMonth as $data)
                            <div class="item" data-value="{{$data}}">{{$data}}</div>
                        @endforeach
                    </div>
                </div>
            </div>
            {{-- <div class="sixteen wide column">
                <button class="fluid ui secondary button">Fits container</button>
            </div> --}}
        </div>
    </div>
    <div class="actions">
        {{-- <div class="ui button">取消</div> --}}
        <div id="closing_confirm_btn" class="ui button">確認關帳</div>
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


    init();

    // data_table.on( 'order.dt search.dt', function () {
    //     data_table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
    //         cell.innerHTML = i+1;
    //     } );
    // } ).draw();
    



});
let closing_item_table;
let data_table;
let closings;
let closing_table_data;

$('.ui.dropdown').dropdown({});




$("#manual_closing_btn").click(function(){
    $('.ui.modal').modal('show');
});
$("#closing_confirm_btn").click(function(){
    $.ajax({
        type: "POST",
        url: "/closings/create",
        contentType: "application/json",
        dataType: "json",
        // async: false,
        // beforeSend: showLoading,
        // complete: hideLoading,
        data: JSON.stringify(
            {
                year_month: $("#year_month_select").val()
            }
        ),
        success: function(response) {
            if(response["status"] == "200"){
                alert("關帳成功");
                window.location.href = "/closings/index";
            } else {
                alert('關帳失敗');
            }
        },
        error: function(response) {
            // console.log(response);
        }
    });
});


function bind_closing_table(){
    let data_table = $('#thisTable').DataTable({
        data: closing_table_data,
        columns: [
            // { data: null, orderable:false, width: "3%"}, 
            { data: "year_month", orderable:false},
            { data: "created_at", orderable:false},
        ],
        order: [[0, "desc"],[1, "desc"]],
        paging: true,
        pageLength: 5,
        lengthChange: false,
        aaSorting: [],
        searching: false,
        info: false,
        language: {
            url: "/DataTables/localisation/zh_TW.json",
            zeroRecords: "查無紀錄"
        },
        // paging: true,
        fixedHeader: true
    });
    return data_table;
}

function bind_item_table(year_month, closing_id){
    const closing_items = closings[year_month][closing_id]["items"];
    if ( $.fn.dataTable.isDataTable( '#closing_item_table' ) ) {
        closing_item_table = $('#closing_item_table').DataTable().clear().rows.add(closing_items).draw();
    } else {
        closing_item_table = $('#closing_item_table').DataTable({
            data: closing_items,
            columns: [
                // { data: null, orderable:false, width: "3%"}, 
                { data: "material_name_and_no"},
                { data: "starting_count"},
                { data: "purchase_count"},
                { data: "purchase_return_count"},
                { data: "order_count"},
                { data: "closing_count"},

                { data: "starting_total"},
                { data: "purchase_total"},
                { data: "purchase_return_total"},
                { data: "order_cost"},
                { data: "closing_total"}

                // { data: "purchase_unit_price", orderable:false},
                // { data: "order_total", orderable:false},
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
            searching: true,
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
    }

    return closing_item_table;
}

function get_closing_data(){
    return $.ajax({
        type: "POST",
        url: "/closings/queryClosingWithItems",
        contentType: "application/json",
        dataType: "json",
        // async: false,
        // beforeSend: showLoading,
        // complete: hideLoading,
        data: JSON.stringify(
            {
                search: {},
                order: {}
            }
        ),
        success: function(response) {
            closings = response["data"];
            closing_table_data = [];
            for (let year_month in response["data"]) {
                this_year_month = response["data"][year_month];
                for (let closing_id in this_year_month) {
                    this_closing = this_year_month[closing_id];
                    const closing = {
                        "closing_id": closing_id,
                        "year_month": this_closing["closing_year_month"],
                        "created_at" : this_closing["closing_created_at"]
                    };
                    closing_table_data.push(closing);
                }
            }
        },
        error: function(response) {
            // console.log(response);
        }
    });
    return true;
}
async function init(){
    await get_closing_data();
    data_table = bind_closing_table();
    // bind_item_table();
    data_table.on( 'click', 'tr', function () {
        const data = data_table.row( this ).data();
        $("#closing_id").val(data["closing_id"]);
        // console.log(data_table.row( this ).data());
        // closing_item_table.ajax.reload();
        bind_item_table(data["year_month"],data["closing_id"]);

        data_table.$('tr.active').removeClass('active');
        $(this).addClass('active');
    });
}
</script>
@endsection