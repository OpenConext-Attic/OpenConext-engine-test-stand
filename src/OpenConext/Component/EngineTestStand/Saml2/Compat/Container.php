<?php

namespace OpenConext\Component\EngineTestStand\Saml2\Compat;

use Psr;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Exception\NotImplementedException;

class Container extends \SAML2_Compat_AbstractContainer
{
    const ID_PREFIX = 'OPENCONEXT_ETS_';

    const DEBUG_TYPE_IN = 'in';
    const DEBUG_TYPE_OUT = 'in';
    const DEBUG_TYPE_ENCRYPT = 'encrypt';
    const DEBUG_TYPE_DECRYPT = 'decrypt';

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var array
     */
    protected $lastDebugMessage = array();

    /**
     * @param string $type
     * @return mixed
     */
    public function getLastDebugMessageOfType($type = self::DEBUG_TYPE_IN)
    {
        return $this->lastDebugMessage[$type];
    }

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
        return self::ID_PREFIX . rand(0, 100000000);
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
        if ($message instanceof \DOMElement) {
            $message = $message->ownerDocument->saveXML();
        }
        $this->lastDebugMessage[$type] = $message;
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
        throw new NotImplementedException('SSP/SAML2 Redirect not implemented! URL: ' . $url);
    }

    /**
     * Trigger the user to perform a POST to the given URL with the given data.
     *
     * @param string $url
     * @param array $data
     * @return $this
     */
    public function postRedirect($url, $data = array())
    {
        $formData = '';
        foreach ($data as $name => $value) {
            $value = htmlentities($value, ENT_COMPAT, 'utf-8');
            $formData .= "            <input name=\"$name\" type=\"text\" value=\"$value\" />" . PHP_EOL;
        }

        if (isset($data['SAMLRequest'])) {
            $requestXml = base64_decode($data['SAMLRequest']);

            $requestXml = self::formatXml($requestXml);

            $data['authnRequestXml'] = $requestXml;
        }
        if (!isset($data['authnRequestXml'])) {
            $data['authnRequestXml'] = 'N/A';
        }

        $responseDebug = '';
        if (isset($data['SAMLResponse'])) {
            $responseXml = base64_decode($data['SAMLResponse']);

            $responseXml = self::formatXml($responseXml);

            $responseDebug = '<pre id="responseDebug">' . htmlentities($responseXml, ENT_QUOTES, 'utf-8')  . '</pre>';
        }

        $this->response = new Response(
<<<HTML
<html>
    <head>
        <title>Redirecting...</title>
    </head>
    <body>
        <pre id="authnRequestXml">{$data['authnRequestXml']}</pre>
        $responseDebug
        <form id="postform" action="{$url}" method="post">
            $formData

            <input type="submit" value="GO" />
        </form>
        <script>setTimeout(function() {document.getElementById('postform').submit();}, 1500);</script>
    </body>
</html>
HTML
        );
        return $this;
    }

    public function getPostResponse()
    {
        return $this->response;
    }

    /**
     * @param $xml
     * @return string
     */
    public static function formatXml($xml)
    {
        $dom = new \DOMDocument;
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($xml);
        $dom->formatOutput = true;
        $xml = $dom->saveXml();
        return $xml;
    }
}
