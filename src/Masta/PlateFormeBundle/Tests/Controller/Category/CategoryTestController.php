<?php

namespace Masta\PlateFormeBundle\Tests\Controller\Category;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryControllerTest extends WebTestCase
{
   public function setUp()
   {
        parent::setup();
        $fixtures = array(
          'Masta\PlateFormeBundle\DataFixtures\ORM\LoadUserCommentData',
        );
        $this->loadFixtures($fixtures);
    }
  }
}
