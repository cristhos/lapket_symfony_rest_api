<?php
namespace Masta\PlateFormeBundle\Utils\Beta;

use Symfony\Component\HttpFoundation\Response;

class BetaHTMLAdder
{
  // Méthode pour ajouter le « bêta » à une réponse
  public function addBeta(Response $response, $remainingDays)
  {
    $content = $response->getContent();
    $html = '<div style="position: fixed; bottom:0;z-index:7; color: white; background: red; width: 10%; text-align: center; padding: 0.5em;">Beta J-'.(int) $remainingDays.' !</div>';
    $content = str_replace(
      '<body>',
      '<body> '.$html,
      $content
    );
    $response->setContent($content);
    return $response;
  }
}
