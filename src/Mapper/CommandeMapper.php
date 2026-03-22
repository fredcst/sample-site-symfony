<?php

namespace App\Mapper;

use App\DTO\CommandeDTO;
use App\Entity\Commande;

class CommandeMapper
{
    public static function toDTO(Commande $commande){
        return new CommandeDTO(
            $commande->getId(),
            $commande->getTitle(),
            $commande->getDescription(),
            $commande->getCreatedAt()->format('Y-m-d H:i:s'),
            $commande->getCreatedBy()->getEmail(),
        );
    }
}
