<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/model/TypeModel.php";

class TypeRequest
{
    private $action;
    private $model;

    public function __construct(string $action)
    {
        $this->action = $action;
        $this->model = new TypeModel();
    }

    public function perform_request($args): array
    {
        $return = array("error" => 0);
        switch ($this->action) {
            case "get":
                $items = $this->model->get_types($args);
                if (count($items) > 0) {
                    $return["items"] = $items;
                } else {
                    $return["error"] = 6;
                }
                break;
            case "create":
                if (!$this->model->create_type($args)) {
                    $return["error"] = 7;
                }
                break;
            case "update":
                if (!$this->model->update_type($args)) {
                    $return["error"] = 8;
                }
                break;
            case "delete":
                if (!$this->model->delete_type($args)) {
                    $return["error"] = 9;
                }
                break;
            default:
                $return["error"] = 3;
                break;
        }
        return $return;
    }
}