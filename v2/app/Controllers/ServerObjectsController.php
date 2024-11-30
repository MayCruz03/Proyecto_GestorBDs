<?php

namespace App\Controllers;

use App\Controllers\Controller;

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

    public function createTable()
    {
        return $this->view("createTable");
    }
}
