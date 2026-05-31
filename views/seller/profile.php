<?php include 'views/partials/header.php'; ?>
<?php 
$totalListings = count($cars);
$totalValue = array_sum(array_column($cars, 'price'));
?>
<div class="page-container">
    <!-- Seller Analytics Header -->
    <div class="card" style="margin-bottom: 40px; padding: 30px; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 24px; position: relative; overflow: hidden;">
        <div>
            <span class="badge role-seller" style="margin-bottom: 8px;">Authorized Seller</span>
            <h2 style="margin: 0; line-height: 1;"><?= htmlspecialchars($_SESSION['user_name']) ?></h2>
            <p style="color: var(--text-muted); font-size: 14px; margin-top: 8px;"><i class="ri-mail-line"></i> <?= htmlspecialchars($_SESSION['user_email'] ?? 'Premium Member') ?></p>
        </div>
        
        <div class="stats-grid" style="margin: 0; min-width: 320px;">
            <div class="stat-card">
                <span style="font-size: 12px; text-transform: uppercase; color: var(--text-secondary);">Active Listings</span>
                <div class="stat-val"><?= $totalListings ?></div>
            </div>
            <div class="stat-card">
                <span style="font-size: 12px; text-transform: uppercase; color: var(--text-secondary);">Portfolio Value</span>
                <div class="stat-val" style="font-size: 20px; margin-top: 10px;"><?= number_format($totalValue) ?> Birr</div>
            </div>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h3 style="font-family: 'Space Grotesk', sans-serif; font-size: 22px;"><i class="ri-play-list-add-line" style="color: var(--gold-solid);"></i> My Listed Cars</h3>
        <a href="index.php?action=sellerAddCar" class="btn btn-sm btn-primary">
            <i class="ri-add-line"></i> Add New Vehicle
        </a>
    </div>

    <div class="car-grid">
        <?php foreach($cars as $car): ?>
            <div class="car-card">
                <div class="car-image-container">
                    <?php if($car['image_front']): ?>
                        <div class="car-image" style="background-image: url('<?= htmlspecialchars($car['image_front']) ?>')"></div>
                    <?php else: ?>
                        <div class="car-image placeholder-image">
                            <i class="ri-image-line"></i>
                            <span>No Photo</span>
                        </div>
                    <?php endif; ?>
                    <span class="car-year-badge"><?= htmlspecialchars($car['year']) ?></span>
                </div>
                <div class="car-details">
                    <div class="car-info">
                        <h4><?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?></h4>
                        <div class="car-meta">
                            <span><i class="ri-calendar-line"></i> <?= htmlspecialchars($car['year']) ?></span>
                        </div>
                    </div>
                    <div class="car-footer">
                        <span class="price"><?= number_format($car['price']) ?> Birr</span>
                        <div class="car-actions">
                            <a href="index.php?action=carDetails&id=<?= $car['id'] ?>" class="btn btn-sm btn-secondary" style="padding: 6px 10px;" title="Preview details">
                                <i class="ri-eye-line"></i>
                            </a>
                            <a href="index.php?action=sellerEditCar&id=<?= $car['id'] ?>" class="btn btn-sm btn-primary" style="padding: 6px 10px;" title="Edit listing">
                                <i class="ri-edit-line"></i>
                            </a>
                            <a href="index.php?action=sellerDeleteCar&id=<?= $car['id'] ?>" class="btn btn-sm btn-danger" style="padding: 6px 10px;" onclick="return confirm('Are you sure you want to permanently delete this vehicle?')" title="Delete listing">
                                <i class="ri-delete-bin-line"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if(empty($cars)): ?>
        <div class="card text-center" style="padding: 50px; border-style: dashed; border-color: var(--border-color); margin-top: 20px;">
            <i class="ri-inbox-line" style="font-size: 48px; color: var(--text-muted); margin-bottom: 16px;"></i>
            <p style="font-size: 16px; color: var(--text-secondary); margin-bottom: 24px;">You haven't listed any vehicles yet.</p>
            <a href="index.php?action=sellerAddCar" class="btn btn-primary">
                <i class="ri-add-line"></i> List Your First Car
            </a>
        </div>
    <?php endif; ?>
</div>
<?php include 'views/partials/footer.php'; ?>
