<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Cycle Counts</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Cycle Counts</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Summary Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $stats->planned_counts ?? 0 ?></h3>
                            <p>Planned Counts</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $stats->active_counts ?? 0 ?></h3>
                            <p>In Progress</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-play"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $stats->completed_counts ?? 0 ?></h3>
                            <p>Completed</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3><?= $stats->total_counts ?? 0 ?></h3>
                            <p>Total Counts</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-list"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="btn-group" role="group">
                        <a href="/cycle_counts/create" class="btn btn-success">
                            <i class="fas fa-plus"></i> Create New Count
                        </a>
                        <button class="btn btn-info" onclick="refreshData()">
                            <i class="fas fa-sync"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>

            <!-- Cycle Counts Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Cycle Counts List</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="cycleCountsTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Count Name</th>
                                    <th>Type</th>
                                    <th>Planned Date</th>
                                    <th>Started</th>
                                    <th>Status</th>
                                    <th>Item Count</th>
                                    <th>Created By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($cycleCounts)): ?>
                                    <?php foreach ($cycleCounts as $count): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($count->count_name) ?></td>
                                            <td>
                                                <span
                                                    class="badge badge-<?= $count->type === 'full' ? 'primary' : 'secondary' ?>">
                                                    <?= ucfirst($count->type) ?>
                                                </span>
                                            </td>
                                            <td><?= date('Y-m-d', strtotime($count->planned_date)) ?></td>
                                            <td><?= $count->started_at ? date('Y-m-d H:i', strtotime($count->started_at)) : '-' ?>
                                            </td>
                                            <td>
                                                <?php
                                                $statusClass = [
                                                    'planned' => 'info',
                                                    'in_progress' => 'warning',
                                                    'completed' => 'success',
                                                    'cancelled' => 'danger'
                                                ];
                                                ?>
                                                <span class="badge badge-<?= $statusClass[$count->status] ?? 'secondary' ?>">
                                                    <?= ucwords(str_replace('_', ' ', $count->status)) ?>
                                                </span>
                                            </td>
                                            <td><?= $count->item_count ?></td>
                                            <td><?= htmlspecialchars($count->created_by_name ?? 'N/A') ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="/cycle_counts/show/<?= $count->id ?>" class="btn btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    <?php if ($count->status === 'planned'): ?>
                                                        <button class="btn btn-success" onclick="startCount(<?= $count->id ?>)">
                                                            <i class="fas fa-play"></i>
                                                        </button>
                                                    <?php endif; ?>

                                                    <?php if ($count->status === 'in_progress'): ?>
                                                        <a href="/cycle_counts/count/<?= $count->id ?>" class="btn btn-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button class="btn btn-success" onclick="completeCount(<?= $count->id ?>)">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    <?php endif; ?>

                                                    <?php if (in_array($count->status, ['planned', 'in_progress'])): ?>
                                                        <button class="btn btn-danger" onclick="cancelCount(<?= $count->id ?>)">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No cycle counts found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function () {
        $('#cycleCountsTable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "order": [[2, "desc"]]
        });
    });

    function startCount(countId) {
        if (confirm('Are you sure you want to start this cycle count?')) {
            window.location.href = '/cycle_counts/start/' + countId;
        }
    }

    function completeCount(countId) {
        if (confirm('Are you sure you want to complete this cycle count?')) {
            window.location.href = '/cycle_counts/complete/' + countId;
        }
    }

    function cancelCount(countId) {
        if (confirm('Are you sure you want to cancel this cycle count?')) {
            window.location.href = '/cycle_counts/cancel/' + countId;
        }
    }

    function refreshData() {
        location.reload();
    }
</script>


            </div> <!-- End container-fluid -->
        </div> <!-- End page-content-wrapper -->
    </div> <!-- End wrapper -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
    <script src="<?php echo URLROOT; ?>/js/main.js"></script>
</body>
</html>