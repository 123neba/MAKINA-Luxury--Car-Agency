<?php include 'views/partials/header.php'; ?>
<div class="page-container">
    <div class="showroom-header">
        <h2>Available Cars</h2>
        <div class="showroom-filter-box">
            <i class="ri-search-2-line"></i>
            <input type="text" id="carFilter" placeholder="Search by brand, model, year...">
        </div>
    </div>
    
    <div class="car-grid" id="carGrid">
        <?php foreach($cars as $car): ?>
            <div class="car-card clickable" onclick="window.location.href='index.php?action=carDetails&id=<?= $car['id'] ?>'">
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
                            <span><i class="ri-bookmark-line"></i> <?= htmlspecialchars($car['brand']) ?></span>
                            <span><i class="ri-calendar-line"></i> <?= htmlspecialchars($car['year']) ?></span>
                        </div>
                        <p style="color: var(--text-muted); font-size: 13px; margin-top: 8px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?= htmlspecialchars($car['summary']) ?>
                        </p>
                    </div>
                    
                    <div class="car-footer">
                        <span class="price"><?= number_format($car['price']) ?> Birr</span>
                        <span class="btn btn-sm btn-secondary" style="padding: 6px 12px; font-size: 11px;">
                            <span>View Details</span> <i class="ri-arrow-right-s-line"></i>
                        </span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <?php if(empty($cars)): ?>
        <div class="card text-center" style="padding: 40px; border-style: dashed; border-color: var(--border-color);">
            <i class="ri-car-line" style="font-size: 48px; color: var(--text-muted); margin-bottom: 16px;"></i>
            <p style="font-size: 16px; color: var(--text-secondary);">No premium vehicles listed at the moment.</p>
        </div>
    <?php endif; ?>
</div>
<?php include 'views/partials/footer.php'; ?>
