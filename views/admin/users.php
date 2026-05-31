<?php include 'views/partials/header.php'; ?>
<div class="page-container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 16px;">
        <h2>System Administration</h2>
        <div style="font-size: 14px; color: var(--text-secondary);">
            <i class="ri-user-settings-line" style="color: var(--gold-solid);"></i> Logged in as: <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>
        </div>
    </div>
    
    <div class="card" style="padding: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; margin-bottom: 24px;">
            <h3 style="font-family: 'Space Grotesk', sans-serif; font-size: 20px;"><i class="ri-group-line" style="color: var(--gold-solid);"></i> User Database</h3>
            
            <div class="filter-group" style="margin: 0; min-width: 280px; position: relative;">
                <input type="text" id="userFilter" placeholder="Search users by email..." style="padding-left: 44px;">
                <i class="ri-search-line" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 16px;"></i>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="data-table" id="usersTable">
                <thead>
                    <tr>
                        <th>User Account</th>
                        <th>System Role</th>
                        <th>Security Status</th>
                        <th style="text-align: center;">Account Controls</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $user): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <?php if($user['status'] === 'blocked'): ?>
                                        <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--danger); box-shadow: var(--danger-glow);" title="Blocked Account"></span>
                                    <?php else: ?>
                                        <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--success); box-shadow: var(--success-glow);" title="Active Account"></span>
                                    <?php endif; ?>
                                    <span style="font-weight: 500; color: var(--text-primary);"><?= htmlspecialchars($user['email']) ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="badge role-<?= $user['role'] ?>"><?= ucfirst($user['role']) ?></span>
                            </td>
                            <td>
                                <?php if($user['status'] === 'blocked'): ?>
                                    <span style="color: var(--danger); font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="ri-lock-line"></i> Blocked
                                    </span>
                                <?php else: ?>
                                    <span style="color: var(--success); font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="ri-shield-check-line"></i> Active
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <?php if($user['role'] !== 'admin'): ?>
                                        <?php if($user['status'] === 'blocked'): ?>
                                            <a href="index.php?action=adminUnblockUser&id=<?= $user['id'] ?>" class="btn btn-sm btn-success" title="Unblock User">
                                                <i class="ri-lock-unlock-line"></i> Unblock
                                            </a>
                                        <?php else: ?>
                                            <a href="index.php?action=adminBlockUser&id=<?= $user['id'] ?>" class="btn btn-sm btn-warning" title="Block User">
                                                <i class="ri-lock-line"></i> Block
                                            </a>
                                        <?php endif; ?>
                                        <a href="index.php?action=adminDeleteUser&id=<?= $user['id'] ?>" class="btn btn-sm btn-danger" style="padding: 6px 10px;" title="Delete User Account" onclick="return confirm('Are you sure you want to permanently delete this user account?')">
                                            <i class="ri-delete-bin-line"></i>
                                        </a>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted); font-size: 12px;">System Owner</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'views/partials/footer.php'; ?>
