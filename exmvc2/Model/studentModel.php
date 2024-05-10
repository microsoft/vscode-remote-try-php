<?php
class StudentModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getStudentInfo() {
        $query = "SELECT name, age, major FROM student";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
