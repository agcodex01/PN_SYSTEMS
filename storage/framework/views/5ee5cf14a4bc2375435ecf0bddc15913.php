<?php $__env->startSection('content'); ?>
<div class="admin-container">
    <div class="page-header">
        <div class="header-content">
            <h1>Manage Users</h1>
            <p class="text-muted">View and manage system users</p>
        </div>
        <a href="<?php echo e(route('admin.pnph_users.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New User
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <form action="<?php echo e(route('admin.pnph_users.index')); ?>" method="GET" class="filter-form">
                <div class="form-group">
                    <select name="role" id="role" class="form-select" onchange="this.form.submit()">
                        <option value="">All Roles</option>
                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($role); ?>" <?php echo e($roleFilter == $role ? 'selected' : ''); ?>>
                                <?php echo e(ucfirst($role)); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </form>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>#<?php echo e($user->user_id); ?></td>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <?php echo e(strtoupper(substr($user->user_fname, 0, 1))); ?>

                                        </div>
                                        <div>
                                            <div class="user-name"><?php echo e($user->user_fname); ?> <?php echo e($user->user_lname); ?></div>
                                            <div class="user-details">
                                                <?php echo e($user->user_mInitial); ?><?php echo e($user->user_suffix ? ', ' . $user->user_suffix : ''); ?>

                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo e($user->user_email); ?></td>
                                <td>
                                    <span class="role-badge"><?php echo e(ucfirst($user->user_role)); ?></span>
                                </td>
                                <td>
                                    <?php if($user->status === 'active'): ?>
                                        <span class="status-badge active">
                                            <i class="fas fa-circle"></i> Active
                                        </span>
                                    <?php else: ?>
                                        <span class="status-badge inactive">
                                            <i class="fas fa-circle"></i> Inactive
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="actions">
                                    <div class="action-buttons">
                                        <a href="<?php echo e(route('admin.pnph_users.show', $user->user_id)); ?>" 
                                           class="btn-icon" 
                                           title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('admin.pnph_users.edit', $user->user_id)); ?>" 
                                           class="btn-icon" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">No users found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if($users->hasPages()): ?>
            <div class="pagination-container">
                <div class="pagination-info">
                    Showing <?php echo e($users->firstItem()); ?> to <?php echo e($users->lastItem()); ?> of <?php echo e($users->total()); ?> entries
                </div>
                <div class="pagination-buttons">
                    <?php if($users->onFirstPage()): ?>
                        <span class="pagination-button disabled">
                            <i class="fas fa-chevron-left"></i> Previous
                        </span>
                    <?php else: ?>
                        <a href="<?php echo e($users->previousPageUrl()); ?>" class="pagination-button">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                    <?php endif; ?>

                    <div class="page-info">
                        Page <?php echo e($users->currentPage()); ?> of <?php echo e($users->lastPage()); ?>

                    </div>

                    <?php if($users->hasMorePages()): ?>
                        <a href="<?php echo e($users->nextPageUrl()); ?>" class="pagination-button">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php else: ?>
                        <span class="pagination-button disabled">
                            Next <i class="fas fa-chevron-right"></i>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Main Container */
.admin-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

/* Page Header */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 25px;
}

.page-header h1 {
    font-size: 1.8rem;
    color: var(--text-color);
    margin: 0 0 5px 0;
}

.page-header .text-muted {
    color: #6c757d;
    margin: 0;
}

/* Card Styles */
.card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    margin-bottom: 25px;
    overflow: hidden;
}

.card-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-body {
    padding: 0;
}

/* Table Styles */
.table-responsive {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th,
.data-table td {
    padding: 15px 20px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.data-table th {
    font-weight: 600;
    color: #555;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.data-table tr:last-child td {
    border-bottom: none;
}

.data-table tr:hover {
    background-color: #f9f9f9;
}

/* User Info */
.user-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #e3f2fd;
    color: var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.9rem;
}

.user-name {
    font-weight: 500;
    color: #333;
}

.user-details {
    font-size: 0.85rem;
    color: #6c757d;
}

/* Badges */
.role-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
    background-color: #e3f2fd;
    color: #1976d2;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-badge i {
    font-size: 0.6rem;
}

.status-badge.active {
    background-color: #e8f5e9;
    color: #2e7d32;
}

.status-badge.inactive {
    background-color: #ffebee;
    color: #c62828;
}

/* Action Buttons */
.actions {
    text-align: right;
}

.action-buttons {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
}

.btn-icon {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #666;
    background: #f5f5f5;
    transition: all 0.2s;
}

.btn-icon:hover {
    background: #e0e0e0;
    color: #333;
    text-decoration: none;
}

/* Pagination */
.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-top: 1px solid #eee;
}

.pagination-info {
    color: #6c757d;
    font-size: 0.9rem;
}

.pagination-buttons {
    display: flex;
    align-items: center;
    gap: 10px;
}

.pagination-button {
    padding: 8px 16px;
    border-radius: 6px;
    background: white;
    border: 1px solid #ddd;
    color: #333;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    transition: all 0.2s;
}

.pagination-button:hover:not(.disabled) {
    background: #f5f5f5;
    border-color: #ccc;
}

.pagination-button.disabled {
    color: #aaa;
    cursor: not-allowed;
}

.page-info {
    margin: 0 10px;
    font-size: 0.9rem;
    color: #666;
}

/* Form Elements */
.form-select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    background-color: white;
    font-size: 0.9rem;
    min-width: 180px;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 16px;
    padding-right: 36px;
}

/* Responsive */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        gap: 15px;
    }
    
    .card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .pagination-container {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
}
</style>
</div>

<style>
    /* Simple and Clean Pagination Style */
    .pagination-simple {
        margin-top: 30px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }
    
    .pagination-buttons {
        display: flex;
        gap: 20px;
    }
    
    .pagination-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 25px;
        border-radius: 30px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .pagination-button.prev {
        background: linear-gradient(135deg, #1a8fc4 0%, #22bbea 100%);
        color: white;
        border: 1px solid #1a8fc4;
    }
    
    .pagination-button.next {
        background: linear-gradient(135deg, #22bbea 0%, #4ac9f5 100%);
        color: white;
        border: 1px solid #22bbea;
    }
    
    .pagination-button.prev:hover:not(.disabled) {
        background: linear-gradient(135deg, #15779e 0%, #1a9ecf 100%);
    }
    
    .pagination-button.next:hover:not(.disabled) {
        background: linear-gradient(135deg, #1a9ecf 0%, #3abcec 100%);
    }
    
    .pagination-button:hover:not(.disabled) {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.15);
    }
    
    .pagination-button:active:not(.disabled) {
        transform: translateY(0);
    }
    
    .pagination-button.disabled {
        background: #e9ecef;
        color: #6c757d;
        cursor: not-allowed;
        opacity: 0.7;
    }
    
    .pagination-button i {
        margin: 0 5px;
        font-size: 0.9em;
    }
    
    .pagination-info {
        color: #666;
        font-size: 0.9em;
        margin-top: 8px;
        font-weight: 500;
    }
    
    .pagination-button:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(34, 187, 234, 0.3);
    }
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin_layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laravel\PNPH-CAPSTONE\resources\views/admin/pnph_users/index.blade.php ENDPATH**/ ?>