<?php
// Parse existing license plate: "3-12345-AA" → parts
$lpParts = explode('-', $car['license_plate'] ?? '');
$lpChar1 = $lpParts[0] ?? '';
$lpNum   = $lpParts[1] ?? '';
$lpChar2 = $lpParts[2] ?? '';
?>
<?php include 'views/partials/header.php'; ?>
<div class="page-container">
    <div style="margin-bottom: 24px;">
        <a href="index.php?action=sellerProfile" class="btn btn-sm btn-secondary">
            <i class="ri-arrow-left-line"></i> Back to Profile
        </a>
    </div>

    <div class="card form-card" style="max-width: 740px; margin: 0 auto; position: relative;">
        <div style="text-align: center; margin-bottom: 24px;">
            <i class="ri-edit-box-line" style="font-size: 48px; color: var(--gold-solid); text-shadow: var(--gold-glow);"></i>
        </div>
        <h2 style="text-align: center; margin-bottom: 8px;">Edit Vehicle Listing</h2>
        <p style="color: var(--text-secondary); text-align: center; font-size: 14px; margin-bottom: 36px;">
            Update the details for your <strong><?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?></strong>
        </p>

        <?php if (!empty($error)): ?>
            <div class="alert danger" style="margin-bottom: 24px;">
                <i class="ri-error-warning-line" style="font-size: 18px;"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form action="index.php?action=sellerPostEditCar" method="POST" enctype="multipart/form-data" id="editCarForm">
            <input type="hidden" name="car_id" value="<?= (int)$car['id'] ?>">

            <div class="form-row">
                <div class="form-group">
                    <label><i class="ri-car-fill"></i> Brand Name</label>
                    <input type="text" name="brand" list="brandList" required placeholder="e.g. Toyota"
                           value="<?= htmlspecialchars($car['brand']) ?>">
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
                    <input type="text" name="model" required placeholder="e.g. Corolla / ID.4"
                           value="<?= htmlspecialchars($car['model']) ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="ri-calendar-line"></i> Year</label>
                    <input type="number" name="year" min="1900" max="2027" required placeholder="YYYY"
                           value="<?= htmlspecialchars($car['year']) ?>">
                </div>
                <div class="form-group">
                    <label><i class="ri-money-dollar-circle-line"></i> Asking Price (Birr)</label>
                    <input type="number" name="price" step="10000" min="50000" required placeholder="e.g. 2,800,000"
                           value="<?= htmlspecialchars($car['price']) ?>">
                </div>
            </div>

            <div class="form-group">
                <label><i class="ri-bank-card-line"></i> Ethiopia License Plate</label>
                <div class="license-plate-inputs">
                    <input type="text" name="lp_char1" id="lp_char1" placeholder="Code (e.g. 3)" required
                           value="<?= htmlspecialchars($lpChar1) ?>">
                    <input type="text" name="lp_num"   id="lp_num"   placeholder="Plate Number (e.g. A4589)" required
                           value="<?= htmlspecialchars($lpNum) ?>">
                    <input type="text" name="lp_char2" id="lp_char2" placeholder="Region (e.g. ET)" required
                           value="<?= htmlspecialchars($lpChar2) ?>">
                </div>
                <span style="display: block; font-size: 11px; color: var(--text-muted); margin-top: 8px; margin-left: 4px;">
                    * Standard format automatically formats code, plate number, and region.
                </span>
            </div>

            <div class="form-group">
                <label><i class="ri-chat-quote-line"></i> Listing Summary</label>
                <textarea name="summary" rows="4" required placeholder="Describe the vehicle's history, condition, mileage, key specifications..."><?= htmlspecialchars($car['summary']) ?></textarea>
            </div>

            <!-- Current Photos Preview -->
            <div class="form-group">
                <label><i class="ri-image-line"></i> Current Photos</label>
                <div style="display: flex; gap: 12px; margin-top: 10px; flex-wrap: wrap;">
                    <?php foreach (['image_front' => 'Front', 'image_back' => 'Back', 'image_interior' => 'Interior'] as $field => $label): ?>
                        <?php if (!empty($car[$field])): ?>
                            <div style="text-align: center;">
                                <img src="<?= htmlspecialchars($car[$field]) ?>" alt="<?= $label ?>"
                                     style="width: 150px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-color);">
                                <span style="display: block; font-size: 11px; color: var(--text-muted); margin-top: 4px;"><?= $label ?></span>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- New Photos (optional) -->
            <div class="form-group">
                <label><i class="ri-camera-fill"></i> Replace Photos <span style="font-weight: 400; color: var(--text-muted); font-size: 13px;">(optional — leave blank to keep current)</span></label>
                <p style="color: var(--text-muted); font-size: 12px; margin: 8px 0 0 4px;">
                    Only upload a new photo if you want to replace that view.
                </p>
                <div class="photo-upload-grid" style="margin-top: 12px;">
                    <?php
                    $uploadSlots = [
                        'image_front'    => ['id' => 'img_front',    'icon' => 'ri-car-line',       'title' => 'New Front View',    'hint' => 'Exterior — front'],
                        'image_back'     => ['id' => 'img_back',     'icon' => 'ri-roadster-line',   'title' => 'New Back View',     'hint' => 'Exterior — rear'],
                        'image_interior' => ['id' => 'img_interior', 'icon' => 'ri-steering-2-line', 'title' => 'New Interior View', 'hint' => 'Cabin / dashboard'],
                    ];
                    foreach ($uploadSlots as $fieldName => $slot): ?>
                        <div class="upload-box" data-slot="<?= $fieldName ?>">
                            <label for="<?= $slot['id'] ?>" class="upload-label">
                                <img class="upload-preview" alt="Preview" hidden>
                                <span class="upload-placeholder">
                                    <span class="plus-icon"><i class="<?= $slot['icon'] ?>"></i></span>
                                    <span class="upload-title"><?= $slot['title'] ?></span>
                                    <span class="upload-hint"><?= $slot['hint'] ?></span>
                                </span>
                            </label>
                            <input type="file" name="<?= $fieldName ?>" id="<?= $slot['id'] ?>"
                                   accept="image/jpeg,image/png,image/webp"
                                   class="hidden-input car-photo-input">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="margin-top: 24px;">
                <i class="ri-save-line"></i> <span>Save Changes</span>
            </button>
        </form>
    </div>
</div>
<?php include 'views/partials/footer.php'; ?>
