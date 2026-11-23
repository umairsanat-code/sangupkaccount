      <div class="row m-3">
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info elevation-1"><i class="bg-info"></i></span>

                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Sellers</span>
                                            <span class="info-box-number">
                                                {{ $allSellers??'N/A' }}
                                            </span>
                                        </div>
                                        <!-- /.info-box-content -->
                                    </div>
                                    <!-- /.info-box -->
                                </div>
                                <!-- /.col -->
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="mb-3 info-box">
                                        <span class="info-box-icon bg-info elevation-1">
                                            <i class="bg-info"></i></span>

                                        <div class="info-box-content">
                                            <span class="info-box-text">Approved Sellers</span>
                                            <span class="info-box-number">{{ $approvedSellers??'N/A' }}</span>
                                        </div>
                                        <!-- /.info-box-content -->
                                    </div>
                                    <!-- /.info-box -->
                                </div>
                                <!-- /.col -->

                                <!-- fix for small devices only -->
                                <div class="clearfix hidden-md-up"></div>

                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="mb-3 info-box">
                                        <span class="info-box-icon bg-info  elevation-1"><i
                                               class="bg-info"></i></span>

                                        <div class="info-box-content">
                                            <span class="info-box-text">Need Approvals</span>
                                            <span class="info-box-number">{{ $needApprovalSellers??'N/A' }}</span>
                                        </div>
                                        <!-- /.info-box-content -->
                                    </div>
                                    <!-- /.info-box -->
                                </div>
                                <!-- /.col -->
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="mb-3 info-box">
                                        <span class="info-box-icon bg-info  elevation-1"><i
                                               class="bg-info"></i></span>

                                        <div class="info-box-content">
                                            <span class="info-box-text">Rejected Sellers</span>
                                            <span class="info-box-number">{{ $rejectedSellers??'N/A' }}</span>
                                        </div>
                                        <!-- /.info-box-content -->
                                    </div>
                                    <!-- /.info-box -->
                                </div>
                                <!-- /.col -->
                            </div>