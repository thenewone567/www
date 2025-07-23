<div class="container-fluid">
    <!-- Row 1: Sales Overview -->
    <div class="row my-4">
        <h2 class="mb-3">Sales Overview</h2>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Today</h5>
                    <p class="card-text">$1,234</p>
                    <p class="card-text"><small class="text-muted"><i class="fas fa-arrow-up text-success"></i> 5%</small></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">This Week</h5>
                    <p class="card-text">$8,765</p>
                    <p class="card-text"><small class="text-muted"><i class="fas fa-arrow-up text-success"></i> 12%</small></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">This Month</h5>
                    <p class="card-text">$34,567</p>
                    <p class="card-text"><small class="text-muted"><i class="fas fa-arrow-down text-danger"></i> 2%</small></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Three Columns -->
    <div class="row my-4">
        <!-- Top Selling Items -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Top Selling Items</div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Item 1</td>
                                <td>50</td>
                                <td>$500</td>
                            </tr>
                            <tr>
                                <td>Item 2</td>
                                <td>30</td>
                                <td>$450</td>
                            </tr>
                            <tr>
                                <td>Item 3</td>
                                <td>25</td>
                                <td>$300</td>
                            </tr>
                            <tr>
                                <td>Item 4</td>
                                <td>20</td>
                                <td>$200</td>
                            </tr>
                            <tr>
                                <td>Item 5</td>
                                <td>15</td>
                                <td>$150</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Pending Restock -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Pending Restock</div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Location</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Item A</td>
                                <td>100</td>
                                <td>WH-1</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                            </tr>
                            <tr>
                                <td>Item B</td>
                                <td>50</td>
                                <td>WH-2</td>
                                <td><span class="badge bg-info">In Transit</span></td>
                            </tr>
                            <tr>
                                <td>Item C</td>
                                <td>75</td>
                                <td>WH-1</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Low Stock Alerts -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Low Stock Alerts</div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Threshold</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="table-danger">
                                <td>Item X</td>
                                <td>8</td>
                                <td>10</td>
                            </tr>
                            <tr>
                                <td>Item Y</td>
                                <td>12</td>
                                <td>10</td>
                            </tr>
                            <tr class="table-danger">
                                <td>Item Z</td>
                                <td>5</td>
                                <td>5</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Quick Actions & Charts -->
    <div class="row my-4">
        <!-- Quick Actions -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Quick Actions</div>
                <div class="card-body">
                    <a href="#" class="btn btn-primary mb-2"><i class="fas fa-plus"></i> New Sale</a>
                    <a href="#" class="btn btn-secondary mb-2"><i class="fas fa-plus"></i> New Purchase</a>
                    <a href="#" class="btn btn-info mb-2"><i class="fas fa-undo"></i> Return</a>
                </div>
            </div>
        </div>
        <!-- Charts -->
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Inventory</div>
                        <div class="card-body">
                            <canvas id="inventoryChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Financials</div>
                        <div class="card-body">
                            <canvas id="financialsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="public/js/main.js"></script>
