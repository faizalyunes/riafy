<?php
session_start();
include "database/db.php";

// Function to register a new user
function registerUser($name, $email, $password) {
    global $connection;
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $checkemail = $connection->query("SELECT * FROM users WHERE email='$email'");
    if ($checkemail->num_rows > 0) {
        return "Email already exists!";
    }
    $sql = $connection->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $sql->bind_param("sss", $name, $email, $hashedPassword);
    if ($sql->execute()) {
        $_SESSION["success_message"] = "User registered successfully! Please login to continue."; 
        header("Location: index.php");
        exit();
    } else {
        return "Error: " . $connection->error;
    }
}

// Function to login a user
function loginUser($email, $password) {
    global $connection;

    $result = $connection->query("SELECT * FROM users WHERE email='$email'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user["password"])) {
            $_SESSION["user"] = $user;
            header("Location: dashboard.php");
            exit();
        } else {
            return "Invalid password!";
        }
    } else {
        return "User not found!";
    }
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION["user"]);
}

// Function to logout user
function logoutUser() {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Function to handle logout
function logout() {
    if (isset($_GET['action']) && $_GET['action'] === 'logout') {
        logoutUser();
    }
}

// Function to fetch movies
function getMovies($searchQuery = "", $page = 1) { 
    $apiKey = "fd3dbafa"; 
    $apiUrl = "http://www.omdbapi.com/?apikey=$apiKey&s=" . urlencode($searchQuery) . "&page=$page";

    $options = [
        "http" => [
            "method" => "GET",
            "timeout" => 10 // Increase timeout to 10 seconds
        ]
    ];
    
    $context = stream_context_create($options);
    
    $response = file_get_contents($apiUrl, false, $context);
    if ($response === false) {
        return [
            'movies' => [],
            'totalResults' => 0,
            'error' => "Failed to fetch movies."
        ];
    }
    $moviesData = json_decode($response, true);

    if (isset($moviesData['Error'])) {
        return [
            'movies' => [],
            'totalResults' => 0,
            'error' => $moviesData['Error']
        ];
    }

    return [
        'movies' => isset($moviesData['Search']) ? $moviesData['Search'] : [],
        'totalResults' => isset($moviesData['totalResults']) ? (int)$moviesData['totalResults'] : 0,
        'error' => null
    ];
}

// Function to get favorite movies
function getFavouriteMovies($userId) {
    global $connection;

    // Prepare an SQL statement to prevent SQL injection
    $favouritequery = $connection->prepare("SELECT movie_id, movie_title, thumbnail, movie_year, movie_type FROM favorite_movies WHERE user_id = ?");
    $favouritequery->bind_param("i", $userId);

    $favouritequery->execute();
    $result = $favouritequery->get_result();
    $favorites = $result->fetch_all(MYSQLI_ASSOC);

    return $favorites;
}

// Function to add a movie to favorites
function addToFavorites($userId, $movieId, $movieTitle, $thumbnail, $type, $year) {
    global $connection;

    // Check if the movie is already in favorites
    $movieexist = $connection->prepare("SELECT * FROM favorite_movies WHERE user_id = ? AND movie_id = ?");
    $movieexist->bind_param("is", $userId, $movieId);
    $movieexist->execute();
    $result = $movieexist->get_result();

    if ($result->num_rows > 0) {
        return "Movie is already in favorites.";
    }

    // Add the movie to favorites
    $insertquery = $connection->prepare("INSERT INTO favorite_movies (user_id, movie_id, movie_title, thumbnail, movie_type, movie_year) VALUES (?, ?, ?, ?, ?, ?)");
    $insertquery->bind_param("isssss", $userId, $movieId, $movieTitle, $thumbnail, $type, $year);

    if ($insertquery->execute()) {
        return true;
    } else {
        return "Error: " . $connection->error;
    }
}

// Remove movie from favorites
function removeFromFavorites($userId, $movieId) {
    global $connection;

    $removequery = $connection->prepare("DELETE FROM favorite_movies WHERE user_id = ? AND movie_id = ?");
    $removequery->bind_param("is", $userId, $movieId);

    if ($removequery->execute()) {
        return true;
    } else {
        return "Error: " . $connection->error;
    }
}

?>