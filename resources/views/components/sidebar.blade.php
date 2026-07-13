<div class="px-3 mb-3 d-flex align-items-center position-relative">
    <div class="input-group input-group-sm search-wrapper" id="searchWrapper">
        <button class="input-group-text bg-transparent border-0 text-white-50" type="button" id="toggleSearchBtn">
            <i class="fa fa-search"></i>
        </button>
        <input type="text" id="sidebarSearch" class="form-control bg-transparent text-white border-0 small custom-search-input" placeholder="Search menu modules..." style="display: none; width: 0; padding: 0;">
    </div>
</div>

<ul id="sidebarMenu" class="list-unstyled components">

    <li class="menu-item-node">
        <a href="{{ route('dashboard') }}">
            <i class="fa fa-dashboard yellow_color me-2"></i>
            <span>Dashboard Panel</span>
        </a>
    </li>

    <li class="menu-item-node">
        <a href="#sshLab-Panel" data-toggle="collapse" aria-expanded="{{ request()->routeIs('product.view') ? 'true' : 'false' }}" class="dropdown-toggle">
            <i class="fa fa-tags green_color me-2"></i>
            <span>Product Group</span>
        </a>
        <ul class="collapse list-unstyled {{ request()->routeIs('product.view') ? 'show' : '' }}" id="sshLab-Panel">
       <li>
    <a href="{{ route('product.view', ['product' => 'all']) }}" 
       style="font-size: 0.9em; {{ request('product') == 'all' || empty(request('product')) ? 'color: #fff; font-weight: bold; background-color: rgba(255,255,255,0.1);' : 'color: #ccc;' }} display: block; padding: 5px 10px; border-radius: 4px;">
       <i class="fa fa-layer-group me-2 small text-warning"></i><strong>All Products (By Groups)</strong>
    </a>
</li>

@foreach($groupsdata as $group)
    <li>
        <a href="{{ route('product.view', ['product' => $group->id]) }}" 
           style="font-size: 0.9em; {{ request('product') == $group->id ? 'color: #fff; font-weight: bold;' : 'color: #ccc;' }}">
           <i class="fa fa-boxes me-2 small"></i>{{ $group->product_group }}
        </a>
    </li>
