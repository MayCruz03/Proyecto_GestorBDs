<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Lib\ApiResponse;
use Lib\DBObjectsMap;

class ServerObjectsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getServers()
    {
        $json = file_get_contents(DATA_DIR . "/servers.json");
        $data = json_decode($json, true);

        return $data;
    }

    public function getServeObjectsTypes()
    {
        $request = $this->Api->get("/serverObjects/types");

        if (!$request->success) {
            return $request;
        }

        $data = [];
        foreach ($request->data as $item) {
            $data[] = [
                "objectId" => $item["OBJ_TYPE_ID"],
                "objectName" => $item["OBJ_TYPE_NAME_ES"],
                "objectParent" => $item["OBJ_PARENT_ID"],
                "objectIcon" => $item["OBJ_TYPE_ICON"]
            ];
        }
        $request->data = $data;

        return $request;
    }

    public function getServeObjects()
    {
        $request = $this->Api->get("/serverObjects/list");

        if (!$request->success) {
            return $request;
        }

        $data = [];
        foreach ($request->data as $item) {
            $data[] = [
                "objectTypeId" => $item["OBJ_TYPE_ID"],
                "objectTypeName" => $item["OBJ_TYPE_NAME"],
                "objectId" => $item["OBJ_ID"],
                "objectName" => $item["OBJ_NAME"],
                "schema" => $item["OBJ_SCHEMA_NAME"],
                "parentOf" => $item["PARENT_ID"]
            ];
        }
        $request->data = $data;

        return $request;
    }

    public function getDataTypes()
    {
        $request = $this->Api->get("/serverObjects/dataTypes");

        if (!$request->success) {
            return $request;
        }

        $data = [];
        foreach ($request->data as $item) {
            $data[] = [
                "dataTypeId" => $item["DTYPE_ID"],
                "dataTypeName" => $item["DTYPE_NAME"],
                "dataTypeNumberParams" => $item["DPARAMS_NUMBER"]
            ];
        }
        $request->data = $data;

        return $request;
    }

    public function createTable()
    {
        $dataTypes = $this->getDataTypes();
        return $this->view("createTable", [
            "title" => "Crear Tabla",
            "dataTypes" => $dataTypes->data ?? []
        ]);
    }

    public function createTablePostBack()
    {
        $response = new ApiResponse();

        $request = $this->checkRequestParams(["tableName", "columns"], true);
        $data = $request->data;

        if (empty($_SESSION["DB_USER_SCHEMA"] ?? null)) {
            return $response->Error(500, "No se indico el esquema principal...");
        }

        $url = "/serverObjects/getObjId/" . DBObjectsMap::OBJ_SCHEMA . "/" . $_SESSION["DB_USER_SCHEMA"];
        $data["schema"] = $this->Api->get($url)->data;

        $url = "/serverObjects/getObjId/" . DBObjectsMap::OBJ_DATABASE . "/" . $_SESSION["DB_NAME"];
        $databaseId = $this->Api->get($url)->data;

        $response = $this->Api->post("/table/{$databaseId}/create", $data);
        return $response;

    }
}
