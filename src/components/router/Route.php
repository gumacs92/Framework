<?php
/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-12-07
 * Time: 08:05 PM
 */

namespace Framework\Components\Router;


use Framework\Abstractions\Exceptions\RouteException;

class Route
{
    //TODO short syntax
    //TODO router groups
    //TODO namign routes
    //TODO defaults
    private $uriTemplate;
    private $uriSettings;
    private $uriName;

    private $predefinedParams = [
        ":namespace" => '(?<namespace>\/[a-zA-Z0-9\_\-]+)',
        ":module" => '(?<module>\/[a-zA-Z0-9\_\-]+)',
        ":controller" => '(?<controller>\/[a-zA-Z0-9\_\-]+)',
        ":action" => '(?<action>\/[a-zA-Z0-9\_]+)',
        ":params" => '(?<params>\/.*)*',
        ":int" => '(?<int>\/[0-9]+)'
    ];

    public function __construct($uri, $uri_settings, $uri_name = '')
    {
        if (!is_array($uri_settings)) {
            throw new RouteException("Fatal error: uri settings have to be an array");
        }

        $this->uriTemplate = self::validateUriTemplate($uri, $uri_settings);
        $this->uriSettings = $uri_settings;
        $this->uriName = $uri_name;
    }

    public static function validateUriTemplate($uri, $uri_settings)
    {
        $has_params = false;
        $uri_pieces = explode('/', ltrim($uri, '/'));
        $number_of_templates = 0;
        $templates_given = 0;
        $template_positions = [];
        $templates_in_order = [];

        foreach ($uri_pieces as $piece) {
            if (!preg_match('/^([\/]?(:namespace|:module|:controller|:action|:params|:int|(\#[\S]+\#)|[a-zA-Z0-9\_\-]))+$/', $piece)) {
                throw new RouteException("Fatal error: Invalid uri: template is malformed");
            }

            if (preg_match('/^(:namespace|:module|:controller|:action|:params|:int|(\#[\S]+\#))$/', $piece)) {
                $number_of_templates++;
                $templates_in_order[] = $piece;
            }

            if ($piece === ":params") {
                $has_params = true;
            }
        }

        if (!array_key_exists('controller', $uri_settings) || !array_key_exists('action', $uri_settings)) {
            throw new RouteException("Fatal error: Invalid uri: a controller and an action is required");
        }

        if ($has_params && $uri_pieces[sizeof($uri_pieces) - 1] !== ':params') {
            throw new RouteException("Fatal error: Invalid uri: :params is required to be the last in the uri");
        }

        foreach ($uri_settings as $name => $setting) {
            if (is_int($setting)) {
                if (array_key_exists($setting, $template_positions)) {
                    throw new RouteException('Fatal error: Invalid uri: a template position can only be referenced once');
                } else {
                    $template_positions[$setting] = $name;
                    $templates_given++;
                }
            }
        }

        if ($number_of_templates !== $templates_given) {
            throw new RouteException('Fatal error: Invalid uri: not every template in the uri is referenced');
        }

        foreach ($template_positions as $num => $name) {
            if ($template_positions[$num] !== ltrim($templates_in_order[$num - 1], ':') && !preg_match('/^(\#[\S]+\#)$/', $templates_in_order[$num - 1])) {
                throw new RouteException("Fatal error: Invalid uri: named template is at an invalid referenced position");
            }
        }

        return $uri;
    }

    public function matchTemplateUri($uri)
    {
        $uri_template_pieces = explode('/', ltrim($this->uriTemplate, '/'));
        $matches = [];
        $offset = 0;
        $i = 0;

        $regex = '/';
        foreach ($uri_template_pieces as $template) {
            if (array_key_exists($template, $this->predefinedParams)) {
                $regex .= $this->predefinedParams[$template];
                $i++;
            } else {
                if (!preg_match('/^(\#[\S]+\#)$/', $template)) {
                    $regex .= '(\/' . $template . ')';
                } else {
                    $i++;
                    $offset++;
                    $regex .= '(?<' . array_search($i, $this->uriSettings) . '>\/' . trim($template, '#') . ')';
                }
            }
        }
        $regex .= '/';

        if (preg_match($regex, $uri, $matches)) {
            foreach ($this->uriSettings as $name => $value) {
                if (is_int($value)) {
                    $this->uriSettings[$name] = ltrim($matches[$name], '/');
                }
            }
            return true;
        } else {
            return false;
        }
    }

    public function execute($uri = '')
    {
        if (empty($uri)) {
            $uri = $_REQUEST['_uri'];
        }

        if ($this->matchTemplateUri($uri)) {
            $controller_name =
                isset($this->uriSettings['namespace']) ? $this->uriSettings['namespace'] . '\\' : '' .
                isset($this->uriSettings['module']) ? $this->uriSettings['module'] . '\\'  : '' .
                    $this->uriSettings['controller'] . 'Controller';
            $action_name = $this->uriSettings['action'] . 'Action';
            $int = isset($this->uriSettings['int']) ? $this->uriSettings['int'] : null;
            $params = isset($this->uriSettings['params']) ? explode('/', $this->uriSettings['params']) : null;

            $arguments[] = $int;
            $arguments[] = $params;

            $controller = new $controller_name();
            $controller->$action_name(...$arguments);

            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getUriSettings()
    {
        return $this->uriSettings;
    }
}