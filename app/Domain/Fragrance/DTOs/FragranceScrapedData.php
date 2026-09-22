<?php

declare(strict_types=1);

namespace App\Domain\Fragrance\DTOs;

class FragranceScrapedData
{
    /**
     * @param  array<int, array{name: string, percentage: float, color: string}>  $accords
     * @param  array<int, array{layer: string, name: string, image_url: ?string}>  $notes
     * @param  array<string, int>|null  $seasons
     * @param  array<string, int>|null  $dayNight
     */
    public function __construct(
        public readonly int $fragranticaId,
        public readonly string $fragranticaUrl,
        public readonly string $name,
        public readonly ?string $brand,
        public readonly string $gender,
        public readonly ?string $description,
        public readonly ?string $inspiredBy,
        public readonly ?string $concentration,
        public readonly ?int $year,
        public readonly ?string $imageUrl,
        public readonly ?string $brandLogoUrl,
        public readonly ?float $rating,
        public readonly int $votesCount,
        public readonly array $accords,
        public readonly array $notes,
        public readonly ?array $seasons,
        public readonly ?array $dayNight,
    ) {}

    public function toProductArray(): array
    {
        return [
            'fragrantica_id'  => $this->fragranticaId,
            'fragrantica_url' => $this->fragranticaUrl,
            'name'            => $this->name,
            'brand'           => $this->brand,
            'gender'          => $this->gender,
            'description'     => $this->description,
            'inspired_by'     => $this->inspiredBy,
            'concentration'   => $this->concentration,
            'year'            => $this->year,
            'image_url'       => $this->imageUrl,
            'brand_logo_url'  => $this->brandLogoUrl,
            'rating'          => $this->rating,
            'votes_count'     => $this->votesCount,
            'seasons'         => $this->seasons,
            'day_night'       => $this->dayNight,
            'scraped_at'      => now(),
        ];
    }
}