@endforeach
        </ul>
    </li>

    <li class="menu-item-node">
        <a href="#store-Panel" data-toggle="collapse" aria-expanded="{{ request()->routeIs('productstore') ? 'true' : 'false' }}" class="dropdown-toggle">
            <i class="fa fa-store blue2_color me-2"></i>
            <span>Product Store</span>
        </a>
        <ul class="collapse list-unstyled {{ request()->routeIs('productstore') ? 'show' : '' }}" id="store-Panel">
            <li>
                <a href="{{ route('productstore') }}">
                    <i class="fa fa-refresh  me-2 small"></i>Update Store
                </a>
            </li>
        </ul>
    </li>

    <li class="menu-item-node">
        <a href="#product-Panel" data-toggle="collapse" aria-expanded="{{ request()->routeIs('productadd', 'productgroupadd','productsattribute') ? 'true' : 'false' }}" class="dropdown-toggle">
            <i class="fa fa-sliders blue2_color me-2"></i>
            <span>Product Setup</span>
        </a>
        <ul class="collapse list-unstyled {{ request()->routeIs('productadd', 'productgroupadd','productreturn','productsattribute') ? 'show' : '' }}" id="product-Panel">
            <li>
                <a href="{{ route('productgroupadd') }}">
                    <i class="fa fa-folder-plus  me-2 small"></i>Add Group
                </a>
            </li>
            <li>
                <a href="{{ route('productadd') }}">
                    <i class="fa fa-plus-circle  me-2 small"></i>Add Product
                </a>
            </li>
            <li>
                <a href="{{ route('productreturn') }}">
                    <i class="fa fa-plus-circle  me-2 small"></i>Return Product
                </a>
            </li>
             <li>
                <a href="{{ route('warranty.index') }}">
                    <i class="fa fa-plus-circle  me-2 small"></i>warrenty Claim
                </a>
            </li>
            <li>
                <a href="{{ route('productsattribute') }}">
                    <i class="fa fa-puzzle-piece  me-2 small"></i>Add Product Attribute
                </a>
            </li>
        </ul>
    </li>

    <li class="menu-item-node">
        <a href="#accounts" data-toggle="collapse" aria-expanded="{{ request()->routeIs('receiveentry', 'journalview','paymententry') ? 'true' : 'false' }}" class="dropdown-toggle">
            <i class="fa fa-calculator blue2_color me-2"></i>
            <span>Accounts</span>
        </a>
        <ul class="collapse list-unstyled {{ request()->routeIs('receiveentry', 'journalview','paymententry') ? 'show' : '' }}" id="accounts">
            <li>
                <a href="{{ route('receiveentry') }}">
                    <i class="fa fa-arrow-circle-down  me-2 small"></i>Receive Entry
                </a>
            </li>
                <li>
                <a href="{{ route('paymententry') }}">
                  <i class="fa fa-credit-card  me-2 small"></i></i>Payment Entry
                </a>
            </li>
            <li>
                <a href="{{ route('journalview') }}">
                    <i class="fa fa-file-text  me-2 small"></i>Journal Report
                </a>
            </li>
        </ul>
    </li>
    <li class="menu-item-node">
        <a href="#stackholder-Panel" data-toggle="collapse" aria-expanded="{{ request()->routeIs('accounts.sr.index','accounts.seller.index') ? 'true' : 'false' }}" class="dropdown-toggle">
         <i class="nav-icon fas fa-users-cog"></i>
            <span>Stakeholders Ledger</span>
        </a>
        <ul class="collapse list-unstyled {{ request()->routeIs('accounts.sr.index','accounts.seller.index') ? 'show' : '' }}" id="stackholder-Panel">
            <li>
                <a href="{{route('accounts.sr.index')}}">  <i class="fas fa-shopping-cart nav-icon"></i>SR Add</a>
            </li>
               <li>
                <a href="{{route('accounts.seller.index')}}">  <i class="fas fa-truck-loading nav-icon"></i>Seller Add</a>
            </li>
          
        </ul>
    </li>
    <li class="menu-item-node">
        <a href="#Admin-Panel" data-toggle="collapse" aria-expanded="{{ request()->routeIs('userlist*') ? 'true' : 'false' }}" class="dropdown-toggle">
            <i class="fa fa-user-secret blue2_color me-2"></i>
            <span>Admin</span>
        </a>
        <ul class="collapse list-unstyled {{ request()->routeIs('userlist*') ? 'show' : '' }}" id="Admin-Panel">
            <li>
                <a href="{{ route('userlist') }}">
                    <i class="fa fa-user-plus  me-2 small"></i>User Add
                </a>
            </li>
       
            <li>
                <a href="">
                    <i class="fa fa-shield  me-2 small"></i>User Permission
                </a>
            </li>
            <li>
                <a href="">
                    <i class="fa fa-users-cog  me-2 small"></i>Role Management
                </a>
            </li>
        </ul>
    </li>

</ul>

<script>$(document).ready(function() {
    $('#sidebarSearch').on('keyup', function() {
        let value = $(this).val().toLowerCase();
        
        $('#sidebarMenu > li.menu-item-node').each(function() {
            let parentNode = $(this);
            let subMenu = parentNode.find('.collapse');
            let childItems = parentNode.find('ul li');
            
            // 1. If search is empty, reset everything to its original state
            if (value.length === 0) {
                parentNode.show();
                childItems.show();
                // Close menus unless they are active for the current page
                if (!subMenu.hasClass('show')) {
                    subMenu.removeClass('show');
                }
                return; // Skip to next parent loop item
            }
            
            // 2. Check if the parent menu item heading text matches
            let parentHeadingText = parentNode.children('a').text().toLowerCase();
            let parentMatches = parentHeadingText.indexOf(value) > -1;
            
            let visibleChildrenCount = 0;
            
            // 3. Filter individual child items
            childItems.each(function() {
                let childItem = $(this);
                let childText = childItem.text().toLowerCase();
                
                if (childText.indexOf(value) > -1) {
                    childItem.show();
                    visibleChildrenCount++;
                } else {
                    childItem.hide();
                }
            });
            
            // 4. Final visibility rules for the parent module block
            if (parentMatches) {
                // If the parent item heading matches, show parent and ALL its children
                parentNode.show();
                childItems.show();
                subMenu.addClass('show');
            } else if (visibleChildrenCount > 0) {
                // If parent heading doesn't match, but has matching children, show parent and open menu
                parentNode.show();
                subMenu.addClass('show');
            } else {
                // Nothing matches inside this module structure at all
                parentNode.hide();
            }
        });
    });
});
</script>