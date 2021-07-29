<html>
    <head>
        <style>
            .current{
                color: #f1f1f1 !important;
            }
        </style>
    </head>
    <body>
        <div id="my-side-nav" class="sidenav container" style="width:75px; overflow: visible;">
            <a  class="nav-toggle-btn" onclick="toggleNav()"><i id="nav-btn-arrow" class="fas fa-chevron-right" style="color: gray;"></i></a>
        
            <div id="nav-header" class="mb-5" style="">
                <img src="/image/chef-icon-white.png" alt="" style="color:gray; display: block; margin-left: auto; margin-right: auto; width: 30%;">
            
            </div>
            <div id="navigation_branch_container" style="text-align: center; padding:1rem; display:none;  ">
            @if($appUser->role == "mis" || $appUser->role == "admin" )
                <select name="" id="app_branch">
                @foreach($appBranches as $branch)
                <option value="{{$branch->id}}" @if($branch->id == $appUser->branch_id) selected @endif>{{$branch->name}}</option>
                @endforeach
                </select>
            @else
                <h3 style="text-align: center;">{{$appBranches[0]["name"]}}</h3>
            @endif
            </div>
            <div id="nav-body" style="overflow-x:hidden;"> 
                {{-- <div>
                    <a href="/purchases/create" class="link-container">
                        <i class="fas fa-shipping-fast"></i>
                        <span class="link-title" >廠商進貨維護</span>
                    </a>
                </div> --}}
                @if($appUser->role == "mis" ||$appUser->role == "admin" || in_array("儀表板", $appRoles))
                <div>
                    <a href="/dashboard" class="link-container {{ Request::segment(1) === 'dashboard' ? 'current' : null }}">
                        <i class="fas fa-home fa-fw"></i>
                        <span class="link-title" >儀表板</span>
                    </a>
                </div>
                @endif

                @if($appUser->role == "mis" ||$appUser->role == "admin" || in_array("廠商進貨分析", $appRoles))
                <div>
                    <a href="/purchases/index" class="link-container {{ Request::segment(1) === 'purchases' ? 'current' : null }}">
                        <i class="fas fa-truck fa-fw"></i>
                        <span class="link-title" >廠商進貨分析</span>
                    </a>
                </div>
                @endif

                @if($appUser->role == "mis" ||$appUser->role == "admin" || in_array("廠商退貨分析", $appRoles))
                <div>
                    <a href="/purchase_returns/index" class="link-container {{ Request::segment(1) === 'purchase_returns' ? 'current' : null }}">
                        <i class="fas fa-dolly fa-fw"></i>
                        <span class="link-title" >廠商退貨分析</span>
                    </a>
                </div>
                @endif

                @if($appUser->role == "mis" ||$appUser->role == "admin" || in_array("材料進貨分析", $appRoles))
                <div>
                    <a href="/purchase_items/index" class="link-container {{ Request::segment(1) === 'purchase_items' ? 'current' : null }}">
                        <i class="fas fa-boxes fa-fw"></i>
                        <span class="link-title" >材料進貨分析</span>
                    </a>
                </div>
                @endif

                @if($appUser->role == "mis" ||$appUser->role == "admin" || in_array("進耗存別關帳", $appRoles))
                <div>
                    <a href="/closings/index" class="link-container {{ Request::segment(1) === 'closings' ? 'current' : null }}">
                        <i class="fas fa-calculator fa-fw"></i>
                        <span class="link-title" >進耗存別關帳</span>
                    </a>
                </div>
                @endif

                @if($appUser->role == "mis" ||$appUser->role == "admin" || in_array("單據異動分析", $appRoles))
                <div>
                    <a href="/transactions/index" class="link-container {{ Request::segment(1) === 'transactions' ? 'current' : null }}">
                        <i class="fas fa-history fa-fw"></i>
                        <span class="link-title" >單據異動分析</span>
                    </a>
                </div>
                @endif

                @if($appUser->role == "mis" ||$appUser->role == "admin" || in_array("單據異動分析", $appRoles))
                <div>
                    <a href="/orders/qlieerImport" class="link-container {{ Request::segment(1) === 'orders' ? 'current' : null }}">
                        <i class="fas fa-file-import fa-fw"></i>
                        <span class="link-title" >Qlieer產品報表匯入</span>
                    </a>
                </div>
                @endif

                <div class="ui divider"></div>
                @if($appUser->role == "mis" ||$appUser->role == "admin" || in_array("廠商管理", $appRoles))
                <div>
                    <a href="/suppliers/index" class="link-container {{ Request::segment(1) === 'suppliers' ? 'current' : null }}">
                        <i class="far fa-address-card fa-fw"></i>
                        <span class="link-title" >廠商管理</span>
                    </a>
                </div>
                @endif

                @if($appUser->role == "mis" ||$appUser->role == "admin" || in_array("材料管理", $appRoles))
                <div>
                    <a href="/materials/index" class="link-container {{ Request::segment(1) === 'materials' ? 'current' : null }}">
                        <i class="fas fa-carrot fa-fw"></i>
                        <span class="link-title" >材料管理</span>
                    </a>
                </div>
                @endif

                @if($appUser->role == "mis" ||$appUser->role == "admin" || in_array("帳號管理", $appRoles))
                <div>
                    <a href="/users/index" class="link-container {{ Request::segment(1) === 'users' ? 'current' : null }}">
                        <i class="fas fa-users fa-fw"></i>
                        <span class="link-title" >帳號管理</span>
                    </a>
                </div>
                @endif

                @if($appUser->role == "mis" ||$appUser->role == "admin" || in_array("銷貨產品管理(Qlieer)", $appRoles))
                <div>
                    <a href="/products/index" class="link-container {{ Request::segment(1) === 'products' ? 'current' : null }}">
                        <i class="fas fa-box fa-fw"></i>
                        <span class="link-title" >銷貨產品管理(Qlieer)</span>
                    </a>
                </div>
                @endif

                @if($appUser->role == "mis" ||$appUser->role == "admin" || in_array("進貨產品管理(Google)", $appRoles))
                <div>
                    <a href="/imports/index" class="link-container {{ Request::segment(1) === 'imports' ? 'current' : null }}">
                        <i class="fas fa-box-open fa-fw"></i>
                        <span class="link-title" >進貨產品管理(Google)</span>
                    </a>
                </div>
                @endif

                {{-- <div>
                    <a href="/material_sets/index" class="link-container">
                        <i class="fas fa-file-import"></i>
                        <span class="link-title" >進貨單位換算</span>
                    </a>
                </div> --}}
                {{-- <div>
                    <a href="/products/index" class="link-container">
                        <i class="fas fa-carrot"></i>
                        <span class="link-title" >商品管理</span>
                    </a>
                </div> --}}
                @if($appUser->role == "mis" || in_array("維護工具", $appRoles))
                <div>
                    <a href="/mis/index" class="link-container {{ Request::segment(1) === 'mis' ? 'current' : null }}">
                        <i class="fas fa-tools fa-fw"></i>
                        <span class="link-title" >維護工具</span>
                    </a>
                </div>
                @endif
                
            </div>
            <form id="logout_form" method="POST" action="{{ route('logout') }}">
                @csrf
            </form>

            <div id="nav-footer" class="container" style="position: absolute; bottom: 0rem; color: white;">
                <div class="fluid ui bottom pointing dropdown button black">
                    <div style="text-align: center;">
                        <span id="navigation_name" style="margin:1rem; display:none;">{{$appUser->name}}</span><i class="fas fa-angle-right fa-fw"></i>
                    </div>
                    <div class="menu">
                        <div class="item" onclick="$('#changePasswordModal').modal({closable: false}).modal('show')">
                            變更密碼
                        </div>
                        <div onclick="event.preventDefault();$('#logout_form').submit();" class="item">
                            登出
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="changePasswordModal" class="ui modal">
                <i class="close icon"></i>
                <div class="header">
                    變更密碼
                </div>
                <div class="content">
                    <div id="nav_form" class="ui form">
                        <div class=" fields">
                            <div class="eight wide field">
                                <label>舊密碼</label>
                                <input id="old_password" type="password" value="">
                            </div>
                        </div>
                        <div class=" fields">
                            <div class="eight wide field">
                                <label>新密碼</label>
                                <input id="new_password" type="password" value="">
                            </div>
                        </div>
                        <div class=" fields">
                            <div class="eight wide field">
                                <label>新密碼確認</label>
                                <input id="new_password_check" type="password" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="actions">
                <div class="ui black deny button">
                    取消
                </div>
                <div id="changePasswordConfirm" class="ui primary button">
                    確定變更密碼
                </div>
                </div>
            </div>
        <script>

