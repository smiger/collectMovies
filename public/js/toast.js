var Toast = {};
Toast.toast = function(msg) {
    var active = "toast-active";
    var div = document.createElement("div");
    div.classList.add("toast-container")
    div.innerHTML = '<div class="toast-message-container">' + msg + "</div>"
    div.addEventListener("webkitTransitionEnd", function() {
        div.classList.contains(active) || div.parentNode.removeChild(div)
    });
    document.body.appendChild(div)
    div.offsetHeight
    div.classList.add(active)
    setTimeout(function() {
        div.classList.remove(active)
    }, 3000)
}