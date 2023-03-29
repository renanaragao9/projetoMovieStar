<?php
    
    require_once("templates/header.php");
    
    // Verifica se usuario está autenticado
    require_once("models/User.php");
    require_once("dao/UserDAO.php");
    require_once("dao/MovieDAO.php");
    
    // Verifica se usuario está autenticado
    $user = new User();
    $userDao = new UserDao($conn, $BASE_URL);
    $userData = $userDao->verifyToken(true);
    $movieDao = new MovieDAO($conn, $BASE_URL);
    $id = filter_input(INPUT_GET, "id");

    if(empty($id)) {
        $message->setMessage("O filme não foi encontrado!", "error", "index.php");
    } else {

        $movie = $movieDao->findById($id);

        // Verifica se o filme existe
        if(!$movie) {
            
            $message->setMessage("O filme não foi encontrado!", "error", "index.php");
        }
    }

    // Checar se o filme tem imagem
    if($movie->image == "") {
        $movie->image = "movie_cover.jpg";
    }

?>
    <div id="main-container" class="container-fluid">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6 offset-md-1">
                    <h1><?= $movie->title ?></h1>
                    <p class="page-description">Altere os dados do filme do formuçário abaixo:</p>
                    <form id="edit-movie-form" action="<?= $BASE_URL ?>movie_process.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="type" value="update">
                        <input type="hidden" name="id" value="<?= $movie->id ?>">
                        <div class="form-group">
                            <label for="title">Titulo:</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?= $movie->title ?>" placeholder="Digite o titulo do seu filme!">
                        </div>
                        <div class="form-group">
                            <label for="image">Imagem:</label>
                            <input type="file" class="form-control" id="image" name="image">
                        </div>
                        <div class="form-group">
                            <label for="length">Duração:</label>
                            <input type="text" class="form-control" id="length" name="length" value="<?= $movie->length ?>" placeholder="Digite a duração do filme">
                        </div>
                        <div class="form-group">
                            <label for="category">Categoria:</label>
                            <select name="category" class="form-control" id="category">
                                <option value="">Selecione</option>
                                <option value="Ação" <?= $movie->category === "Ação" ? "selected" : "" ?>>Ação</option>
                                <option value="Drama" <?= $movie->category === "Drama" ? "selected" : "" ?>>Drama</option>
                                <option value="Comédia" <?= $movie->category === "Comédia" ? "selected" : "" ?>>Comédia</option>
                                <option value="Fantasia" <?= $movie->category === "Fantasia" ? "selected" : "" ?>>Fantasia</option>
                                <option value="Ficção" <?= $movie->category === "Ficção" ? "selected" : "" ?>>Ficção</option>
                                <option value="Romance" <?= $movie->category === "Romance" ? "selected" : "" ?>>Romance</option>
                                <option value="Terror" <?= $movie->category === "Terror" ? "selected" : "" ?>>Terror</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="trailer">Trailer:</label>
                            <input type="text" class="form-control" id="trailer" name="trailer" value="<?= $movie->trailer ?>" placeholder="Link do trailer">
                        </div>
                        <div class="form-group">
                            <label for="description">Descrição:</label>
                            <textarea name="description" class="form-control" id="description" rows="5" placeholder="Descreva o filme..."><?= $movie->description ?></textarea>
                        </div>
                        <input type="submit" class="btn card-btn" value="Editar Filme">  
                    </form>
                </div>
                <div class="col-md-3">
                <div class="movie-image-container" style="background-image: url('<?= $BASE_URL ?>img/movies/<?= $movie->image ?>')"></div>
                </div>
            </div>
        </div>
    </div>

<?php
    require_once("templates/footer.php");
?>