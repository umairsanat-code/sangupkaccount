<!-- Modal -->
<div class="modal fade" id="permissionEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form  class="products" method="post" id="updaterolesForm"   enctype="multipart/form-data">
      <div class="modal-body">
                
            <div class="col-12">
                @csrf
                <div class="row">
                    <div class="col-md-12 col-lg-12 col-sm-12">
                        <div class="form-group">
                             <label>Name</label>
                             <input type="text" name="role" id="editName" class="form-control" autocomplete="off" required>
                             <input type="hidden" name="roleId" class="form-control" id="editroleId">
                              <input type="hidden" name="modalId" value="#EditCategory" class="form-control">
                         </div>
                    </div>
                     <div class="col-md-12 col-lg-12 col-sm-12">
                       <div class="form-group">
                                <label>Persmissions</label>
                                 <select class="form-control select3"style="width:100%" name="permissions[]"multiple="multiple" required>
                                 @foreach($permssions as $p)
                                  <option value="{{ $p->name }}">{{ ucfirst($p->moduleName) }}-{{ $p->name }}</option>
                                 @endforeach
                                 </select>
                                 <br>
                               <span>At least give 1 or more permissions to 1 role</span>
                            </div>
                  </div>
                </div>
             </div>
               </div>
         <div class="modal-footer">
            <button type="submit" class="btn btn-success btn-flat btn-lg">Done</button>
           </div>
                   </form>
            </div>
          </div>
        </div>



