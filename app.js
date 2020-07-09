var newPostListController = function (lister) {
    return function (limit) {
        return lister().then(function (posts) {
            var limitedPosts = posts.slice(0, limit);
            return {
                count: limitedPosts.length,
                items: limitedPosts,
                totalCharacterCount: limitedPosts.reduce(function (acc, cr) { return acc + cr.body.length; }, 0)
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
    return fetch("https://jsonplaceholder.typicode.com/posts").then(function (res) { return (res.ok ? res.json() : []); });
}
var randomStr = function () { return Math.random().toString(36).substring(7); };
function randomPostLister() {
    return Promise.resolve(Array.from(Array(10).keys()).map(function (_, i) { return ({ id: i, userId: 6, title: randomStr(), body: randomStr() }); }));
}
// Összeszerelés
// const mockPostListerController = newController(mockPostLister);
// const networkPostListerController = newController(networkPostLister);
// Localstorage lister
// IndexedDB lister
// ...
// Futtatás
document.getElementById("strategy-selector-form").onsubmit = function (e) {
    e.preventDefault();
    var strategy = e.target.elements.selectedStrategy.value;
    var limit = e.target.elements.limit.value;
    if (strategy === "mock") {
        var mockPostListerController = newPostListController(mockPostLister);
        mockPostListerController(limit).then(renderPosts);
    }
    if (strategy === "network") {
        var networkPostListerController = newPostListController(networkPostLister);
        networkPostListerController(limit).then(renderPosts);
    }
    if (strategy === "random") {
        var randomPostListerController = newPostListController(randomPostLister);
        randomPostListerController(limit).then(renderPosts);
    }
};
function renderPosts(response) {
    document.getElementById("posts-container").innerHTML = "\n    <ul class=\"list-group\">\n      " + response.items.map(function (post) { return "<li class=\"list-group-item\">" + post.title + "</li>"; }).join("") + "\n    </ul>\n    ";
}
