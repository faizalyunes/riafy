<?php
include "include/layout/header.php";
$userId = $_SESSION['user']['id'];
$favourites = getFavouriteMovies($userId);
$totalResults = count($favourites);
$totalPages = ceil($totalResults / 10);
$page = isset($_GET['page']) ? $_GET['page'] : 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $movieId = $_POST['movie_id'];
    $movieTitle = $_POST['movie_title'];
    $thumbnail = $_POST['thumbnail'];

    if ($_POST['action'] === 'remove') {
        $result = removeFromFavorites($userId, $movieId);
        if ($result === true) {
            $message = "Movie removed from favorites.";
            $favourites = getFavouriteMovies($userId);
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
            <?php if(!empty($favourites)): ?>
                <?php foreach ($favourites as $movie): ?>
                    <tr>
                        <td><img src="<?= $movie['thumbnail'] ?>" alt="Poster" width="50"></td>
                        <td><?= $movie['movie_title'] ?></td>
                        <td><?= $movie['movie_year'] ?></td>
                        <td><?= ucfirst($movie['movie_type']) ?></td>
                        <td>
                            <form method="POST" action="favourites.php">
                                <input type="hidden" name="movie_id" value="<?= htmlspecialchars($movie['movie_id']) ?>">
                                <input type="hidden" name="movie_title" value="<?= htmlspecialchars($movie['movie_title']) ?>">
                                <input type="hidden" name="thumbnail" value="<?= htmlspecialchars($movie['thumbnail']) ?>">
                                <button type="submit" name="action" value="remove" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this movie from favorites?')">Remove from Favorites</button>
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