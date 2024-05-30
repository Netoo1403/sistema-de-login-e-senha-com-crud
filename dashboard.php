<!DOCTYPE html>
<html>

<head>
    <title>Dashboard</title>
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

 .form-container input,
 .form-container select {
    margin-bottom: 20px; 
    padding: 15px;
    border-radius: 10px;
    border: 1px solid #ddd; 
    background-color: #fff; 
    color: #333; 
  }

 .form-container input[type="submit"] {
    background-color: #4CAF50; 
    color: #fff;
    cursor: pointer;
    border: none;
    padding: 15px 30px;
    border-radius: 10px; 
  }

 .form-container input[type="submit"]:hover {
    background-color: #3e8e41;
  }

 .form-container #formEdit {
    display: none; /* Esconde o formulário de edição inicialmente */
  }
</style>
</head>

<body>

    <script>
     function preencherFormularioEdicao(id, marca, modelo) 
    { document.getElementById('formEdit').style.display = 'block'; 
    document.getElementById('idEdit').value = id; 
    document.getElementById('marcaEdit').value = marca; 
    document.getElementById('modeloEdit').value = modelo; } 
    document.querySelectorAll('.editar-btn').forEach(function (button) 
    { button.addEventListener('click', function () 
    { var id = this.dataset.id;
         var marca = this.dataset.marca;
          var modelo = this.dataset.modelo;
           preencherFormularioEdicao(id, marca, modelo); }); 
    }); </script>

    <?php
    // Inicia a sessão
    session_start();

    // Verifica se o usuário está logado
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        header("location: login.php");
        exit;
    }

    // Conecta ao banco de dados
    $conn = new mysqli("localhost", "root", "", "teste");

    // Verifica a conexão
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Consulta o banco de dados para verificar o nível de acesso do usuário
    $sql = "SELECT nivel_acesso FROM perfil WHERE id = (SELECT perfil_id FROM usuario WHERE username = '" . $_SESSION["username"] . "')";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nivel_acesso = $row["nivel_acesso"];
    } else {
        echo "Erro ao obter o nível de acesso do usuário";
        exit;
    }

    $conn->close();
    ?>
    <div class="form-container">
        <?php
        $conn = new mysqli("localhost", "root", "", "teste");
        $sql = "SELECT nivel_acesso FROM perfil WHERE id = (SELECT perfil_id FROM usuario WHERE username = '" . $_SESSION["username"] . "')";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $nivel_acesso = $row["nivel_acesso"];
        } else {
            echo "Erro ao obter o nível de acesso do usuário";
            exit;
        }

       
        // Atualização de carros 
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["idEdit"]))
         { $id = $_POST["idEdit"]; $marca = isset($_POST["marcaEdit"]) && !empty($_POST["marcaEdit"]) ? $_POST["marcaEdit"] : null; $modelo = isset($_POST["modeloEdit"]) && !empty($_POST["modeloEdit"]) ? $_POST["modeloEdit"] : null;

// Busca o carro atual no banco de dados 
$sql = "SELECT marca, modelo FROM carros WHERE id=$id"; $result = $conn->query($sql); if ($result->num_rows > 0) { $row = $result->fetch_assoc(); // Se a marca ou o modelo não foram enviados no form, mantém o valor que estava 
    $marca = $marca !== null ? $marca : $row["marca"]; $modelo = $modelo !== null ? $modelo : $row["modelo"]; }

$sql = "UPDATE carros SET marca='$marca', modelo='$modelo' WHERE id=$id;"; if ($conn->query($sql) === TRUE) {

} else { echo "Erro ao atualizar o carro: " . $conn->error; } }


        // Se o usuário for um administrador, ele pode fazer o CRUD de tarefas
        if ($nivel_acesso == "Administrador") {

            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["marca"]) && isset($_POST["modelo"])) {
                $marca = $_POST["marca"];
                $modelo = $_POST["modelo"];
                $sql = "INSERT INTO carros (marca, modelo) VALUES ('$marca', '$modelo')";
                if ($conn->query($sql) === TRUE) {
        
                } else {
                    echo "Erro: ". $sql. "<br>". $conn->error;
                }
                header("Location: ". $_SERVER["PHP_SELF"]);
                exit;
            }
        
            // Deleção de carros
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
                $id = $_POST["id"];
                $sql = "DELETE FROM carros WHERE id=$id";
                if ($conn->query($sql) === TRUE) {
        
                } else {
                    echo "Erro ao deletar o carro: ". $conn->error;
                }
            }
        
            echo '
            <h1>Formulário de Carros</h1>
            <form action="" method="post">
                <h2>Criar Carro</h2>
                <label for="marca">Marca:</label><br>
                <input type="text" id="marca" name="marca"><br>
                <label for="modelo">Modelo:</label><br>
                <input type="text" id="modelo" name="modelo"><br>
                <input type="submit" value="Criar">
            </form>
        
            <form id="formEdit" action="" method="post">
                <h2>Atualizar Carro</h2>
                <label for="idEdit">ID:</label><br>
                <input type="text" id="idEdit" name="idEdit"><br>
                <label for="marcaEdit">Marca:</label><br>
                <input type="text" id="marcaEdit" name="marcaEdit" value=""><br>
                <label for="modeloEdit">Modelo:</label><br>
                <input type="text" id="modeloEdit" name="modeloEdit"><br>
                <input type="submit" value="Atualizar">
            </form>
        
            ';
        
            // Leitura de carros
            $sql = "SELECT id, marca, modelo FROM carros";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo "<table>"; // Iniciar tabela
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>Id: ". $row["id"]. "</td>";
                    echo "<td>Marca: ". $row["marca"]. "</td>";
                    echo "<td>Modelo: ". $row["modelo"]. "</td>";
                    // Adiciona um botão de deletar com o ID do carro
                    echo "<td><form action='' method='post'>";
                    echo "<input type='hidden' name='id' value='". $row["id"]. "'>";
                    echo "<input type='submit' value='Deletar'>";
                    echo "</form></td>";
                    echo "<td><button onclick='preencherFormularioEdicao(\"". $row["id"]. "\", \"". $row["marca"]. "\", \"". $row["modelo"]. "\")'>Editar</button></td>";
                }
                echo "</table>"; // Fechar tabela
            } else {
                echo "Nenhum carro encontrado <br> ";
            }
            echo "<br> <a href='logout.php'>Logout</a>";
        

        } else if ($nivel_acesso == "Cliente") {
            echo "Bem vindo, cliente!<br><br>"; 

            
            // Se o usuário for um cliente, ele pode apenas visualizar os carros
            $sql = "SELECT marca, modelo FROM carros";
            $result = $conn->query($sql);
        
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "Marca: " . $row["marca"] . " - Modelo: " . $row["modelo"] . "<br>";
                }
            } else {
                echo "Nenhum carro encontrado";
            }
            echo "<br> <a href='logout.php'>Logout</a>";
        } else {
            echo "<p>Você não tem permissão para acessar esta página.</p> <br> <a href='logout.php'>Logout</a>";
        }
        
        $conn->close(); 
    ?>
    
    </div>
    
</body>

</html>
