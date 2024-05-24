<!DOCTYPE html>
<html>
<head>
    <title>Calculation using MVC Model</title>
</head>
<body>
    <form id="form" action="controller/CalcController.php" method="post">
        <h2>Calculation using MVC Model</h2>
        <table>
            <tr>
                <td>NUMBER 1</td>
                <td><input type="text" name="number1"></td>
            </tr>
            <tr>
                <td>NUMBER 2</td>
                <td><input type="text" name="number2"></td>
            </tr>
            <tr>
                <td>METHOD</td>
                <td>
                    <select name="method_v">
                        <option value="add">Add</option>
                        <option value="sub">Sub</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" value="Send" name="btnSubmit"></td>
            </tr>
        </table>
    </form>
</body>
</html>