window.onload = function() {
    $('#app_branch').dropdown();
    $('.ui.dropdown').dropdown();

    $('#nav_form').form.settings.prompt.empty = "請填寫{name}";
    $('#nav_form').form.settings.prompt.number = "{name}應為數字";
    $('#nav_form').form.settings.prompt.minLength = '{name} 長度至少 {ruleValue} 碼以上';

    
    $('#nav_form').form({
        inline: true,
        fields: {
            old_password: ['empty', 'minLength[6]'],
            new_password: ['empty', 'minLength[6]'],
            new_password_check: ['empty', 'minLength[6]']
        }
    });


    $("#changePasswordConfirm").click(function(){
        $('#nav_form').form('validate form');
        if($('#nav_form').form('is valid')) {
            if($("#new_password").val() != $("#new_password_check").val()){
                alert("新密碼不一致");
                return;
            }
            let data = {
                "old_password": $("#old_password").val(),
                "new_password": $("#new_password").val()
            };
            $.ajax({
                type: "POST",
                url: "/users/changePassword",
                contentType: "application/json",
                dataType: "json",
                // beforeSend: showLoading,
                // complete: hideLoading,
                data: JSON.stringify(data),
                success: function(response) {
                    // console.log(response);
                    if(response["error"].length > 0){
                        error_message = "";
                        for(let i = 0 ; i < response["error"].length; i ++){
                            error_message += response["error"][i] + "\r\n";
                        }
                        alert(error_message);
                    } else {
                        alert("密碼變更完成，請用新的密碼重新登入");
                        $('#logout_form').submit();
                    }
                    // window.location.href = "/purchases/create";
                },
                error: function(response) {
                    // console.log(response);
                }
            });
        }
    })
};
        </script>
        
    </body>
</html>
