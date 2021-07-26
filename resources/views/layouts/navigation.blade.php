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
                <img src="/image/chef-icon-white.png" alt="" style="color:gray; display: block; margin-left: auto; margin-right: auto; width: 50%;">
            </div>
            <div id="nav-body" style="overflow-x:hidden;"> 
                {{-- <div>
                    <a href="/purchases/create" class="link-container">
                        <i class="fas fa-shipping-fast"></i>
                        <span class="link-title" >廠商進貨維護</span>
                    </a>
                </div> --}}
                @if($appUser->role == "admin" || in_array("儀表板", $appRoles))
                <div>
                    <a href="/dashboard" class="link-container {{ Request::segment(1) === 'dashboard' ? 'current' : null }}">
                        <i class="fas fa-home fa-fw"></i>
                        <span class="link-title" >儀表板</span>
                    </a>
                </div>
                @endif

                @if($appUser->role == "admin" || in_array("廠商進貨分析", $appRoles))
                <div>
                    <a href="/purchases/index" class="link-container {{ Request::segment(1) === 'purchases' ? 'current' : null }}">
                        <i class="fas fa-truck fa-fw"></i>
                        <span class="link-title" >廠商進貨分析</span>
                    </a>
                </div>
                @endif

                @if($appUser->role == "admin" || in_array("廠商退貨分析", $appRoles))
                <div>
                    <a href="/purchase_returns/index" class="link-container {{ Request::segment(1) === 'purchase_returns' ? 'current' : null }}">
                        <i class="fas fa-dolly fa-fw"></i>
                        <span class="link-title" >廠商退貨分析</span>
                    </a>
                </div>
                @endif

                @if($appUser->role == "admin" || in_array("材料進貨分析", $appRoles))
                <div>
                    <a href="/purchase_items/index" class="link-container {{ Request::segment(1) === 'purchase_items' ? 'current' : null }}">
                        <i class="fas fa-boxes fa-fw"></i>
                        <span class="link-title" >材料進貨分析</span>
                    </a>
                </div>
                @endif

                @if($appUser->role == "admin" || in_array("進耗存別關帳", $appRoles))
                <div>
                    <a href="/closings/index" class="link-container {{ Request::segment(1) === 'closings' ? 'current' : null }}">
                        <i class="fas fa-calculator fa-fw"></i>
                        <span class="link-title" >進耗存別關帳</span>
                    </a>
                </div>
                @endif

                @if($appUser->role == "admin" || in_array("單據異動分析", $appRoles))
                <div>
                    <a href="/transactions/index" class="link-container {{ Request::segment(1) === 'transactions' ? 'current' : null }}">
                        <i class="fas fa-history fa-fw"></i>
                        <span class="link-title" >單據異動分析</span>
                    </a>
                </div>
                @endif

                @if($appUser->role == "admin" || in_array("廠商管理", $appRoles))
                <div>
                    <a href="/suppliers/index" class="link-container {{ Request::segment(1) === 'suppliers' ? 'current' : null }}">
                        <i class="far fa-address-card fa-fw"></i>
                        <span class="link-title" >廠商管理</span>
                    </a>
                </div>
                @endif

                @if($appUser->role == "admin" || in_array("材料管理", $appRoles))
                <div>
                    <a href="/materials/index" class="link-container {{ Request::segment(1) === 'materials' ? 'current' : null }}">
                        <i class="fas fa-carrot fa-fw"></i>
                        <span class="link-title" >材料管理</span>
                    </a>
                </div>
                @endif

                @if($appUser->role == "admin" || in_array("帳號管理", $appRoles))
                <div>
                    <a href="/users/index" class="link-container {{ Request::segment(1) === 'users' ? 'current' : null }}">
                        <i class="fas fa-users fa-fw"></i>
                        <span class="link-title" >帳號管理</span>
                    </a>
                </div>
                @endif

                @if($appUser->role == "admin" || in_array("銷貨產品管理(Qlieer)", $appRoles))
                <div>
                    <a href="/products/index" class="link-container {{ Request::segment(1) === 'products' ? 'current' : null }}">
                        <i class="fas fa-box fa-fw"></i>
                        <span class="link-title" >銷貨產品管理(Qlieer)</span>
                    </a>
                </div>
                @endif

                @if($appUser->role == "admin" || in_array("進貨產品管理(Google)", $appRoles))
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
                @if($appUser->role == "admin" || in_array("維護工具", $appRoles))
                <div>
                    <a href="/mis/index" class="link-container {{ Request::segment(1) === 'mis' ? 'current' : null }}">
                        <i class="fas fa-tools fa-fw"></i>
                        <span class="link-title" >維護工具</span>
                    </a>
                </div>
                @endif
                
            </div>
            <div id="nav-footer" class="container" style="position: absolute; bottom: 1em; color: white;">
                <div class="dropup" style="width: 100%;">
                    <button class="dropbtn">
                        <span id="navigation_name" style="margin:1rem; display:none;">{{$appUser->name}}</span><i class="fas fa-angle-right fa-fw"></i>
                    </button>
                    <div class="dropup-content">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-responsive-nav-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                            {{ __('登出') }}
                            </x-responsive-nav-link>
                        </form>
                    </div>
                </div>
            </div>
            
        </div>
        <script>

        </script>
        
    </body>
</html>
