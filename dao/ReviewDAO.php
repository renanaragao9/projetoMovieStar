<?php

    require_once("models/Review.php");
    require_once("models/Message.php");
    require_once("dao/UserDAO.php");

    //Review DAO

    class ReviewDao implements ReviewDAOInterface {
        private $conn;
        private $url;
        private $message;

        public function __construct(PDO $conn, $url) {
            $this->conn = $conn;
            $this->url = $url;
            $this->message = new Message($url);
        }
            
            public function buildReview($data) {

                $reviewObject = new Review();

                $reviewObject->id = $data["id"];
                $reviewObject->rating = $data["rating"];
                $reviewObject->review = $data["review"];
                $reviewObject->users_id = $data["users_id"];
                $reviewObject->movies_id = $data["movies_id"];

                return $reviewObject;

            }
            public function create(Review $review) {
                print_r($review);
                $stmt = $this->conn->prepare("INSERT INTO reviews (
                    rating, review, users_id, movies_id
                    ) VALUES (
                    :rating, :review, :users_id, :movies_id
                    )");
    
                    $stmt->bindParam(":rating", $review->rating);
                    $stmt->bindParam(":review", $review->review);
                    $stmt->bindParam(":users_id", $review->users_id);
                    $stmt->bindParam(":movies_id", $review->movies_id);
                    
                    
                    $stmt->execute();
    
                    // Mensagem de sucesso por adicionar fime
                    $this->message->setMessage("Crítica adicionada com sucesso!", "success", "index.php");

            }
            public function getMoviesReview($id) {
                
                $reviews = [];

                $stmt = $this->conn->prepare("SELECT * FROM reviews WHERE movies_id = :movies_id");

                $stmt->bindParam(":movies_id", $id);

                $stmt->execute();

                if($stmt->rowCount() > 0) {
                    
                    $reviewsData = $stmt->fetchAll();

                    $userDao = new UserDao($this->conn, $this->url);

                    foreach($reviewsData as $review) {
                        
                        $reviewObject = $this->buildReview($review);

                        // Chamar dados do usuário
                        $user = $userDao->findById($reviewObject->users_id);

                        $reviewObject->user = $user;
                        
                        $reviews[] = $reviewObject;
                    }
                    
                }
                return $reviews;
            }
            public function hasAlreadyReviewed($id, $userId) {

            }
            public function getRatings($id) {

            }
        
    }