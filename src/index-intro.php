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


class NetworkPostLister
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

class ListController
{
    private NetworkPostLister $networkPostLister;

    public function __construct(NetworkPostLister $networkPostLister)
    {
        $this->networkPostLister = $networkPostLister;
    }

    public function getPosts(int $limit): Response
    {
        $posts = $this->networkPostLister->list();
        $limitedPosts = array_slice($posts, 0, $limit);
        $totalCharCount = array_reduce($limitedPosts, fn ($acc, $post) => $acc + strlen($post->body), 0);
        return new Response(count($limitedPosts), $limitedPosts, $totalCharCount);
    }
}

$controller = new ListController(new NetworkPostLister);
$response = $controller->getPosts(5);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" />
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