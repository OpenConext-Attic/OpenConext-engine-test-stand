<?php

namespace OpenConext\EngineTestStand\Saml2\Compat;

use Psr;
use Symfony\Component\HttpFoundation\Response;

class Container extends \SAML2_Compat_AbstractContainer
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * Get a PSR-3 compatible logger.
     * @return Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return new SyslogLogger();
    }

    /**
     * Generate a random identifier for identifying SAML2 documents.
     */
    public function generateId()
    {
        return 'OPENCONEXT_ETS_' . rand(0, 100000000);
    }

    /**
     * Log an incoming message to the debug log.
     *
     * Type can be either:
     * - **in** XML received from third party
     * - **out** XML that will be sent to third party
     * - **encrypt** XML that is about to be encrypted
     * - **decrypt** XML that was just decrypted
     *
     * @param string $message
     * @param string $type
     * @return void
     */
    public function debugMessage($message, $type)
    {
        $this->getLogger()->debug($type . ': ' . $message);
    }

    /**
     * Trigger the user to perform a GET to the given URL with the given data.
     *
     * @param string $url
     * @param array $data
     * @return void
     */
    public function redirect($url, $data = array())
    {
        header('Location: ' . $url . (empty($data) ? '' : http_build_query($data)));
    }

    /**
     * Trigger the user to perform a POST to the given URL with the given data.
     *
     * @param string $url
     * @param array $data
     * @return void
     */
    public function postRedirect($url, $data = array())
    {
        $formData = '';
        foreach ($data as $name => $value) {
            $value = htmlentities($value, ENT_COMPAT, 'utf-8');
            $formData .= "            <input type=\"text\" value=\"$value\" />" . PHP_EOL;
        }

        $this->response = new Response(
<<<HTML
<html>
    <head>
        <title>Redirecting...</title>
    </head>
    <body>
        <form id="postform" action="{$url}" method="post">
            $formData
        </form>
        <script>setTimeout(function() {document.getElementById('postform').submit();}, 1500);</script>
    </body>
</html>
HTML
        );
    }

    public function getPostResponse()
    {
        return $this->response;
    }
}
