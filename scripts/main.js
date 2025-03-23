
document.addEventListener("scroll", function () {
    const boxes = document.querySelectorAll(".box");
 
const imgs = document.getElementById("img");
   const img = document.querySelectorAll("#img img");
   
   let idx = 0;

function iniciarCarrossel() {
    const imgs = document.querySelector(".container");
    const img = document.querySelectorAll(".container img");

    setInterval(() => {
        idx++;
        if (idx > img.length - 1) {
            idx = 0;
        }
        imgs.style.transform = `translateX(${-idx * 300}px)`;
    }, 1800);
}

   
document.addEventListener("DOMContentLoaded", function () {
  ScrollReveal().reveal('.box', { 
      duration: 1000, 
      origin: 'bottom', 
      distance: '50px',
      reset: true 
  });

  
  iniciarCarrossel();
});


    boxes.forEach((box) => {
        const boxTop = box.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;

        if (boxTop < windowHeight - 50) {
            box.classList.add("show");
        }
    });
});
