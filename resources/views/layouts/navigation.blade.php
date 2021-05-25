
<div id="my-side-nav" class="sidenav container" style="width:75px; overflow: visible;">
    <a  class="nav-toggle-btn" onclick="toggleNav()"><i id="nav-btn-arrow" class="fas fa-chevron-right" style="color: gray;"></i></a>

    <div id="nav-header" class="mb-5" style="">
        <img src="/image/chef-icon-white.png" alt="" style="color:gray; display: block; margin-left: auto; margin-right: auto; width: 50%;">
    </div>
    <div id="nav-body" style="overflow-x:hidden;"> 
        <div>
            <a href="/purchases/create" class="link-container">
                {{-- <i class="fas fa-cubes fa-fw"></i> --}}
                <i class="shipping fast icon"></i>
                <span class="link-title" >廠商進貨維護</span>
            </a>
        </div>
        <div>
            <a href="/purchases/index" class="link-container">
                {{-- <i class="fas fa-cubes fa-fw"></i> --}}
                <i class="truck icon"></i>
                <span class="link-title" >廠商進貨分析</span>
            </a>
        </div>
        <div>
            <a href="/purchase_items/index" class="link-container">
                {{-- <i class="fas fa-cubes fa-fw"></i> --}}
                <i class="boxes icon"></i>
                <span class="link-title" >材料進貨分析</span>
            </a>
        </div>
    </div>
    <div id="nav-footer" class="container" style="position: absolute; bottom: 1em; color: white;">
        <div class="dropup" style="width: 100%;">
            <button class="dropbtn">
                <i class="fas fa-angle-right"></i>
            </button>
            <div class="dropup-content">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
    
</div>