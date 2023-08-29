<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping AS ORM;

trait CreatedAtTrait
{
    #[ORM\Column(type: 'datetime_immutable', options:['default' => 'CURRENT_TIMESTAMP'])]
    // CURRENT_TIMESTAMP -> pour definir la date du jour
    private $created_at;

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

}