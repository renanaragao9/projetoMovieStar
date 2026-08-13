<?php
    
    require_once(__DIR__ . "/../../globals.php");
    require_once(__DIR__ . "/../../database/db.php");
    require_once(__DIR__ . "/../../dao/UserDAO.php");
    require_once(__DIR__ . "/../../models/User.php");
    require_once(__DIR__ . "/../../models/Message.php");
    require_once(__DIR__ . "/../../img/Storage.php");

    $Message = new Message($BASE_URL);
    $userDao = new UserDAO($conn, $BASE_URL);
    $storage = new Storage();

    //Resgatar o tipo de formulario
    $type = filter_input(INPUT_POST, "type");

    // Atualizar usuário
    if($type === "update") {
        
        // Resgata dados do usuário
        $userData = $userDao->verifyToken();
         
        // Receber dados do post
        $name = filter_input(INPUT_POST, "name");
        $lastname = filter_input(INPUT_POST, "lastname");
        $email = filter_input(INPUT_POST, "email");
        $bio = filter_input(INPUT_POST, "bio");

        // Criar um novo objeto de usuário
        $user = new User();
        
        // Preencher os dados do usuário
        $userData->name = $name;
        $userData->lastname = $lastname;
        $userData->email = $email;
        $userData->bio = $bio;

        // Upload da imagem
        if(isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])) {

            $imageName = $user->imageGenerateName();

            $savedName = $storage->save($_FILES["image"], Storage::USERS_DIR, $imageName);

            if($savedName !== null) {
                $userData->image = $savedName;
            } else {
                $Message->setMessage("Tipo inválido de imagem, insira png ou jpg!", "error", "back");
            }
        }

        $userDao->update($userData);

        // Atualizar senha do usuário
    } else if($type === "changepassword") {

        // Receber dados do post
        $password = filter_input(INPUT_POST, "password");
        $confirmpassword = filter_input(INPUT_POST, "confirmpassword");
       
        // Resgata dados do usuário
        $userData = $userDao->verifyToken();

        $id = $userData->id;
        
        if($password == $confirmpassword) {

            // Criar um novo objeto de usuário
            $user = new User();

            $finalPassword = $user->generatePassword($password);

            $user->password = $finalPassword;
            $user->id = $id;

            $userDao->changePassword($user);

        } else {
            $Message->setMessage("As senha não são iguais!", "error", "back");
        }

    } else {
        $Message->setMessage("Informações inválidas!", "error", "pages/movies/index.php");
    }

?>