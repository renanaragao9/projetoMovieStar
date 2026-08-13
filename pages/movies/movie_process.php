<?php
    require_once(__DIR__ . "/../../globals.php");
    require_once(__DIR__ . "/../../database/db.php");
    require_once(__DIR__ . "/../../dao/UserDAO.php");
    require_once(__DIR__ . "/../../dao/MovieDAO.php");
    require_once(__DIR__ . "/../../models/Movie.php");
    require_once(__DIR__ . "/../../models/Message.php");
    require_once(__DIR__ . "/../../img/Storage.php");

    $message = new Message($BASE_URL);
    $userDao = new UserDAO($conn, $BASE_URL);
    $movieDao = new MovieDAO($conn, $BASE_URL);
    $storage = new Storage();

    //Resgatar o tipo de formulario
    $type = filter_input(INPUT_POST, "type");

    // Resgata dados do usuário
    $userData = $userDao->verifyToken();

    if($type === "create") {

        // Receber os dados dos inputs
        $title = filter_input(INPUT_POST, "title");
        $description = filter_input(INPUT_POST, "description");
        $trailer = filter_input(INPUT_POST, "trailer");
        $category = filter_input(INPUT_POST, "category");
        $length = filter_input(INPUT_POST, "length");

        $movie = new Movie();

        // Validação mínima de dados
        if(!empty($title) && !empty($description) && !empty($category)) {

            $movie->title = $title;
            $movie->description = $description;
            $movie->trailer = $trailer;
            $movie->category = $category;
            $movie->length = $length;
            $movie->users_id = $userData->id;

            // Upload de imagem do filme
            if(isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])) {

                $imageName = $movie->imageGenerateName();

                $savedName = $storage->save($_FILES["image"], Storage::MOVIES_DIR, $imageName);

                if($savedName !== null) {
                    $movie->image = $savedName;
                } else {
                    $message->setMessage("Tipo inválido de imagem, insira png ou jpg!", "error", "back");
                }
            }

            $movieDao->create($movie);

        } else {
            $message->setMessage("Você precisa adicionar pelo menos: título, descrição e categoria!", "error", "back");
        }

    } else if($type === "delete") {

        // Receber os dados do form
        $id = filter_input(INPUT_POST, "id");

        $movie = $movieDao->findById($id);

        if($movie) {

            // Verifica se o filme é do usuario
            if($movie->users_id === $userData->id) {
                
                $movieDao->destroy($movie->id);
                
            } else {
                
                $message->setMessage("Informações inválidas!", "error", "pages/movies/index.php");
            }

        } else {
            
            $message->setMessage("Informações inválidas!", "error", "pages/movies/index.php");
        }
    } else if($type === "update") {

        // Receber os dados dos inputs
        $title = filter_input(INPUT_POST, "title");
        $description = filter_input(INPUT_POST, "description");
        $trailer = filter_input(INPUT_POST, "trailer");
        $category = filter_input(INPUT_POST, "category");
        $length = filter_input(INPUT_POST, "length");
        $id = filter_input(INPUT_POST, "id");

        $movieData = $movieDao->findById($id);

        // Verifica se encotrou o filme
        if($movieData) {

            // Verifica se o filme é do usuario
            if($movieData->users_id === $userData->id) {
                
                // Validação mínima de dados
                if(!empty($title) && !empty($description) && !empty($category)) {
                    
                    // Edição de filme
                    $movieData->title = $title;
                    $movieData->description = $description;
                    $movieData->trailer = $trailer;
                    $movieData->category = $category;
                    $movieData->length = $length;

                    // Upload de imagem do filme
                    if(isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])) {

                        $imageName = $movieData->imageGenerateName();

                        $savedName = $storage->save($_FILES["image"], Storage::MOVIES_DIR, $imageName);

                        if($savedName !== null) {
                            $movieData->image = $savedName;
                        } else {
                            $message->setMessage("Tipo inválido de imagem, insira png ou jpg!", "error", "back");
                        }
                    }
                    
                    $movieDao->update($movieData);
                    
                } else {
                    $message->setMessage("Você precisa adicionar pelo menos: título, descrição e categoria!", "error", "back");
                }

            } else {
                
                $message->setMessage("Informações inválidas!", "error", "pages/movies/index.php");
            }
        }

    }else {
    
    $message->setMessage("Informações inválidas!", "error", "pages/movies/index.php");
    
    }


?>