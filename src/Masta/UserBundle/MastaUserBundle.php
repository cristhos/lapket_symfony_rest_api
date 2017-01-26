<?php

namespace Masta\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MastaUserBundle extends Bundle
{
	public function getParent()
    {
        return 'FOSUserBundle';
    }
}
