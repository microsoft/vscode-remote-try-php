<?php
// Bắt đầu phiên làm việc
session_start();

// Kiểm tra yêu cầu và điều hướng đến controller phù hợp
if (isset($_GET['controller'])) {
    $controllerName = ucfirst($_GET['controller']) . 'Controller';
} else {
    // Mặc định chuyển hướng đến controller Sach nếu không có yêu cầu controller cụ thể
    $controllerName = 'SachController';
}

$controllerFile = 'controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controller = new $controllerName();
    
    // Gọi phương thức index() của controller
    if (isset($_GET['action']) && method_exists($controller, $_GET['action'])) {
        $action = $_GET['action'];
        $controller->$action();
    } else {
        $controller->index();
    }
} else {
    // Trả về trang 404 nếu không tìm thấy controller
    header("HTTP/1.0 404 Not Found");
    echo "File không tồn tại.";
}
?>
