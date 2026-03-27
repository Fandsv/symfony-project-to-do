<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateTaskDto
{
    #[Assert\Length(max: 255)]
    public ?string $title = null;

    #[Assert\Length(max: 1000)]
    public ?string $description = null;

    #[Assert\Type('bool')]
    public ?bool $isDone = null;
}
