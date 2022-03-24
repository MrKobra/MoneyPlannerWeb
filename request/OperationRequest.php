<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/model/OperationModel.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/model/CategoryModel.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/model/TypeModel.php";

class OperationRequest {
    private $action;
    private $model;

    public function __construct(string $action) {
        $this->action = $action;
        $this->model = new OperationModel();
    }

    public function perform_request($args): array {
        $return = array("error" => 0);
        switch($this->action) {
            case "get":
                $items = $this->model->get_operations($args);
                if(count($items) > 0) {
                    if(array_key_exists("include_category", $args) && $args["include_category"] == 1) {
                        $category_model = new CategoryModel();
                        foreach($items as & $item) {
                            $category = $category_model->get_categories(["id" => $item["category_id"]]);
                            $item["category"] = (count($category) > 0) ? $category[0] : $category;
                        }
                    }
                    if(array_key_exists("include_type", $args) && $args["include_type"] == 1) {
                        $type_model = new TypeModel();
                        foreach($items as & $item) {
                            $type = $type_model->get_types(["id" => $item["type_id"]]);
                            $item["type"] = (count($type) > 0) ? $type[0] : $type;
                        }
                    }
                    $return["items"] = $items;
                } else {
                    $return["error"] = 6;
                }
                break;
            case "create":
                if(!$this->model->create_operation($args)) {
                    $return["error"] = 7;
                }
                break;
            case "update":
                if(!$this->model->update_operation($args)) {
                    $return["error"] = 8;
                }
                break;
            case "delete":
                if(!$this->model->delete_operation($args)) {
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