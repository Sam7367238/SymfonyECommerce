<?php

namespace App\EntityListener;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsEntityListener(event: Events::prePersist, entity: Product::class)]
#[AsEntityListener(event: Events::preUpdate, entity: Product::class)]
final readonly class ProductEntityListener
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function prePersist(Product $product, LifecycleEventArgs $event): void
    {
        $product->computeSlug($this->slugger);
    }

    public function preUpdate(Product $product, LifecycleEventArgs $event): void
    {
        $product->computeSlug($this->slugger);
    }
}
