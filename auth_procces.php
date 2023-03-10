<?php
    
    require_once("globals.php");
    require_once("db.php");
    require_once("dao/UserDAO.php");
    require_once("models/User.php");
    require_once("models/Message.php");

    $Message = new Message($BASE_URL);
    $userDao = new UserDAO($conn, $BASE_URL);
    
    //Resgatar o tipo de formulario
    $type = filter_input(INPUT_POST, "type");

    if($type == "register") {
        $name = filter_input(INPUT_POST, "name");
        $lastname = filter_input(INPUT_POST, "lastname");
        $email = filter_input(INPUT_POST, "email");
        $password = filter_input(INPUT_POST, "password");
        $confirmpassword = filter_input(INPUT_POST, "confirmpassword");

        // Verificação de dados mínimos
        if($name && $lastname && $email && $password){
            
            //Verificar se as senha se batem
            if($password === $confirmpassword) {

                //Verificar se o e-mail já está cadastrado no sistema
                if($userDao->findByEmail($email) === false) {
                    echo "Nnehum usuario foi encontrado";
                } else {
                    // Enviar uma mensagem de erro de senha não se batem
                    $Message->setMessage("Usuário já cadastrado, tente outro e-mail.", "error", "back");
                }
            } else {
                // Enviar uma mensagem de erro de senha não se batem
                $Message->setMessage("As senhas não são iguais.", "error", "back");
            }

        } else {
            // Enviar uma msg de erro de dados faltantes
            $Message->setMessage("Por favor, preencha todos os campos.", "error", "back");
        }
    } 
    else if($type == "login") {

    }
?>