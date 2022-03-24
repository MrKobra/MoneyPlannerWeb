<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/inc/functions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/inc/DataBase.php";

class OperationModel {
    private static $table_name = "operation";
    private static $create_table_fields = array(
        "required" => array("user_id", "type_id", "category_id", "bank_card_id", "amount"),
        "not_required" => array("id", "date", "date_created")
    );
    private static $get_args = array(
        "date_interval" => array("date_from", "date_before"),
        "page" => array("limit", "offset"),
        "sort" => array("orderby", "order"),
        "fields" => array("id", "user_id", "type_id", "category_id", "bank_card_id", "amount", "date", "date_created")
    );
    private $db;

    public function __construct() {
        $this->db = new DataBase();
    }

    public function get_operations($args): array {
        $query = $this->generate_get_query($args);
        return (strlen($query) > 0) ? $this->db->get_rows($query) : array();
    }

    public function create_operation($args): int {
        $query = $this->generate_create_query($args);
        return (strlen($query) > 0) ? $this->db->create_row($query) : 0;
    }

    public function delete_operation($args): bool {
        $query = $this->generate_delete_query($args);
        return strlen($query) > 0 && $this->db->delete_row($query);
    }

    public function update_operation($args): bool {
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
                array_push($update_args, "$key = $value");
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
                $where_str = (strpos($value, ",")) ? "$key IN (" . DataBase::get_multiple_arg($value) . ")" : "$key = '$value'";
                array_push($where_args, $where_str);
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
        $date_interval_args = get_args($args, self::$get_args["date_interval"]);
        $page_args = get_args($args, self::$get_args["page"]);
        $sort_args = get_args($args, self::$get_args["sort"], array("orderby" => "date", "order" => "DESC"));
        $field_args = get_args($args, self::$get_args["fields"]);

        $query = "SELECT * FROM " . self::$table_name;

        if(count($field_args) > 0) {
            $query .= (strpos($query, "WHERE")) ? " AND " : " WHERE ";

            $where_args = array();
            foreach($field_args as $key => $value) {
                $where_str = (strpos($value, ",")) ? "$key IN (" . DataBase::get_multiple_arg($value) . ")" : "$key = '$value'";
                array_push($where_args, $where_str);
            }
            $query .= implode(" AND ", $where_args);
        }

        if(count($date_interval_args) > 0) {
            if(array_key_exists("date_from", $date_interval_args) && array_key_exists("date_before", $date_interval_args)) {
                $query .= (strpos($query, "WHERE")) ? " AND" : " WHERE";
                $query .= " date BETWEEN '{$date_interval_args["date_from"]}' AND '{$date_interval_args["date_before"]}'";
            }
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