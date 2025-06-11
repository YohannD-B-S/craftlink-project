const hamburger = document.getElementById("hamburger");
const mobile = document.getElementById("mobile");
const closeButton = document.getElementById("close-mobile");
const overlay = document.getElementById("overlay");

function openMenu() {
    mobile.style.right = "0%";
    overlay.style.display = "block";
}

function closeMenu() {
    mobile.style.right = "-100%";
    overlay.style.display = "none";
}

hamburger.addEventListener("click", openMenu);
closeButton.addEventListener("click", closeMenu);
overlay.addEventListener("click", closeMenu);

// Toggle submenus on mobile
const mobileDropdowns = mobile.querySelectorAll(".dropdown > a");

mobileDropdowns.forEach(link => {
    link.addEventListener("click", e => {
        const parentLi = link.parentElement;
        parentLi.classList.toggle("open");
    });
});
