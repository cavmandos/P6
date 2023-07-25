document.addEventListener("DOMContentLoaded", function () {
    const btnShowImages = document.querySelector(".btn-show-images");
    const mediaRow = document.querySelector(".media-row");
    const btnMargin = document.querySelector(".margin-play");

    btnShowImages.addEventListener("click", function () {
        mediaRow.classList.toggle("d-none");
        btnMargin.classList.toggle("mb-3");
        if (mediaRow.classList.contains("d-none")) {
            btnShowImages.textContent = "Afficher les médias";
        } else {
            btnShowImages.textContent = "Masquer les médias";
        }
    });
});