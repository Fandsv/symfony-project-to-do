<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateTaskDto
{
    #[Assert\NotBlank(message: "Title обязательно")]
    #[Assert\Length(max: 255)]
    public ?string $title = null;

    #[Assert\NotBlank(message: "Description обязательно")]
    #[Assert\Length(max: 1000)]
    public ?string $description = null;

    #[Assert\Type('bool')]
    public ?bool $isDone = null;
}
