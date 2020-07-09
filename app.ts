// Érték objektumok típusai (value object types)
type Post = {
  id: number;
  title: string;
  body: string;
};

type Response_ = {
  count: number;
  items: Array<Post>;
  totalCharacterCount: number;
};

// Function típusok (function types)
type PostLister = () => Promise<Array<Post>>;

type PostListController = (lister: PostLister) => (limit: number) => Promise<Response_>;

const newPostListController: PostListController = (lister: PostLister) => {
  return function (limit): Promise<Response_> {
    return lister().then((posts) => {
      const limitedPosts = posts.slice(0, limit);

      return {
        count: limitedPosts.length,
        items: limitedPosts,
        totalCharacterCount: limitedPosts.reduce((acc, cr) => acc + cr.body.length, 0),
      };
    });
  };
};

// Implementálás
function mockPostLister() {
  return Promise.resolve([
    { id: 1, userId: 4, title: "teszt", body: "teszt" },
    { id: 2, userId: 4, title: "teszt2", body: "teszt2" },
    { id: 3, userId: 4, title: "teszt3", body: "teszt3" },
    { id: 4, userId: 4, title: "teszt4", body: "teszt4" },
    { id: 5, userId: 4, title: "teszt5", body: "teszt5" },
    { id: 6, userId: 4, title: "teszt6", body: "teszt6" },
  ]);
}

function networkPostLister() {
  return fetch("https://jsonplaceholder.typicode.com/posts").then((res) => (res.ok ? res.json() : []));
}

const randomStr = () => Math.random().toString(36).substring(7);
function randomPostLister() {
  return Promise.resolve(
    Array.from(Array(10).keys()).map((_, i) => ({ id: i, userId: 6, title: randomStr(), body: randomStr() }))
  );
}

// Összeszerelés
// const mockPostListerController = newController(mockPostLister);
// const networkPostListerController = newController(networkPostLister);
// Localstorage lister
// IndexedDB lister
// ...

// Futtatás

document.getElementById("strategy-selector-form").onsubmit = (e: any) => {
  e.preventDefault();
  const strategy = e.target.elements.selectedStrategy.value;
  const limit = e.target.elements.limit.value;

  if (strategy === "mock") {
    const mockPostListerController = newPostListController(mockPostLister);
    mockPostListerController(limit).then(renderPosts);
  }

  if (strategy === "network") {
    const networkPostListerController = newPostListController(networkPostLister);
    networkPostListerController(limit).then(renderPosts);
  }
  if (strategy === "random") {
    const randomPostListerController = newPostListController(randomPostLister);
    randomPostListerController(limit).then(renderPosts);
  }
};

function renderPosts(response) {
  document.getElementById("posts-container").innerHTML = `
    <ul class="list-group">
      ${response.items.map((post) => `<li class="list-group-item">${post.title}</li>`).join("")}
    </ul>
    `;
}
