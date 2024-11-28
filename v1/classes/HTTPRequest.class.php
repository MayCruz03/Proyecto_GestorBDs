
<?php

include_once "HTTPResponse.class.php";

class HTTPRequest
{

    /**
     * Devuelve las entradas enviadas por php://input en un array asociativo
     * @return array[]
     */
    public function get_body_request_params(): array
    {
        $body_data = file_get_contents("php://input");
        // Decodificar los datos en el formato que estés utilizando (por ejemplo, JSON)
        $body_data_array = json_decode($body_data, true) == null ? [] : json_decode($body_data, true);

        return $body_data_array;
    }

    /**
     * Deuvelve en un array[] de objetos FileDetail los archivos encontrado en $_FILE con el nombre pasado en $field_name
     * @param string $field_name
     * @return FileDetail[]
     */
    public function get_attachments_files($field_name): array
    {

        $array_files = [];

        if (isset($_FILES[$field_name])) {

            // en caso de ser varios adjuntos exprime como un \curl_file_create cada archivo
            if (is_array($_FILES[$field_name]['tmp_name'])) {
                for ($i = 0; $i < count($_FILES[$field_name]['tmp_name']); $i++) {

                    $nombre = str_replace(" ", "-", $_FILES[$field_name]['name'][$i]); // Nombre original del archivo
                    $tipo = $_FILES[$field_name]['type'][$i];   // Tipo MIME del archivo
                    $temp = $_FILES[$field_name]['tmp_name'][$i]; // Nombre temporal del archivo en el servidor

                    array_push($array_files, new FileDetail($temp, $nombre, $tipo));
                }
            } else {
                // si no en caso de ser uno solo apenda a array
                $nombre = str_replace(" ", "-", $_FILES[$field_name]['name']); // Nombre original del archivo
                $tipo = $_FILES[$field_name]['type'];   // Tipo MIME del archivo
                $temp = $_FILES[$field_name]['tmp_name']; // Nombre temporal del archivo en el servidor

                array_push($array_files, new FileDetail($temp, $nombre, $tipo));
            }
        }

        return $array_files;
    }

    /**
     * Crea un peticion cURL de tipo POST y retorna un HTTPResponse con los estados de la misma
     * @param string $url
     * @param mixed $parameters
     * @param string[] $headers
     * @param bool $encodeParameters
     * @param string $_USERPWD
     * @return HTTPResponse
     */
    public function POST_request(
        $url,
        $parameters = [],
        $headers = [],
        $encodeParameters = true,
        $_USERPWD = null
    ): HTTPResponse {

        $http_request = new  HTTPResponse();
        try {

            if ($encodeParameters) {
                $parameters = json_encode($parameters); // Convierte los datos a formato JSON
            }

            // Inicializa una nueva sesión cURL
            $curl = curl_init($url);

            // Configura las cabeceras para enviar datos JSON
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            if ($_USERPWD != null) {
                curl_setopt($curl, CURLOPT_USERPWD, $_USERPWD);
            }
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);


            // Ejecuta la solicitud cURL y almacena la respuesta en una variable
            $response = curl_exec($curl);
            $response_info = curl_getinfo($curl);
            $response_data = json_decode($response, true);

            // Manejo de errores
            if ($response === false) {
                $http_request->Error(502, "HTTP Error", curl_error($curl));
            } else if (in_array($response_info['http_code'], [200, 201])) {
                $http_request->Ok($response_data);
            } else {
                $http_request->Error(501, "Server error [{$response_info['http_code']}]", $response_data);
            }

            // Cierra la sesión cURL
            curl_close($curl);
        } catch (\Throwable $th) {
            $http_request->Error(500, $th->getMessage());
        }

