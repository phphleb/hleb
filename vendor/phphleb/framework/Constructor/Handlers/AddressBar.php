<?php

declare(strict_types=1);

/*
 * URL validation and parsing.
 *
 * Действия по валидации и разбору URL.
 */

namespace Hleb\Constructor\Handlers;

use Hleb\Idnaconv\IdnaConvert;

final class AddressBar
{
    private $inputParameters;

    /** @internal */
    public $redirect = null;

    /** @internal */
    public $realUrl = null;


    // Initialization with input parameters.
    // Инициализация с вводными параметрами.
    /** @internal */
    public function __construct(array $params) {
        $this->inputParameters = $params;
    }

    // Parses data to check the current URL, otherwise marks it as a redirect to the correct one.
    // Выполняет разбор данных для проверки текущего URL, в противном случае отмечается как редирект на правильный.
    /** @internal */
    public function get() {
        $partsOfAddress = explode('?', $this->inputParameters['SERVER']['REQUEST_URI']);
        $address = rawurldecode(array_shift($partsOfAddress));
        $realParameters = count($partsOfAddress) > 0 ? '?' . implode('?', $partsOfAddress) : ''; // params
        $actualProtocol = $this->inputParameters['HTTPS'];
        $realProtocol = $this->inputParameters['HLEB_PROJECT_ONLY_HTTPS'] ? 'https://' : $actualProtocol; // protocol
        define('HLEB_PROJECT_PROTOCOL', $realProtocol);
        $endElement = explode('/', $address);
        $fileUrl = stripos(end($endElement), '.') !== false;
        $relAddress = "";
        if (!empty($address)) {
            if (!$fileUrl) {
                if ($address[strlen($address) - 1] === '/') {
                    $relAddress = $this->inputParameters['HLEB_PROJECT_ENDING_URL'] ? $address : rtrim($address, "/");
                } else {
                    $relAddress = $this->inputParameters['HLEB_PROJECT_ENDING_URL'] ? $address . '/' : $address;
                }
            } else {
                $relAddress = $address;
            }
        }

        // Processing domains with Cyrillic letters.
        // Обработка доменов с кириллицей.
        $host = $this->inputParameters['SERVER']['HTTP_HOST'];
        $idn = null;
        define('HLEB_MAIN_DOMAIN_ORIGIN', $host);
        if (stripos($host, 'xn--') !== false) {
            require $this->inputParameters['HLEB_PROJECT_DIRECTORY'] . '/Idnaconv/IdnaConvert.php';
            $idn = new IdnaConvert();
            $host = $idn->decode($host);
        }

        $partsOfHost = explode('.', $host);
        if ($this->inputParameters['HLEB_PROJECT_GLUE_WITH_WWW'] == 1) {
            if ($partsOfHost[0] == 'www') array_shift($partsOfHost);
        } else if ($this->inputParameters['HLEB_PROJECT_GLUE_WITH_WWW'] == 2) {
            if ($partsOfHost[0] != 'www') $partsOfHost = array_merge(['www'], $partsOfHost);
        }
        $realHostWww = implode('.', $partsOfHost); // host
        define("HLEB_MAIN_DOMAIN", $host);

        // Check if the address is valid.
        // Проверка на валидность адреса.
        if (!preg_match($this->inputParameters['HLEB_PROJECT_VALIDITY_URL'], $address)) {
            $realUrlMain = $realProtocol . $realHostWww;
            $this->redirect($realUrlMain);
            return $realUrlMain;
        }

        // Check if the URL is correct.
        // Проверка на корректность URL.
        $realUrl = $realProtocol . (preg_replace('/\/{2,}/', '/', $realHostWww . $relAddress)) . $realParameters;

        $partsOfActualUri = explode('?', $this->inputParameters['SERVER']['REQUEST_URI']);
        $firstActualUri = rawurldecode(array_shift($partsOfActualUri));
        $firstActualParams = count($partsOfActualUri) > 0 ? '?' . implode('?', $partsOfActualUri) : '';
        $actualHost = strval(is_null($idn) ? $this->inputParameters['SERVER']['HTTP_HOST'] : $idn->decode($this->inputParameters['SERVER']['HTTP_HOST']));
        $actualUrl = $actualProtocol . strval($actualHost) . $firstActualUri . $firstActualParams;
        if(empty($relAddress)) {
            $realUrl =  rtrim($realUrl, '/');
            $actualUrl = rtrim($actualUrl, '/');
        }
        if ($realUrl !== $actualUrl && $address !== '/') {
            $this->redirect($realUrl);
        }
        return $realUrl;
    }

    // Sets the address to which the redirect is needed.
    // Устанавливает адрес на который необходим редирект.
    private function redirect($realUrl) {
        $this->redirect = $realUrl;
    }
}

