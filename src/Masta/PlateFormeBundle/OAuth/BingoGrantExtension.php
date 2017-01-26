<?php
namespace Masta\PlateFormeBundle\OAuth;

use FOS\OAuthServerBundle\Storage\GrantExtensionInterface;
use OAuth2\Model\IOAuth2Client;

/**
 * Play at bingo to get an access_token: May the luck be with you!
 */
class BingoGrantExtension implements GrantExtensionInterface
{
    /*
     * {@inheritdoc}
     */
    public function checkGrantExtension(IOAuth2Client $client, array $inputData, array $authHeaders)
    {
        // Check that the input data is correct
        if (!isset($inputData['number_1']) || !isset($inputData['number_2'])) {
            return false;
        }

        $numberToGuess1 = rand(0, 100);
        $numberToGuess2 = rand(0, 100);

        if ($numberToGuess1 != $inputData['number_1'] && $numberToGuess2 != $inputData['number_2']) {
            return false; // No number guessed, grant will fail
        }

        if ($numberToGuess1 == $inputData['number_1'] && $numberToGuess2 == $inputData['number_2']) {
            // Both numbers were guessed, we grant an access_token linked
            // to a user
            return array(
                'data' => $userManager->findRandomUser()
            );
        }

        if ($numberToGuess1 == $inputData['number_1'] || $numberToGuess2 == $inputData['number_2']) {
            // Only one of the numbers were guessed
            // We grant a simple access token

            return true;
        }

        return false; // No number guessed, the grant will fail
    }
}
