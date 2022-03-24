<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/model/CategoryModel.php";

class CategoryRequest {
    private $action;
    private $model;

    public function __construct(string $action) {
        $this->action = $action;
        $this->model = new CategoryModel();
    }

    public function perform_request($args): array {
        $return = array("error" => 0);
        switch($this->action) {
            case "get":
                $items = $this->model->get_categories($args);
                if(count($items) > 0) {
                    $return["items"] = $items;
                } else {
                    $return["error"] = 6;
                }
                break;
            case "create":
                if (!$this->model->create_category($args)) {
                    $return["error"] = 7;
                }
                break;
            case "update":
                if (!$this->model->update_category($args)) {
                    $return["error"] = 8;
                }
                break;
            case "delete":
                if (!$this->model->delete_category($args)) {
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