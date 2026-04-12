<?php

declare(strict_types=1);

namespace App\Domain\News\DTOs;

readonly class NewsItemData
{
    public function __construct(
        public string $title,
        public string $link,
        public string $date,
        public string $source,
        public string $summary,
        public ?string $imageUrl = null,
    ) {}

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'link' => $this->link,
            'date' => $this->date,
            'source' => $this->source,
            'summary' => $this->summary,
            'image_url' => $this->imageUrl,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            link: $data['link'],
            date: $data['date'],
            source: $data['source'],
            summary: $data['summary'],
            imageUrl: $data['image_url'] ?? null,
        );
    }
}
