<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/inc/config.php";

class DataBase {
    private $connect;

    public function get_rows($query): array {
        $return = array();
        $this->open_connect();
        if($this->connect) {
            $rows = mysqli_query($this->connect, $query);
            if($rows && mysqli_num_rows($rows) > 0) {
                while($item = mysqli_fetch_assoc($rows)) {
                    array_push($return, $item);
                }
            }
        }
        $this->close_connect();
        return $return;
    }

    public function create_row($query): int {
        $insert  = 0;

        $this->open_connect();
        if($this->connect) {
            if(mysqli_query($this->connect, $query)) {
                $return = mysqli_insert_id($this->connect);
            }
        }
        $this->close_connect();

        return $insert;
    }

    public function delete_row($query): bool {
        $this->open_connect();
        $delete = mysqli_query($this->connect, $query);
        $this->close_connect();
        return $delete;
    }

    public function update_row($query): bool {
        $this->open_connect();
        $update = mysqli_query($this->connect, $query);
        $this->close_connect();
        return $update;
    }

    public static function get_multiple_arg(string $arg): string {
        $arg_array = explode(",", $arg);
        foreach($arg_array as & $value) {
            $value = trim($value);
        }
        return "'" . implode("','", $arg_array) . "'";
    }

    private function open_connect(): void {
        $this->connect = mysqli_connect(HOSTNAME, USERNAME, PASSWORD, DB_NAME);
    }

    private function close_connect(): void {
        if($this->connect) {
            mysqli_close($this->connect);
        }
    }
}


