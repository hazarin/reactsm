<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $content = '';
        while(strlen($content) < 120) {
            $content .= 'Veni vidi vici. ';
        }

        for ($i = 1; $i < 17; $i++) {
            $item = new Article();
            $item
                ->setTitle('Article title '.$i)
                ->setContent($content);
            $manager->persist($item);
        }

        $manager->flush();
    }
}
