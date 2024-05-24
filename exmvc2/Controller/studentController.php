<?php
include_once 'studentModel.php';

class StudentController {
    public function showStudentInfo() {
        $db = new PDO('mysql:host=localhost;dbname=student', 'root', 'root');
        $studentModel = new StudentModel($db);
        $studentInfo = $studentModel->getStudentInfo();
        include 'studentView.php';
    }
}

$studentController = new StudentController();
$studentController->showStudentInfo();
?>
