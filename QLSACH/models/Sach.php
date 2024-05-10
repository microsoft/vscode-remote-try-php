<?php
class Sach {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Hàm lấy danh sách sách
    public function getList() {
        $sql = "SELECT * FROM Sach";
        $result = $this->conn->query($sql);

        $sachList = array();
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $sachList[] = $row;
            }
        }

        return $sachList;
    }
}
?>
