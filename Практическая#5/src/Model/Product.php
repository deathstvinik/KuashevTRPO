<?php

declare(strict_types=1);

namespace Model;

use InvalidArgumentException;

final class Product
{
    public function __construct(
        private int $id,
        private string $name,
        private float $price,
        private int $categoryId
    ) {
        if ($this->id <= 0) {
            throw new InvalidArgumentException('ID товара должен быть больше нуля.');
        }

        if (trim($this->name) === '') {
            throw new InvalidArgumentException('Название товара не может быть пустым.');
        }

        if ($this->price < 0) {
            throw new InvalidArgumentException('Цена товара не может быть отрицательной.');
        }

        if ($this->categoryId <= 0) {
            throw new InvalidArgumentException('ID категории должен быть больше нуля.');
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function getFormattedPrice(): string
    {
        return number_format($this->price, 2, '.', ' ') . ' ₽';
    }

    public function calculateDiscountPrice(float $percent): float
    {
        if ($percent < 0 || $percent > 100) {
            throw new InvalidArgumentException('Процент скидки должен быть от 0 до 100.');
        }

        return round($this->price * (1 - $percent / 100), 2);
    }

    public function belongsToCategory(int $categoryId): bool
    {
        return $this->categoryId === $categoryId;
    }

    public function isPremium(float $threshold = 1000.0): bool
    {
        return $this->price >= $threshold;
    }
}