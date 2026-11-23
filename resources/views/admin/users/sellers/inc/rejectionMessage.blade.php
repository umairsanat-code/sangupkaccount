<!-- Modal -->
<div class="modal fade" id="rejectionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Rejection Message</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form  class="products" method="get" id="rejectionForm" enctype="multipart/form-data">
      <div class="modal-body">
                
            <div class="col-12">
                @csrf
                <div class="row">
                    <div class="col-md-12 col-lg-12 col-sm-12">
                        <div class="form-group">
                             <textarea class="form-control" name="reason" rows="12" cols="12" required></textarea>
                         </div>
                    </div>
                 </div>
             </div>
               </div>
               <hr>
				    <div class="row m-3">
                <div class="col-md-4">
                  <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-secondary btn-flat
                   btn-lg ">Close</button>
                </div>
                <div class="col-md-8">
                  <div class="text-right">
                    <button type="submit" class="btn btn-danger btn-flat btn-lg">Send Rejection</button>
                  </div>
                </div>
              </div>
				     
              </form>
				    </div>
				  </div>
				</div>

