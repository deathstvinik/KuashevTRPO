<?php

declare(strict_types=1);

namespace Service;

use Model\Product;
use Model\Category;
use RuntimeException;

final class CatalogService
{
    public function __construct(
        private string $dataPath
    ) {}

    public function getCategories(): array
    {
        $filePath = rtrim($this->dataPath, '/\\') . '/categories.json';

        if (!file_exists($filePath)) {
            throw new RuntimeException("Файл категорий не найден: {$filePath}");
        }

        $rawJson = file_get_contents($filePath);
        $data = json_decode($rawJson, true);

        if (!is_array($data)) {
            throw new RuntimeException("Ошибка чтения JSON из {$filePath}");
        }

        $categories = [];
        foreach ($data as $item) {
            $categories[] = new Category(
                (int)$item['id'],
                (string)$item['name']
            );
        }

        return $categories;
    }

    public function getProducts(?int $categoryId = null): array
    {
        $filePath = rtrim($this->dataPath, '/\\') . '/products.json';

        if (!file_exists($filePath)) {
            throw new RuntimeException("Файл товаров не найден: {$filePath}");
        }

        $rawJson = file_get_contents($filePath);
        $data = json_decode($rawJson, true);

        if (!is_array($data)) {
            throw new RuntimeException("Ошибка чтения JSON из {$filePath}");
        }

        $products = [];
        foreach ($data as $item) {
            $product = new Product(
                (int)$item['id'],
                (string)$item['name'],
                (float)$item['price'],
                (int)$item['category_id']
            );

            if ($categoryId !== null && !$product->belongsToCategory($categoryId)) {
                continue;
            }

            $products[] = $product;
        }

        return $products;
    }

    public function getAveragePrice(array $products): float
    {
        if (empty($products)) {
            return 0.0;
        }

        $totalPrice = 0.0;
        foreach ($products as $product) {
            $totalPrice += $product->getPrice();
        }

        return round($totalPrice / count($products), 2);
    }

    public function getTotalCount(array $products): int
    {
        return count($products);
    }