<?php
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Conecta ao banco de dados 
    $conn = new mysqli("localhost", "root", "", "teste");

    if ($conn->connect_error) {
        die("Erro na conexão: " . $conn->connect_error);
    }

    // Consulta o banco de dados usando declaração preparada
    $sql = "SELECT id FROM usuario WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION["loggedin"] = true;
        $_SESSION["username"] = $username;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Usuário ou senha incorretos";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
  body {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    background-color: #f5f5f5; 
    font-family: Arial, sans-serif;
  }

 .form-container {
    background-color: #fff; 
    padding: 30px; 
    border-radius: 10px; 
    box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.2);
    width: 300px;
    margin: 0 auto;
  }

 .form-container form {
    display: flex;
    flex-direction: column;
  }

 .form-container label {
    margin-bottom: 10px; 
    color: #444; 
    font-weight: bold; 
  }

 .form-container input {
    margin-bottom: 20px; 
    padding: 15px;
    border-radius: 10px; 
    border: 1px solid #ddd;
    background-color: #fff; 
    color: #333; 
  }

 .form-container a {
    text-decoration: none;
    color: #337ab7; 
  }
</style>
</head>
<body>
    <div class="form-container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="username">Usuário:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Senha:</label>
            <input type="password" id="password" name="password" required>
            <input type="submit" value="Login">
        </form>
        <a href="registro.php">Registrar</a>
        <?php
        if (!empty($error)) {
            echo "<p>" . $error . "</p>";
        }
        ?>
    </div>
</body>
</html>