        return $http_request;
    }

    /**
     * Crea un peticion cURL de tipo GET y retorna un HTTPResponse con los estados de la misma
     * @param string $url
     * @param mixed $parameters
     * @param string[] $headers
     * @param bool $encodeParameters
     * @param string $_USERPWD
     * @return HTTPResponse
     */
    public function GET_request(
        $url,
        $parameters = [],
        $headers = [],
        $_USERPWD = null
    ): HTTPResponse {

        $http_request = new  HTTPResponse();
        try {

            if (!empty($parameters)) {
                $url .= '?' . http_build_query($parameters);
            }

            // Inicializa una nueva sesión cURL
            $curl = curl_init($url);

            // Configura opciones de cURL
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Devuelve la respuesta en lugar de imprimirla

            if ($_USERPWD != null) {
                curl_setopt($curl, CURLOPT_USERPWD, $_USERPWD);
            }

            if (!empty($headers)) {
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            }
            // Puedes configurar otras opciones como cabeceras, autenticación, etc., según tus necesidades

            // Ejecuta la solicitud cURL y almacena la respuesta en una variable
            $response = curl_exec($curl);
            $response_info = curl_getinfo($curl);
            $response_data = json_decode($response, true);


            // Manejo de errores
            if ($response === false) {
                $http_request->Error(502, "HTTP Error", curl_error($curl));
            } else if (in_array($response_info['http_code'], [200, 201])) {
                $http_request->Ok($response_data);
            } else {
                $http_request->Error(501, "Server error [{$response_info['http_code']}]", $response_data);
            }

            // Cierra la sesión cURL
            curl_close($curl);
        } catch (\Throwable $th) {
            $http_request->Error(500, $th->getMessage());
        }

        return $http_request;
    }
}

class FileDetail
{
    public $path;
    public $name;
    public $contentType;
    public $source;

    function __construct($path, $name, $contentType, $source = "local")
    {
        $this->path = $path;
        $this->name = $name;
        $this->contentType = $contentType;
        $this->source = $source;
    }
}

class FormData
{

    private $_boundary;
    private $_eol;

    private $_formDataString;
    private $_closed;

    function __construct()
    {
        $this->_boundary =  md5(time());
        $this->_eol =  "\r\n";
        $this->_formDataString =  "";
        $this->_closed =  false;
    }

    /**
     * Agrega un nuevo elemento string al formData. No reversible
     */
    function appendParameter(string $paramKey, string $paramValue): void
    {
        $this->_formDataString .= '--' . $this->_boundary . $this->_eol;
        $this->_formDataString .= 'Content-Disposition: form-data; name="' . $paramKey . '"' . $this->_eol . $this->_eol;
        $this->_formDataString .= $paramValue . $this->_eol;
    }

    /**
     * Agrega un nuevo elemento de tipo Archivo al formData junto con su contenido. No reversible
     * @param string $paramKey Nombre de la clave visible para el servidor al que se envia
     * @param FileDetail $paramFile Debe contener en su propiedad FileDetail::path un directorio existente
     */
    function appendFileParameter(string $paramKey, FileDetail $paramFile): void
    {
        $this->_formDataString .= '--' . $this->_boundary . $this->_eol;
        $this->_formDataString .= 'Content-Disposition: form-data; name="' . $paramKey . '"; filename="' . $paramFile->name . '"' . $this->_eol;
        $this->_formDataString .= "Content-Type: $paramFile->contentType" . $this->_eol . $this->_eol;
        $this->_formDataString .= file_get_contents($paramFile->path) . $this->_eol;
    }

    /**
     * Cierra y retorna un string con el FormData creado en el formato aceptado para un HTTP Request
     * @return string
     */
    function getFormData(): string
    {
        if (!$this->_closed) {
            $this->_formDataString .= "--" . $this->_boundary . "--" . $this->_eol . $this->_eol;
            $this->_closed =  true;
        }

        return $this->_formDataString;
    }

    /**
     * Retorna el Boundary usado para la formacion del FormData. Necesario de incluir en Headers de la cURL Request
     * @return string
     */
    function getBoundary(): string
    {
        return $this->_boundary;
    }
}
