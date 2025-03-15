<?php
include "include/layout/header.php";

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$searchQuery = isset($_GET['search']) ? $_GET['search'] : "Wolverine";
$moviesList = getMovies($searchQuery, $page);
$totalResults = $moviesList['totalResults'];
$movies = $moviesList['movies'];
$totalPages = ceil($totalResults / 10);

$userId = $_SESSION['user']['id'];

getFavouriteMovies($userId);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $movieId = $_POST['movie_id'];
    $movieTitle = $_POST['movie_title'];
    $thumbnail = $_POST['thumbnail'];
    $type = $_POST['type'];
    $year = $_POST['year'];

    if ($_POST['action'] === 'add') {
        $result = addToFavorites($userId, $movieId, $movieTitle, $thumbnail, $type, $year);
        if ($result === true) {
            $message = "Movie added to favorites.";
        } else {
            $message = $result;
        }
    } elseif ($_POST['action'] === 'remove') {
        $result = removeFromFavorites($userId, $movieId);
        if ($result === true) {
            $message = "Movie removed from favorites.";
        } else {
            $message = $result;
        }
    }
}

?>

<div class="container mt-5">
    <h2 class="text-center">Movies List</h2>

    <?php if (!empty($moviesList['error'])): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($moviesList['error']) ?>
        </div>
    <?php endif; ?>

    <!-- search -->

    <form method="GET" action="movies.php" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search for a movie..." value="<?= htmlspecialchars($searchQuery) ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>

    
    <table class="table table-bordered table-striped mt-3">
        <thead class="table-dark">
            <tr>
                <th>Poster</th>
                <th>Title</th>
                <th>Year</th>
                <th>Type</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($movies)): ?>
                <?php foreach ($movies as $movie): ?>
                    <?php  
                        $is_favourite = false;
                        $favouriteMovies = getFavouriteMovies($userId);
                        foreach ($favouriteMovies as $favouriteMovie) {
                            if ($favouriteMovie['movie_id'] === $movie['imdbID']) {
                                $is_favourite = true;
                                break;
                            }
                        }
                    ?>
                    <tr>
                        <td><img src="<?= $movie['Poster'] ?>" alt="Poster" width="50"></td>
                        <td><?= $movie['Title'] ?></td>
                        <td><?= $movie['Year'] ?></td>
                        <td><?= ucfirst($movie['Type']) ?></td>
                        <td>
                            <form method="POST" action="movies.php">
                                <input type="hidden" name="movie_id" value="<?= htmlspecialchars($movie['imdbID']) ?>">
                                <input type="hidden" name="movie_title" value="<?= htmlspecialchars($movie['Title']) ?>">
                                <input type="hidden" name="thumbnail" value="<?= htmlspecialchars($movie['Poster']) ?>">
                                <input type="hidden" name="year" value="<?= htmlspecialchars($movie['Year']) ?>">
                                <input type="hidden" name="type" value="<?= htmlspecialchars($movie['Type']) ?>">
                                <?php if ($is_favourite): ?>
                                    <button type="submit" name="action" value="remove" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this movie from favorites?')">Remove from Favorites</button>
                                <?php else: ?>
                                    <button type="submit" name="action" value="add" class="btn btn-primary btn-sm">Add to Favorites</button>
                                <?php endif; ?>
                            </form>

                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No movies found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <nav>
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($searchQuery) ?>">Previous</a></li>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= min($totalPages, 10); $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($searchQuery) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($searchQuery) ?>">Next</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

</body>
</html>