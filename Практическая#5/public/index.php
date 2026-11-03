<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Model/Category.php';
require_once __DIR__ . '/../src/Model/Product.php';
require_once __DIR__ . '/../src/Service/CatalogService.php';

use Service\CatalogService;

$dataPath = __DIR__ . '/../data';
$catalogService = new CatalogService($dataPath);

$selectedCategoryId = null;
if (isset($_GET['category_id']) && $_GET['category_id'] !== '') {
    $selectedCategoryId = (int)$_GET['category_id'];
}

$categories = $catalogService->getCategories();

$products = $catalogService->getProducts($selectedCategoryId);

$averagePrice = $catalogService->getAveragePrice($products);
$totalCount = $catalogService->getTotalCount($products);

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-title">Товаров в выборке</div>
        <div class="stat-val"><?= $totalCount ?> шт.</div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Средняя цена</div>
        <div class="stat-val"><?= number_format($averagePrice, 2, '.', ' ') ?> ₽</div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Фильтр по категории</div>
        <div class="stat-val">
            <?= $selectedCategoryId !== null ? 'Применён (ID: ' . (int)$selectedCategoryId . ')' : 'Все категории' ?>
        </div>
    </div>
</div>

<form class="filter-panel" method="GET" action="index.php">
    <label for="category_id">Категория:</label>
    <select name="category_id" id="category_id">
        <option value="">Все категории</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat->getId() ?>" <?= $selectedCategoryId === $cat->getId() ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat->getName(), ENT_QUOTES, 'UTF-8') ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Применить</button>
    <?php if ($selectedCategoryId !== null): ?>
        <a href="index.php" class="btn-reset">Сбросить</a>
    <?php endif; ?>
</form>

<?php if (empty($products)): ?>
    <div class="empty-state">
        <h3>В выбранной категории нет товаров</h3>
        <p>Попробуйте выбрать другую категорию или сбросить фильтр.</p>
    </div>
<?php else: ?>
    <div class="product-grid">
        <?php foreach ($products as $prod): ?>
            <div class="product-card">
                <div class="product-name">
                    <?= htmlspecialchars($prod->getName(), ENT_QUOTES, 'UTF-8') ?>
                </div>
                <div class="product-price">
                    <?= htmlspecialchars($prod->getFormattedPrice(), ENT_QUOTES, 'UTF-8') ?>
                </div>
                <div class="product-discount">
                    Со скидкой 10%: <?= number_format($prod->calculateDiscountPrice(10), 2, '.', ' ') ?> ₽
                </div>
                
                <?php if ($prod->isPremium()): ?>
                    <div class="tag-premium">Премиум-сегмент</div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>