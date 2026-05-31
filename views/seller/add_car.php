<?php include 'views/partials/header.php'; ?>
<div class="page-container">
    <div class="card form-card" style="max-width: 740px; margin: 0 auto; position: relative;">
        <div style="text-align: center; margin-bottom: 24px;">
            <i class="ri-add-circle-line" style="font-size: 48px; color: var(--gold-solid); text-shadow: var(--gold-glow);"></i>
        </div>
        <h2 style="text-align: center; margin-bottom: 8px;">List a New Vehicle</h2>
        <p style="color: var(--text-secondary); text-align: center; font-size: 14px; margin-bottom: 36px;">Enter the official specifications of your premium automobile</p>

        <?php if (!empty($error)): ?>
            <div class="alert danger" style="margin-bottom: 24px;">
                <i class="ri-error-warning-line" style="font-size: 18px;"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>
        
        <form action="index.php?action=sellerPostAddCar" method="POST" enctype="multipart/form-data" id="addCarForm">
            <div class="form-row">
                <div class="form-group">
                    <label><i class="ri-car-fill"></i> Brand Name</label>
                    <input type="text" name="brand" list="brandList" required placeholder="e.g. Toyota">
                    <datalist id="brandList">
                        <option value="BYD">
                        <option value="Toyota">
                        <option value="Hyundai">
                        <option value="Suzuki">
                        <option value="Ford">
                        <option value="Changan">
                        <option value="Mercedes-Benz">
                    </datalist>
                </div>
                <div class="form-group">
                    <label>Model Description</label>
                    <input type="text" name="model" required placeholder="e.g. Corolla / ID.4">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="ri-calendar-line"></i> Year</label>
                    <input type="number" name="year" min="1900" max="2027" required placeholder="YYYY">
                </div>
                <div class="form-group">
                    <label><i class="ri-money-dollar-circle-line"></i> Asking Price (Birr)</label>
                    <input type="number" name="price" step="10000" min="50000" required placeholder="e.g. 2,800,000">
                </div>
            </div>

            <div class="form-group">
                <label><i class="ri-bank-card-line"></i> Ethiopia License Plate</label>
                <div class="license-plate-inputs">
                    <input type="text" name="lp_char1" id="lp_char1" placeholder="Code (e.g. 3)" required title="One Optional Uppercase Letter or Code Number">
                    <input type="text" name="lp_num" id="lp_num" placeholder="Plate Number (e.g. A4589)" required title="Five Numbers/Chars">
                    <input type="text" name="lp_char2" id="lp_char2" placeholder="Region (e.g. ET)" required title="Two Uppercase Letters for Region/Plate Category">
                </div>
                <span style="display: block; font-size: 11px; color: var(--text-muted); margin-top: 8px; margin-left: 4px;">
                    * Standard format automatically formats code, plate number, and region.
                </span>
            </div>

            <div class="form-group">
                <label><i class="ri-chat-quote-line"></i> Listing Summary</label>
                <textarea name="summary" rows="4" required placeholder="Describe the vehicle's history, condition, mileage, key specifications..."></textarea>
            </div>

            <div class="form-group">
                <label><i class="ri-camera-fill"></i> Vehicle Photos (3 required)</label>
                <p style="color: var(--text-muted); font-size: 12px; margin: 8px 0 0 4px;">
                    Upload three <strong>different</strong> images: front exterior, rear exterior, and interior cabin.
                </p>
                <div class="photo-upload-grid" style="margin-top: 12px;">
                    <div class="upload-box" data-slot="front">
                        <label for="img_front" class="upload-label">
                            <img class="upload-preview" alt="Front preview" hidden>
                            <span class="upload-placeholder">
                                <span class="plus-icon"><i class="ri-car-line"></i></span>
                                <span class="upload-title">Front View</span>
                                <span class="upload-hint">Exterior — front</span>
                            </span>
                        </label>
                        <input type="file" name="image_front" id="img_front" accept="image/jpeg,image/png,image/webp" class="hidden-input car-photo-input" required>
                    </div>
                    <div class="upload-box" data-slot="back">
                        <label for="img_back" class="upload-label">
                            <img class="upload-preview" alt="Back preview" hidden>
                            <span class="upload-placeholder">
                                <span class="plus-icon"><i class="ri-roadster-line"></i></span>
                                <span class="upload-title">Back View</span>
                                <span class="upload-hint">Exterior — rear</span>
                            </span>
                        </label>
                        <input type="file" name="image_back" id="img_back" accept="image/jpeg,image/png,image/webp" class="hidden-input car-photo-input" required>
                    </div>
                    <div class="upload-box" data-slot="interior">
                        <label for="img_interior" class="upload-label">
                            <img class="upload-preview" alt="Interior preview" hidden>
                            <span class="upload-placeholder">
                                <span class="plus-icon"><i class="ri-steering-2-line"></i></span>
                                <span class="upload-title">Interior View</span>
                                <span class="upload-hint">Cabin / dashboard</span>
                            </span>
                        </label>
                        <input type="file" name="image_interior" id="img_interior" accept="image/jpeg,image/png,image/webp" class="hidden-input car-photo-input" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="margin-top: 24px;">
                <span>Publish Listing</span> <i class="ri-check-double-line"></i>
            </button>
        </form>
    </div>
</div>
<?php include 'views/partials/footer.php'; ?>
