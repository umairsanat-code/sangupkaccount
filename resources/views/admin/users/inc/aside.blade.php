<aside class="control-sidebar control-sidebar-light">
    <!-- Control sidebar content goes here -->
    <div class="scrollbarStyle p-3 control-sidebar-content os-host os-theme-light os-host-resize-disabled os-host-scrollbar-horizontal-hidden os-host-transition os-host-overflow os-host-overflow-y order-aside-sidebar">

        <div class="os-scrollbar-corner">
            <button type="button" id="cancelBtn" class="close" data-dismiss="modal">&times;</button>
            <h5 class="mt-3 text-bold">Seller Details</h5>
            <div class="text text-right">
            <div class="dropdown dropdown-block-aside">
              <i class="fas fa-ellipsis-v"id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                @can('Block Sellers')
              <a class="dropdown-item btn-block-aside" href="#"onclick="return  confirm('Are you sure Block?')">Block</a>
               @endcan
               @can('Unblock Sellers')
              <a class="dropdown-item btn-unblock-aside" href="#"onclick="return  confirm('Are you sure to unblock ?')">Unblock</a>
               @endcan
             </div>
          </div>

        </div>
         <div class="seller-details" id="seller-details"></div>
          @can('View Seller Document')
          <div class="row" id="seller-documents" style="margin-bottom:50px;">
          </div>
           @endcan
          <div class="row">
          <div class="col-md-6 col-sm-6 col-lg-6">
            @can('Reject Sellers')
          <a class = "btn btn-danger btn-block btn-reject-aside">Reject</a>

         </div>
         <div class="col-md-6 col-sm-6 col-lg-6">
           @can('Approve Sellers')
          <a class = "btn btn-success btn-block btn-accept-aside" onclick="return  confirm('Are you sure you want to approve this seller  ?')">Approve</a>
          @endcan
         </div>
        </div>
        @endcan
        </div>
    </div>
</aside>

