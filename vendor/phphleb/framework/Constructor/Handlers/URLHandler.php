<?php

namespace Hleb\Constructor\Handlers;

class URLHandler
{

    function __construct()
    {

    }

    protected $adm_blocks = [];

    /**
     * @param array $blocks
     * @return mixed
     */
    public function page(array $blocks)
    {

        if (isset($blocks['update'])) unset($blocks['update']);

        if (isset($blocks['render'])) unset($blocks['render']);

        if (isset($blocks['addresses'])) unset($blocks['addresses']);

        $search_domains = $blocks['domains'] ?? false;

        if (isset($blocks['domains'])) unset($blocks['domains']);

        $url = Request::getMainClearUrl();

        $blocks = $search_domains ? $this->match_subdomains($blocks) : $blocks;

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

        return preg_replace('#(/){2,}#', '/', implode('/', $strokes));

    }

    private function trim_end(string $stroke): string
    {
        if ($stroke[strlen($stroke) - 1] === '/') {
            return substr($stroke, 0, -1);
        }

        return $stroke;
    }

    private function match_subdomains($blocks)
    {

        $host = array_reverse(explode('.', hleb_get_host()));

        if ($host[0] === 'localhost') {
            array_unshift($host, '*');
        }

        $result_blocks = [];

        foreach ($blocks as $key => $block) {

            $search = [];

            $actions = !empty($block['actions']) ? $block['actions'] : [];

            foreach ($actions as $k => $action) {
                if (!empty($action['domain'])) {
                    $domain_part = $host[intval($action['domain'][1]) - 1] ?? null;
                    if (!$action['domain'][2]) {
                        $valid_domain = 0;
                        foreach ($action['domain'][0] as $domain) {
                            if ($domain_part === '*' || (is_null($domain) && is_null($domain_part)) ||
                                ($domain_part != null && strtolower($domain_part) == strtolower($domain))) {
                                $valid_domain++;
                            }
                        }
                        $search[] = $valid_domain > 0;
                    } else {
                        $valid_domain = 0;
                        foreach ($action['domain'][0] as $domain) {
                            if ($domain_part === '*' || (is_null($domain) && is_null($domain_part))) {
                                $valid_domain++;
                            } else if ($domain_part != null) {
                                preg_match('/^' . $domain . '$/u', strtolower($domain_part), $matches);
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

        if(!in_array($real_type, HLEB_HTTP_TYPE_SUPPORT)){

            if (!headers_sent()) {
                header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
                header('Allow: ' . strtoupper(implode(',',HLEB_HTTP_TYPE_SUPPORT)));
                header('Content-length: 0');
            }
            exit();
        }


        $result_blocks = [];

        $admin_pan_data = [];

        foreach ($blocks as $key => $block) {

            $type = [];

            $actions = !empty($block['actions']) ? $block['actions'] : [];

            foreach ($actions as $kt => $action) {

                if (!empty($action['type'])) { // Определяется тип действия

                    $action_types = $action['type'];

                    foreach ($action_types as $k => $action_type) {

                        $type[] = $action_type;
                    }

                }

                if (isset($action['adminPanController'])) {

                    $admin_pan_data[] = $block;
                }

            }

            if (count($type) === 0) {

                $type = !empty($block['type']) ? $block['type'] : [];

            }


            if (count($type) === 0) {

                $type = ['get'];
            }

            if (in_array($real_type, $type) || $real_type == 'options') {

                $result_blocks[] = $block;

            }
        }

        foreach($result_blocks as &$result_block){

            $result_block['_AdminPanelData'] = $admin_pan_data;
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
        $result_url_parts = array_reverse(explode('/', $result_url));

        $url = self::trim_end($result_url);

        foreach ($blocks as $key => $block) {

            $result = self::match_search_path($block, $url, $result_url_parts);

            if ($result !== false) return $result;

        }

        return false;
    }

    /**
     * @param array $block
     * @param string $result_url
     * @param array $result_url_parts
     * @return bool|array
     */
    private function match_search_path($block, $result_url, $result_url_parts)
    {

        $url = '';

        $actions = $block['actions'] ?? [];

        $mat = [];

        foreach ($actions as $k => $action) {

            if (isset($action['prefix'])) {

                $url = self::compound_url([$url, $action['prefix']]);

            } else if (isset($action['where']) && count($action['where'][0]) > 0) {

                foreach ($action['where'][0] as $key => $value) {

                    $mat[$key] = $value;
                }

            }

        }

        $origin_url = self::compound_url([$url, $block['data_path'] ?? '']);

        $url = self::trim_end($origin_url);


        $url_parts = array_reverse(explode('/', $url));

        $result_shift = array_shift($url_parts);

        // /.../.../ или /.../...?/

        if ($result_url == trim($url, '?') ||
            (strlen($result_shift) && $result_shift[strlen($result_shift) - 1] === '?' && implode($result_url_parts) === implode($url_parts))) {
            // Прямое совпадение
            return $block;

        } else {
            // Если есть вариативность в маршруте /{...}/, /{...?}/ или where(...)

            if (count($mat) > 0 || strpos($url, '{') !== false) {

                $generate_real_urls = explode('/', $result_url);

                $generate_urls = explode("/", $url);

                if (count($generate_real_urls) !== count($generate_urls) &&
                    !(($result_shift[strlen($result_shift) - 2] == '?' || $result_shift[strlen($result_shift) - 1] == '?') &&
                        count($generate_real_urls) + 1 == count($generate_urls))) {
                    // Не совпадает длина маршрута с url

                    return false;

                }

                foreach ($generate_urls as $q => $generate_url) {

                    $generate_real_urls[$q] = $generate_real_urls[$q] ?? '';

                    if (!empty($generate_url)) {

                        if ($generate_url[0] === '{' && $generate_url[strlen($generate_url) - 1] === '}') {

                            $exp = trim($generate_url, '{?}');

                            if (isset($mat[$exp])) {

                                if (!(empty($generate_real_urls[$q]) && $generate_url[strlen($generate_url) - 2] === '?')) {

                                    preg_match('/^' . $mat[$exp] . '$/u', $generate_real_urls[$q], $matches);

                                    if (!isset($matches[0]) || $matches[0] != $generate_real_urls[$q]) {

                                        return false;
                                    }
                                }
                            }
                            Request::add($exp, $generate_real_urls[$q]);

                        } else {
                            // Есть вариативность, но и есть прямые совпадения:
                            if (!(empty($generate_real_urls[$q]) && $generate_url[strlen($generate_url) - 1] === '?')) {
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