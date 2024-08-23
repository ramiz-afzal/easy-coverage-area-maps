<?php

namespace EASY_COVERAGE_AREA_MAPS\Core;

use EASY_COVERAGE_AREA_MAPS\Base\Constant;
use EASY_COVERAGE_AREA_MAPS\Base\Functions;

final class RadarApi
{
    /**
     * get application's api mode set in the admin options page
     * @return string $api_mode
     */
    public static function get_api_mode()
    {
        $api_mode = carbon_get_theme_option(Functions::prefix('api_mode'));
        return $api_mode === 'live' ? 'live' : 'test';
    }


    /**
     * get base api URL
     * @param string $path
     * @return string|bool $api_url
     */
    public static function get_api_url(string $path = '')
    {
        $api_url = Constant::URL_API_LIVE;

        if (!empty($path)) {
            $api_url = substr($path, 0, 1) === '/' ? "{$api_url}{$path}" : "{$api_url}/{$path}";
        }

        return $api_url;
    }


    /**
     * get access token if exists
     * @return bool|array $access_token
     */
    public static function get_api_keys()
    {
        try {

            $api_mode   = self::get_api_mode();
            $public_key = null;
            $secret_key = null;

            if ($api_mode === 'live') {
                $public_key = carbon_get_theme_option(Functions::prefix('live_publishable'));;
                $secret_key = carbon_get_theme_option(Functions::prefix('live_secret'));;
            } else {
                $public_key = carbon_get_theme_option(Functions::prefix('test_publishable'));;
                $secret_key = carbon_get_theme_option(Functions::prefix('test_secret'));;
            }

            if (empty($public_key) || empty($secret_key)) {
                return false;
            }

            return [
                'public_key' => $public_key,
                'secret_key' => $secret_key,
            ];
        } catch (\Throwable $error) {
            Functions::debug_log(__METHOD__ . " Error occurred: " . $error->getMessage());
            return false;
        }
    }


    /**
     * Main API call wrapper
     * @param string $path
     * @param string $method
     * @param array $request_body
     * @return bool|array $response
     */
    public static function make_api_call(string $path = '', string $method = 'GET', array $request_body = [], array $headers = [])
    {
        try {
            $response   = false;
            $api_url    = self::get_api_url($path);
            $method     = strtoupper($method);

            if (empty($path) || empty($method) || empty($api_url)) {
                return false;
            }

            $request_headers = array(
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            );

            if (!empty($headers)) {
                $request_headers = array_merge($request_headers, $headers);
            }

            $request_args = array(
                'method'    => $method,
                'headers'   => $request_headers,
            );

            if (!empty($request_body)) {
                if ($method == 'GET') {
                    $api_url = add_query_arg($request_body, $api_url);
                } else if ($method == 'POST' || $method == 'PUT') {
                    $request_args['body'] = json_encode($request_body);
                }
            }

            $wp_response = wp_remote_request($api_url, $request_args);
            if (is_wp_error($wp_response) || !isset($wp_response['response']) || !isset($wp_response['response']['code'])) {
                Functions::debug_log(is_wp_error($wp_response) ? $wp_response->get_error_message() : 'Unknown error occurred.');
                return $response;
            }

            $response_body  = json_decode(wp_remote_retrieve_body($wp_response), true);
            $response       = empty($response_body) ? [] : $response_body;

            return $response;
        } catch (\Throwable $error) {
            Functions::debug_log(__METHOD__ . " Error occurred: " . $error->getMessage());
            return false;
        }
    }


    /**
     * get gro coordinates for a address
     * @param string $address
     * @return bool|array $coords
     */
    public static function get_address_coords(string $address = null)
    {
        try {
            if (empty($address)) {
                return false;
            }

            $api_keys = self::get_api_keys();
            if (empty($api_keys) || !isset($api_keys['public_key']) || !isset($api_keys['secret_key'])) {
                return false;
            }

            $response = self::make_api_call('/geocode/forward', 'GET', ['query' => $address, 'layers' => 'address'], ['Authorization' => $api_keys['public_key']]);
            if (empty($response) || !isset($response['addresses']) || !isset($response['addresses']['latitude']) || !isset($response['addresses']['longitude'])) {
                return false;
            }

            $coords = [
                'latitude'  => $response['addresses']['latitude'],
                'longitude' => $response['addresses']['longitude'],
            ];

            return $coords;
        } catch (\Throwable $error) {
            Functions::debug_log(__METHOD__ . " Error occurred: " . $error->getMessage());
            return false;
        }
    }
}
