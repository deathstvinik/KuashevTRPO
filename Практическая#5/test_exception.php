<?php

declare(strict_types=1);

require_once __DIR__ . '/src/Model/Product.php';

use Model\Product;
use InvalidArgumentException;

echo "--- ТЕСТ: Попытка создать товар с отрицательной ценой ---\n";

try {
    // Передаем заведомо неверную цену: -150.00
    $invalidProduct = new Product(999, 'Тестовый бракованный товар', -150.0, 1);
    echo "ОШИБКА ТЕСТА: Объект создался, а не должен был!\n";
} catch (InvalidArgumentException $e) {
    echo "УСПЕХ ТЕСТА: Конструктор заблокировал создание объекта!\n";
    echo "Поймано исключение: " . $e->getMessage() . "\n";
}