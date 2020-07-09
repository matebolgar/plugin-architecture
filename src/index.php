<?php

class Response
{
    public int $count;
    public array $items;
    public int $totalCharacterCount;
    public function __construct(int $count, array $items, int $totalCharacterCount)
    {
        $this->count = $count;
        $this->items = $items;
        $this->totalCharacterCount = $totalCharacterCount;
    }
}

class Post
{
    public int $id;
    public string $title;
    public string $body;

    public function __construct(int $id, string $title, string $body)
    {
        $this->id = $id;
        $this->title = $title;
        $this->body = $body;
    }
}

interface PostLister
{
    /**
     * @return Post[]
     */
    public function list(): array;
}

class NetworkPostLister implements PostLister
{

    /**
     * @return Post[]
     */
    public function list(): array
    {
        $content = file_get_contents("https://jsonplaceholder.typicode.com/posts");
        $posts = json_decode($content, true);
        return array_map(fn ($post) => new Post($post['id'], $post['title'], $post['body']), $posts);
    }
}
class MockPostLister implements PostLister
{
    /**
     * @return Post[]
     */
    public function list(): array
    {
        return [
            new Post(1, "teszt1", "ok"),
            new Post(2, "teszt2", "ok"),
            new Post(3, "teszt3", "ok"),
            new Post(4, "teszt4", "ok"),
            new Post(5, "teszt5", "ok"),
            new Post(6, "teszt6", "ok"),
        ];
    }
}

class RandomPostLister implements PostLister
{
    /**
     * @return Post[]
     */
    public function list(): array
    {
        return array_map(fn ($num) => new Post($num, $this->getRandomString(), $this->getRandomString()), range(0, 10));
    }

    private function getRandomString()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

class ListController
{
    private PostLister $lister;

    public function __construct(PostLister $lister)
    {
        $this->lister = $lister;
    }

    public function getPosts(int $limit): Response
    {
        $posts = $this->lister->list();
        $limitedPosts = array_slice($posts, 0, $limit);
        $totalCharCount = array_reduce($limitedPosts, fn ($acc, $post) => $acc + strlen($post->body), 0);
        return new Response(count($limitedPosts), $limitedPosts, $totalCharCount);
    }
}

// $controller = new ListController(new NetworkPostLister);
// $controller = new ListController(new MockPostLister);
$controller = new ListController(new RandomPostLister);

$response = $controller->getPosts(5);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous" />
</head>

<body>
    <div class="container">
        <div class="row m-5 border p-5">
        <div id="posts-container" class="w-100">
                <h3>Karakterek száma: <?= $response->totalCharacterCount ?></h3>
                <ul class="list-group">
                    <?php foreach ($response->items as $post) : ?>
                        <li class="list-group-item"><?= $post->body ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</body>

</html>