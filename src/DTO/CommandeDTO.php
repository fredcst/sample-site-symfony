<?php

namespace App\DTO;

class CommandeDTO
{
    public int $id;
    public string $title;
    public string $description;
    public string $createdAt;
    public string $createdBy;

    public function __construct(
        int $id,
        string $title,
        string $description,
        string $createdAt,
        string $createdBy
    )
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->createdAt = $createdAt;
        $this->createdBy = $createdBy;
    }
}
