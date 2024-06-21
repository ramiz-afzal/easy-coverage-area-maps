<?php

namespace PLUGIN_NAMESPACE\Base;

use PLUGIN_NAMESPACE\Base\Variable;

if (!defined('ABSPATH')) exit;

class Functions
{

    public static function generate_uuid($length = 16)
    {
        $uuid = bin2hex(random_bytes(intval(floor($length / 2))));
        return $uuid;
    }

    public static function register_uuid()
    {
        $directory = Variable::GET('PATH') . 'assets/other';
        $filename  = 'uuid.txt';
        $filepath  = $directory . '/' . $filename;

        if (file_exists($filepath)) {
            unlink($filepath);
        }

        // save uuid
        $uuid = self::generate_uuid();
        $file = null;

        if (!file_exists($filepath)) {
            if (!is_dir($directory)) {
                mkdir($directory);
            }
            $file = fopen($filepath, 'x');
        } else {
            $file = fopen($filepath, 'a');
        }

        fwrite($file, $uuid);
        fclose($file);
    }

    public static function get_uuid()
    {
        $file_path = Variable::GET('PATH') . 'assets/other/uuid.txt';
        if (!file_exists($file_path)) {
            self::register_uuid();
        }
        $uuid = trim(file_get_contents($file_path));
        return $uuid;
    }

    public static function error_log($data = array(), string $label = '')
    {
        $timestamp = date('Y-m-d h:i:s A');
        if (!(strlen($label) > 0)) {
            $label = "[{$timestamp}]: {$label}";
        } else {
            $label = "[{$timestamp}]:";
        }

        ob_start();
        echo $label . ":\n";
        var_dump($data);
        $log = ob_get_clean();

        error_log($log);
    }

    public static function debug_file_path($directory = null)
    {
        if (empty($directory)) {
            $directory = Variable::GET('PATH') . 'logs';
        }

        $filename   = 'debug.log';
        $filepath   = $directory . '/' . $filename;
        return $filepath;
    }

    public static function debug_log($data = '')
    {
        ob_start();
        var_dump($data);
        $data = ob_get_clean();

        $timestamp = date('Y-m-d h:i:s A');
        $log = "[{$timestamp}]: {$data}\n";

        $directory  = Variable::GET('PATH') . 'logs';
        $filepath   = self::debug_file_path();
        $debug_file = null;

        if (!file_exists($filepath)) {
            if (!is_dir($directory)) {
                mkdir($directory);
            }
            $debug_file = fopen($filepath, 'x');
        } else {
            $debug_file = fopen($filepath, 'a');
        }

        fwrite($debug_file, $log);
        fclose($debug_file);
    }

    public static function read_debug_logs()
    {
        $data       = '';
        $filepath   = self::debug_file_path();

        if (file_exists($filepath)) {
            $data = file_get_contents($filepath);
        }

        return $data;
    }

    public static function dd($data = array())
    {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
        die();
    }


    public static function debug_dump($data = array())
    {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
    }


    public static function prefix(string $text = '')
    {
        $prefix = str_replace('-', '_', Variable::GET('PREFIX'));
        $text = str_replace(['-', ' '], '_', trim($text));
        return "{$prefix}_{$text}";
    }


    public static function with_uuid(string $text = '')
    {
        $uuid = self::get_uuid();
        $text = str_replace(['_', ' '], '-', trim($text));
        return "{$uuid}-{$text}";
    }


    public static function get_template(string $filename = null, array $args = [], bool $echo = false)
    {
        $file_path = Variable::GET('TEMPLATES') . '/' . $filename;
        if (file_exists($file_path)) {

            ob_start();
            if (!empty($args)) {
                extract($args);
            }
            include($file_path);
            $file_content = ob_get_clean();

            if ($echo) {
                echo $file_content;
                return;
            }

            return $file_content;
        }

        return false;
    }

    public static function css_file(string $filename = null)
    {
        $file_path = Variable::GET('URL') . 'assets/css/' . $filename;
        return $file_path;
    }

    public static function js_file(string $filename = null)
    {
        $file_path = Variable::GET('URL') . 'assets/js/' . $filename;
        return $file_path;
    }

    public static function asset_file(string $file = null)
    {
        $file_path = Variable::GET('URL') . 'assets/' . $file;
        return $file_path;
    }

    public static function is_json(string $string = '')
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
