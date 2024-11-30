<?php

namespace Lib;

use Lib\ApiResponse;

class HttpClient
{
    private $baseUrl; // URL base de la API
    private $headers = []; // Headers personalizados

    /**
     * Constructor para inicializar la URL base
     * @param string $baseUrl
     */
    public function __construct(string $baseUrl = '')
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    /**
     * Setea headers personalizados
     * @param array $headers Array asociativo de headers (clave => valor)
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * Realiza una solicitud GET
     * @param string $endpoint Ruta del endpoint (relativa a la base URL)
     * @param array $params Parámetros de consulta (query string)
     * @return ApiResponse Respuesta decodificada o false en caso de error
     */
    public function get(string $endpoint, $rawData = null, bool $asJson = true)
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        $options = $this->prepareOptions('GET');

        // Si hay datos raw, envíalos en el cuerpo de la solicitud
        if ($rawData) {
            if ($asJson) {
                $options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
                $options[CURLOPT_POSTFIELDS] = is_string($rawData) ? $rawData : json_encode($rawData);
            } else {
                $options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/x-www-form-urlencoded';
                $options[CURLOPT_POSTFIELDS] = http_build_query($rawData);
            }
        }

        return $this->executeRequest($url, $options);
    }

    /**
     * Realiza una solicitud POST
     * @param string $endpoint Ruta del endpoint
     * @param array|string $data Datos a enviar (array para form-data, string para JSON)
     * @param bool $asJson Indica si los datos deben enviarse como JSON
     * @return ApiResponse Respuesta decodificada o false en caso de error
     */
    public function post(string $endpoint, $data = [], bool $asJson = false)
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        $options = $this->prepareOptions('POST');

        if ($asJson) {
            $options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
            $options[CURLOPT_POSTFIELDS] = is_string($data) ? $data : json_encode($data);
        } else {
            $options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/x-www-form-urlencoded';
            $options[CURLOPT_POSTFIELDS] = http_build_query($data);
        }

        return $this->executeRequest($url, $options);
    }

    /**
     * Construye la URL completa con parámetros de consulta
     * @param string $endpoint Ruta del endpoint
     * @param array $params Parámetros de consulta
     * @return string URL completa
     */
    private function buildUrl(string $endpoint, array $params = [])
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        return $url;
    }

    /**
     * Prepara las opciones básicas de cURL
     * @param string $method Método HTTP (GET, POST, etc.)
     * @return array Opciones de cURL
     */
    private function prepareOptions(string $method)
    {
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $this->formatHeaders(),
            CURLOPT_TIMEOUT => 30,
            CURLOPT_COOKIE => API_COOKIE_NAME . "=" . @$_COOKIE[WEB_COOKIE_NAME]
        ];
        return $options;
    }

    /**
     * Ejecuta la solicitud HTTP usando cURL
     * @param string $url URL completa
     * @param array $options Opciones de cURL
     * @return ApiResponse Respuesta decodificada o false en caso de error
     */
    private function executeRequest(string $url, array $options)
    {
        $httpResponse = new ApiResponse();

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($response === false) {
            return $httpResponse->Error($httpCode, $error);
        }

        $decoded = json_decode($response, true);
        if (empty($decoded)) {
            $httpResponse->Ok($response);
        }
        return empty($decoded) ? $httpResponse->Ok($response) : ApiResponse::map($decoded);
    }

    /**
     * Convierte headers personalizados en formato de array
     * @return array Headers formateados
     */
    private function formatHeaders()
    {
        $formattedHeaders = [];
        foreach ($this->headers as $key => $value) {
            $formattedHeaders[] = "{$key}: {$value}";
        }
        return $formattedHeaders;
    }
}
