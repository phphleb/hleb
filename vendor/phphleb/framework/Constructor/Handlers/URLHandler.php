<?php

namespace Hleb\Constructor\Handlers;

use Hleb\Main\Functions;

use Hleb\Constructor\Handlers\Request;

class URLHandler
{

    function __construct()
    {

    }


    /**
     * @param array $blocks
     * @return mixed
     */
    public function page(array $blocks)
    {

        if (isset($blocks["update"])) unset($blocks["update"]);

        if (isset($blocks["render"])) unset($blocks["render"]);

        if (isset($blocks["addresses"])) unset($blocks["addresses"]);

        $url = Functions::clearMainUrl();

        $blocks = $this->match_subdomains($blocks);

        if (empty(count($blocks))) {

            /// Подходящего роута по текущему поддомену не найдено
            return false;
        }

        $blocks = $this->match_search_type($blocks);

        if (empty(count($blocks))) {

            /// Подходящего роута по типу REQUEST_METHOD не найдено
            return false;
        }

        return self::match_search_all_path($blocks, $url);


    }

    private function compound_url($strokes)
    {

        return str_replace(["/////", "////", "///", "//"], "/", implode('/', $strokes));

    }

    private function trim_end(string $stroke): string
    {

        if ($stroke{strlen($stroke) - 1} == "/") {
            return substr($stroke, 0, -1);
        }

        return $stroke;

    }

    private function match_subdomains($blocks)
    {

        $host = array_reverse(explode(".", hleb_get_host()));

        if ($host[0] === 'localhost') {
            array_unshift($host, '*');
        }

        $result_blocks = [];

        foreach ($blocks as $block) {

            $search = [];

            $actions = !empty($block["actions"]) ? $block["actions"] : [];

            foreach ($actions as $action) {
                if (!empty($action["domain"])) {
                    $domain_part = $host[intval($action["domain"][1]) - 1] ?? null;
                    if (!$action["domain"][2]) {
                        $valid_domain = 0;
                        foreach ($action["domain"][0] as $domain) {
                            if ($domain_part === '*' || ($domain == null && $domain_part == null) ||
                                ($domain_part != null && strtolower($domain_part) == strtolower($domain))) {
                                $valid_domain++;
                            }
                        }
                        $search[] = $valid_domain > 0;
                    } else {
                        $valid_domain = 0;
                        foreach ($action["domain"][0] as $domain) {
                            if ($domain_part === '*' || ($domain == null && $domain_part == null)) {
                                $valid_domain++;
                            } else if ($domain_part != null) {
                                preg_match("/^" . $domain . "$/", strtolower($domain_part), $matches);
                                if (count($matches) && $matches[0] == strtolower($domain_part)) {
                                    $valid_domain++;
                                }
                            }
                        }
                        $search[] = $valid_domain > 0;
                    }
                }
                if (!in_array(false, $search)) break;
            }

            if (count($search) == 0 || !in_array(false, $search)) $result_blocks[] = $block;
        }

        return $result_blocks;
    }


    private function match_search_type($blocks)
    {

        $real_type = strtolower($_SERVER['REQUEST_METHOD']);

        $result_blocks = [];


        foreach ($blocks as $block) {

            $type = [];

            $actions = !empty($block["actions"]) ? $block["actions"] : [];

            foreach ($actions as $action) {

                if (!empty($action["type"])) { // Определяется тип действия

                    $action_types = $action["type"];

                    foreach ($action_types as $action_type) {

                        $type[] = $action_type;
                    }

                }

            }

            if (count($type) == 0) {

                $type = !empty($block["type"]) ? $block["type"] : [];

            }


            if (count($type) == 0) {

                $type = ["get"];
            }

            if (in_array($real_type, $type)) {

                $result_blocks[] = $block;

            }
        }

        return $result_blocks;
    }

    /**
     * @param array $blocks
     * @param string $result_url
     * @return bool|array
     */
    private function match_search_all_path($blocks, $result_url)
    {

        foreach ($blocks as $key => $block) {

            $result = self::match_search_path($block, $result_url);

            if ($result !== false) return $result;

        }

        return false;
    }

    /**
     * @param array $block
     * @param string $result_url
     * @return bool|array
     */
    private function match_search_path($block, $result_url)
    {

        $url = '';

        $actions = $block["actions"] ?? [];

        $mat = [];

        foreach ($actions as $action) {

            if (isset($action["prefix"])) {

                $url = self::compound_url([$url, $action["prefix"]]);

            } else if (isset($action["where"]) && count($action["where"][0]) > 0) {

                foreach ($action["where"][0] as $key => $value) {

                    $mat[$key] = $value;
                }

            }

        }

        $origin_url = self::compound_url([$url, $block["data_path"] ?? ""]);

        $url = self::trim_end($origin_url);

        $result_url = self::trim_end($result_url);


        $result_url_parts = array_reverse(explode("/", $result_url));

        $url_parts = array_reverse(explode("/", $url));

        $result_shift = array_shift($url_parts);

        // /.../.../ или /.../...?/

        if ($result_url == trim($url, "?") ||
            ($result_shift{strlen($result_shift) - 1} === "?" && implode($result_url_parts) === implode($url_parts))) {
            // Прямое совпадение
            return $block;

        } else {
            // Если есть вариативность в маршруте /{...}/, /{...?}/ или where(...)

            if (count($mat) > 0 || strpos($url, '{') !== false) {

                $generate_real_urls = explode("/", $result_url);

                $generate_urls = explode("/", $url);

                if (count($generate_real_urls) !== count($generate_urls) &&
                    !(($result_shift{strlen($result_shift) - 2} == "?" || $result_shift{strlen($result_shift) - 1} == "?") &&
                        count($generate_real_urls) + 1 == count($generate_urls))) {
                    // Не совпадает длина маршрута с url

                    return false;

                }

                foreach ($generate_urls as $q => $generate_url) {

                    $generate_real_urls[$q] = $generate_real_urls[$q] ?? "";

                    if (!empty($generate_url)) {

                        if ($generate_url{0} == "{" && $generate_url{strlen($generate_url) - 1} == "}") {

                            $exp = trim($generate_url, "{?}");

                            if (isset($mat[$exp])) {

                                if (!(empty($generate_real_urls[$q]) && $generate_url{strlen($generate_url) - 2} === "?")) {

                                    preg_match("/^" . $mat[$exp] . "$/", $generate_real_urls[$q], $matches);

                                    if (empty($matches[0]) || $matches[0] != $generate_real_urls[$q]) {

                                        return false;
                                    }
                                }
                            } else {
                                // Предупреждение (?)
                            }

                            Request::add($exp, $generate_real_urls[$q]);

                        } else {
                            // Есть вариативность, но и есть прямые совпадения:
                            if (!(empty($generate_real_urls[$q]) && $generate_url{strlen($generate_url) - 1} == "?")) {
                                if (trim($generate_url, "?") !== $generate_real_urls[$q]) {

                                    return false;
                                }
                            }
                        }
                    }
                } // foreach

                return $block;
            }

        }
        return false;
    }


}