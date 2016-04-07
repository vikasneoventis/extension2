<?php

namespace Kahanit\AweMenu\Helper;

class AweMenu extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function stripslashes($string)
    {
        $search = array("\\", "\0", "\r", "\x1a", "'", '"');
        $replace = array("\\\\", "\\0", "\\r", "\Z", "\'", '\"');

        return str_replace($replace, $search, $string);
    }

    public function filterSearchQuery($search_query = '', $keys = [])
    {
        $search_query = trim($search_query);
        $search_items = [];

        foreach ($keys as $key) {
            $pattern = '/' . $key . '\s*:\s*[^:]*((?=' . implode('\s*:)|(?=', $keys) . '\s*:)|($))/i';
            preg_match($pattern, $search_query, $matches);

            if (isset($matches[0])) {
                $search_value = preg_replace('/\s+/', ' ', $matches[0]);
            } else {
                $search_value = '';
            }

            $search_value = explode(':', $search_value);
            $search_items[$key] = end($search_value);
            $search_items[$key] = trim($search_items[$key]);
        }

        $search_items['query'] = $search_query;

        return $search_items;
    }
}
