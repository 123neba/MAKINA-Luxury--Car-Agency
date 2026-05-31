<?php include 'views/partials/header.php'; ?>
<div class="page-container">
    <div style="margin-bottom: 24px;">
        <a href="index.php?action=customerCars" class="btn btn-sm btn-secondary">
            <i class="ri-arrow-left-line"></i> Back to Showroom
        </a>
    </div>
    
    <div class="detail-layout">
        <!-- Left Column: Visual Showcase -->
        <div class="card" style="padding: 24px;">
            <div class="images-showcase">
                <div class="main-preview-container">
                    <?php if($car['image_front']): ?>
                        <img id="mainVehicleImage" src="<?= htmlspecialchars($car['image_front']) ?>" alt="Active vehicle preview" class="main-preview">
                    <?php else: ?>
                        <div class="placeholder-image" style="height: 400px;">
                            <i class="ri-image-line"></i>
                            <span>No Photo Available</span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="images-grid">
                    <?php if($car['image_front']): ?>
                        <button type="button" class="thumb-btn active" onclick="switchMainImage(this.querySelector('img'))">
                            <img src="<?= htmlspecialchars($car['image_front']) ?>" alt="Front view">
                            <span>Front</span>
                        </button>
                    <?php endif; ?>
                    <?php if($car['image_back']): ?>
                        <button type="button" class="thumb-btn" onclick="switchMainImage(this.querySelector('img'))">
                            <img src="<?= htmlspecialchars($car['image_back']) ?>" alt="Back view">
                            <span>Back</span>
                        </button>
                    <?php endif; ?>
                    <?php if($car['image_interior']): ?>
                        <button type="button" class="thumb-btn" onclick="switchMainImage(this.querySelector('img'))">
                            <img src="<?= htmlspecialchars($car['image_interior']) ?>" alt="Interior view">
                            <span>Interior</span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Right Column: Premium Specifications -->
        <div class="card detail-card">
            <span class="badge role-seller" style="margin-bottom: 12px;">Premium Listing</span>
            <h2 style="margin-bottom: 8px;"><?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?></h2>
            <div style="display: flex; gap: 12px; align-items: center; margin-bottom: 20px;">
                <span style="font-size: 14px; color: var(--text-muted);"><i class="ri-calendar-line"></i> Model Year: <strong><?= htmlspecialchars($car['year']) ?></strong></span>
            </div>
            
            <h3 class="price-large"><?= number_format($car['price']) ?> <span style="font-size: 20px;">Birr</span></h3>
            
            <div class="detail-info" style="border-top: 1px solid var(--border-color); padding-top: 24px; margin-top: 24px;">
                <p>
                    <strong>License Plate</strong>
                    <span class="plate-badge">
                        <i class="ri-bank-card-line" style="color: #1e3a8a; margin-right: 6px;"></i>
                        <?= htmlspecialchars($car['license_plate']) ?>
                    </span>
                </p>
                
                <p style="margin-top: 24px;">
                    <strong>Vehicle Summary</strong>
                    <span style="color: var(--text-primary); line-height: 1.8; font-size: 15px;">
                        <?= nl2br(htmlspecialchars($car['summary'])) ?>
                    </span>
                </p>
            </div>
            
            <div class="contact-seller card" style="margin-top: 36px; padding: 24px; border-radius: 12px; background: rgba(255, 255, 255, 0.01);">
                <h4 style="display: flex; align-items: center; gap: 8px; color: var(--gold-solid);">
                    <i class="ri-contacts-line"></i> Contact Seller
                </h4>
                
                <div style="margin-top: 16px;">
                    <p style="margin-bottom: 10px; font-size: 14px; color: var(--text-secondary);">
                        <i class="ri-user-line" style="margin-right: 6px;"></i>
                        <strong>Name:</strong> <?= htmlspecialchars($seller['first_name'] . ' ' . $seller['last_name']) ?>
                    </p>
                    <p style="font-size: 14px; color: var(--text-secondary); display: flex; align-items: center; justify-content: space-between;">
                        <span>
                            <i class="ri-phone-line" style="margin-right: 6px;"></i>
                            <strong>Phone:</strong> <span id="sellerPhone" style="color: var(--text-primary); font-weight: 600; font-family: 'Space Grotesk', sans-serif;"><?= htmlspecialchars($seller['phone_number']) ?></span>
                        </span>
                        <button class="btn btn-sm btn-primary" onclick="copyPhone()">
                            <i class="ri-file-copy-line"></i> Copy
                        </button>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function switchMainImage(element) {
    const mainImg = document.getElementById('mainVehicleImage');
    if (mainImg && element) {
        mainImg.src = element.src;
        document.querySelectorAll('.images-grid .thumb-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        element.closest('.thumb-btn')?.classList.add('active');
    }
}
</script>
<?php include 'views/partials/footer.php'; ?>
