// função para adicionar o link ativado ao header
document.addEventListener("DOMContentLoaded", function () {
    document.querySelector("#link-sobre").innerHTML = `<li class="nav-item"><a href="<?= $base_url ?>/index.php" class="nav-link active" style="background-color: #4F46E5;">About</a></li>`;
});