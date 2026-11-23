<aside class="control-sidebar control-sidebar-light">
    <!-- Control sidebar content goes here -->
    <div class="p-3 control-sidebar-content os-host os-theme-light os-host-resize-disabled os-host-scrollbar-horizontal-hidden os-host-transition os-host-overflow os-host-overflow-y"
         style="height:750px; overflow-y: scroll;">
    
        <div class="os-scrollbar-corner">
            <h5 class="mt-3 text-bold">Order Details</h5>
          <div class="seller-details" id="seller-details"></div>
         {{--  @can('View Seller Document') --}}
          <div class="row" id="seller-documents" style="margin-bottom:50px;">
          </div>
           {{-- @endcan --}}
          <div class="row">
          <div class="col-md-8 col-sm-8 col-lg-8 offset-2 mb-5">
           {{--  @can('Reject Sellers') --}}
          <a class = "btn btn-danger  btn-cancel-aside">Cancel Order</a>
          
         </div>
        </div>
       {{--  @endcan --}}
        </div>
    </div>
</aside>

