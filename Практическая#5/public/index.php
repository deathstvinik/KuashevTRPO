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
?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Практическая работа №5</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f4f6fb; color: #1f2937; line-height: 1.5; }
        header { background: #111827; color: #fff; padding: 18px 36px; display: flex; justify-content: space-between; align-items: center; }
        .brand { font-weight: 800; font-size: 20px; }
        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
        
        .hero { background: #fff; border-radius: 16px; padding: 28px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); margin-bottom: 24px; }
        .badge { display: inline-block; background: #e9efff; color: #315efb; border-radius: 999px; padding: 4px 12px; font-size: 13px; font-weight: 600; margin-bottom: 12px; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 20px; }
        .stat-title { font-size: 18px; font-weight: 700; margin-bottom: 6px; }
        .stat-val { color: #315efb; font-weight: 600; font-size: 16px; }

        .filter-panel { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 18px 20px; margin-bottom: 24px; display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
        select, button, .btn-reset { padding: 8px 14px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 14px; }
        button { background: #315efb; color: white; border: none; cursor: pointer; font-weight: 600; }
        button:hover { background: #254edb; }
        .btn-reset { text-decoration: none; color: #4b5563; background: #f3f4f6; }

        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 18px; }
        .product-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 18px; display: flex; flex-direction: column; justify-content: space-between; }
        .product-name { font-size: 17px; font-weight: 700; margin-bottom: 8px; }
        .product-price { font-size: 18px; font-weight: 800; color: #111827; margin: 8px 0; }
        .product-discount { font-size: 13px; color: #059669; }
        .tag-premium { display: inline-block; background: #fef3c7; color: #b45309; font-size: 11px; padding: 2px 8px; border-radius: 6px; font-weight: 700; width: fit-content; margin-top: 8px; }
        
        .empty-state { background: #fff; border-radius: 14px; padding: 40px; text-align: center; color: #6b7280; }

        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<header>
    <div class="brand">Практическая работа №5</div>
    <span>ТРПО</span>
</header>

<div class="container">
    <div class="hero">
        <span class="badge">PHP ООП</span>
        <h1>Каталог моделей предметной области</h1>
        <p style="color: #6b7280; margin-top: 6px;">
            Данные загружены из JSON-файлов и преобразованы в строго типизированные объекты Product и Category.
        </p>
    </div>

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