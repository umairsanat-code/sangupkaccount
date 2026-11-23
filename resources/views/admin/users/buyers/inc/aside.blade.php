<aside class="control-sidebar control-sidebar-light"
       style="width:300px;">
    <!-- Control sidebar content goes here -->
    <div class="scrollbarStyle p-3 control-sidebar-content os-host os-theme-light os-host-resize-disabled os-host-scrollbar-horizontal-hidden os-host-transition os-host-overflow os-host-overflow-y order-aside-sidebar" >

        <div class="os-scrollbar-corner">
            <button type="button" id="cancelBtn" class="close" data-dismiss="modal">&times;</button>
            <h5 class="mt-3 text-bold">Buyer Details</h5>
            <div class="text text-right">
                @can('Block Buyers')
                <div class="dropdown dropdown-block-aside">
                    <i class="fas fa-ellipsis-v"
                       id="dropdownMenuButton"
                       data-toggle="dropdown"
                       aria-haspopup="true"
                       aria-expanded="false"></i>
                    <div class="dropdown-menu"
                         aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item btn-block-aside"
                           href="#" onclick="return  confirm('Are you sure you want to block this buyer  ?')">Block</a>
                    </div>
                </div>
                @endcan
            </div>
            <div class="seller-details"
                 id="seller-details"></div>
            @can('View Buyer Orders')
            <div class="row">
                <div class="col-12">
                    <ul class="nav nav-pills nav-fill navtop">
                        <li class="nav-item">
                            <a class="nav-link active"
                               href="#activeorders"
                               data-toggle="tab">Active Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                               href="#history"
                               data-toggle="tab">History</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active"
                             role="tabpanel"
                             id="activeorders">

                        </div>

                        <div class="tab-pane"
                             role="tabpanel"
                             id="history">



                    </div>
                      <hr>
                    <div class="col-12"  style="display:none" id="display_all_orders">
                        <a href="{{ url('orders') }}" class="ml-2 btn btn-primary btn-block ">View All Orders</a>
                    </div>


                    </div>
                 </div>
             </div>

            @endcan
        </div>
</aside>
