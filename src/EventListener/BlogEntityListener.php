<?php

// src/EventListener/BlogEntityListener.php
namespace App\EventListener;

use App\Entity\Blog;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Blog::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Blog::class)]
class BlogEntityListener
{
    public function prePersist(Blog $blog): void
    {
        $this->setSlugFromTitle($blog);
    }

    public function preUpdate(Blog $blog): void
    {
        $this->setSlugFromTitle($blog);
    }

    public function setSlugFromTitle(Blog $blog): void
    {
        $titleEn = $blog->getTitleEN();

        if ($titleEn === null) {
            // Handle null title case here, either throw an exception or use a default string
            throw new \LogicException('TitleEN cannot be null');
        }

        $slug = $this->slugify($titleEn);
        $blog->setSlug($slug);
    }

    private function slugify(string $string): string
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
    }
}