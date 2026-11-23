<div class="modal fade" id="paymentHistoryModal" tabindex="-1" role="dialog" aria-labelledby="paymentHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="paymentHistoryModalLabel">
                    <i class="fas fa-history"></i> Payment History
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body">
                <!-- Ticket Summary Section -->
                <div class="card mb-3">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 text-muted">Ticket Number</h6>
                            <h5 id="ticketNumber" class="text-primary">#123456</h5>
                        </div>
                        <div>
                            <h6 class="mb-1 text-muted">Total Paid</h6>
                            <h5 id="totalPaid" class="text-success">$500</h5>
                        </div>
                        <div>
                            <h6 class="mb-1 text-muted">Amount Due</h6>
                            <h5 id="amountDue" class="text-danger">$100</h5>
                        </div>
                        <div>
                            <h6 class="mb-1 text-muted">Pending Amount</h6>
                            <h5 id="pendingAmount" class="text-warning">$50</h5>
                        </div>
                    </div>
                </div>
                <!-- Payment History Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Amount</th>
                            <th>Payment Type</th>
                            <th>Notes</th>
                            <th>Date</th>
                        </tr>
                        </thead>
                        <tbody id="paymentHistoryTable">
                        <!-- Rows will be dynamically loaded here -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav>
                    <ul id="paymentHistoryPagination" class="pagination justify-content-center mt-3">
                        <!-- Pagination links will be dynamically generated here -->
                    </ul>
                </nav>

            </div>
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>
