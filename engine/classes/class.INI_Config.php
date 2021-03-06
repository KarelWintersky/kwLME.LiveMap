<?php

/**
 * Class INI_Config, version 1.0.1
 */
class INI_Config
{
    const GLUE = '/';
    private $config = array();

    /**
     * @param $filepath
     * @param string $subpath
     */
    public function __construct($filepath, $subpath = '')
    {
        $this->init($filepath, $subpath);
    }

    /**
     * @param $filepath
     * @param string $subpath
     */
    public function init($filepath, $subpath = '')
    {
        if (file_exists($filepath)) {
            $new_config = parse_ini_file($filepath, true);

            if ($subpath == "" || $subpath == $this::GLUE) {
                foreach ($new_config as $key => $part) {
                    if (array_key_exists($key, $this->config)) {
                        $this->config[$key] = array_merge($this->config[$key], $part);
                    } else {
                        $this->config[$key] = $part;
                    }
                }

            } else {
                $this->config["{$subpath}"] = $new_config;
            }

            unset($new_config);
        } else {
            $message = "<strong>FATAL ERROR:</strong> Config file `{$filepath}` not found. " . PHP_EOL;

            if (class_exists('CLIConsole') && method_exists('CLIConsole', 'echo_status') ) {
                CLIConsole::echo_status($message);
                die(1);
            } else {
                if (php_sapi_name() === "cli") {
                    $message = strip_tags($message);
                }
                die($message);
            }
        }
    }

    /**
     * @param $filepath
     * @param string $subpath
     */
    public function append($filepath, $subpath = '')
    {
        $this->init($filepath, $subpath);
    }

    /**
     * @param $parents
     * @param null $default_value
     * @return array|null
     */
    public function get($parents, $default_value = NULL)
    {
        if ($parents === '') {
            return $default_value;
        }

        if (!is_array($parents)) {
            $parents = explode($this::GLUE, $parents);
        }

        $ref = &$this->config;

        foreach ((array) $parents as $parent) {
            if (is_array($ref) && array_key_exists($parent, $ref)) {
                $ref = &$ref[$parent];
            } else {
                return $default_value;
            }
        }
        return $ref;
    }

    /**
     * @param $parents
     * @param $value
     * @return bool
     */
    public function set($parents, $value)
    {
        if (!is_array($parents)) {
            $parents = explode($this::GLUE, (string) $parents);
        }

        if (empty($parents)) return false;

        $ref = &$this->config;

        foreach ($parents as $parent) {
            if (isset($ref) && !is_array($ref)) {
                $ref = array();
            }

            $ref = &$ref[$parent];
        }

        $ref = $value;
        return true;
    }

    /**
     * @param array $array
     * @param array|string $parents
     */
    private function array_unset_value(&$array, $parents)
    {
        if (!is_array($parents)) {
            $parents = explode($this::GLUE, $parents);
        }

        $key = array_shift($parents);

        if (empty($parents)) {
            unset($array[$key]);
        } else {
            $this->array_unset_value($array[$key], $parents);
        }
    }

    /**
     * @param $parents
     */
    public function delete($parents)
    {
        $this->array_unset_value($this->config, $parents);
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->config;
    }

}

/* end class.INI_Config.php */