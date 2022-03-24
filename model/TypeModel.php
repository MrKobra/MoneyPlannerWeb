<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/inc/functions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/inc/DataBase.php";

class TypeModel {
    private static $table_name = "type";
    private static $create_table_fields = array(
        "required" => array("title", "slug"),
        "not_required" => array("id", "icon")
    );
    private static $get_args = array(
        "page" => array("limit", "offset"),
        "sort" => array("orderby", "order"),
        "fields" => array("id", "title", "slug", "icon")
    );
    private $db;

    public function __construct() {
        $this->db = new DataBase();
    }

    public function get_types($args): array {
        $query = $this->generate_get_query($args);
        return (strlen($query) > 0) ? $this->db->get_rows($query) : array();
    }

    public function create_type($args): int {
        $query = $this->generate_create_query($args);
        return (strlen($query) > 0) ? $this->db->create_row($query) : 0;
    }

    public function delete_type($args): bool {
        $query = $this->generate_delete_query($args);
        return strlen($query) > 0 && $this->db->delete_row($query);
    }

    public function update_type($args): bool {
        $query = $this->generate_update_query($args);
        return strlen($query) > 0 && $this->db->update_row($query);
    }

    private function generate_update_query($args): string {
        $query = "";
        $field_args = get_args($args, self::$get_args["fields"]);
        if(count($field_args) > 1 && array_key_exists("id", $field_args)) {
            $query = "UPDATE " . self::$table_name . " SET ";

            $id = $field_args["id"];
            unset($field_args["id"]);

            $update_args = array();
            foreach($field_args as $key => $value) {
                array_push($update_args, "$key = '$value'");
            }
            $query .= implode(",", $update_args);
            $query .= " WHERE id = $id";
        }
        return $query;
    }

    private function generate_delete_query($args): string {
        $query = "";

        $field_args = get_args($args, self::$get_args["fields"]);
        if(count($field_args) > 0) {
            $query = "DELETE FROM " . self::$table_name . " WHERE ";
            $where_args = array();
            foreach($field_args as $key => $value) {
                array_push($where_args, "$key = '$value'");
            }
            $query .= implode(" AND ", $where_args);
        }

        return $query;
    }

    private function generate_create_query($args): string {
        $query = "";

        if(compare_args($args, self::$create_table_fields["required"])) {
            $query = "INSERT INTO " . self::$table_name;

            $not_required_args = get_args($args, self::$create_table_fields["not_required"]);
            $required_args = get_args($args, self::$create_table_fields["required"]);
            $fields = implode(",", array_keys($required_args)) . "," . implode(",", array_keys($not_required_args));

            $fields_value = implode(",", $required_args) . "," . implode(",", $not_required_args);

            $query .= " ($fields) VALUES ($fields_value)";
        }
        return $query;
    }

    private function generate_get_query($args): string {
        $page_args = get_args($args, self::$get_args["page"]);
        $sort_args = get_args($args, self::$get_args["sort"], array("orderby" => "id", "order" => "DESC"));
        $field_args = get_args($args, self::$get_args["fields"]);

        $query = "SELECT * FROM " . self::$table_name;

        if(count($field_args) > 0) {
            $query .= (strpos($query, "WHERE")) ? " AND " : " WHERE ";

            $where_args = array();
            foreach($field_args as $key => $value) {
                array_push($where_args, "$key = '$value'");
            }
            $query .= implode(" AND ", $where_args);
        }

        if(count($sort_args) > 0) {
            if(array_key_exists("orderby", $sort_args) && array_key_exists("order", $sort_args)) {
                $query .= " ORDER BY {$sort_args["orderby"]} {$sort_args["order"]}";
            }
        }

        if(count($page_args) > 0) {
            foreach($page_args as $key => $value) {
                $query .= " $key $value";
            }
        }

        return $query;
    }
}