document.addEventListener("DOMContentLoaded", function () {

    // ================= TIMELINE REVEAL ANIMATION =================
    const timelineItems = document.querySelectorAll(".timeline-item");
    const observerOptions = {
        threshold: 0.1
    };

    const timelineObserver = new IntersectionObserver(function (entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add("timeline-visible");
            }
        });
    }, observerOptions);

    timelineItems.forEach(item => {
        timelineObserver.observe(item);
    });

    // ================= IMPACT COUNTERS ANIMATION =================
    const counters = document.querySelectorAll(".counter h3");
    const counterObserver = new IntersectionObserver(function (entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.classList.contains("counted")) {
                const target = +entry.target.innerText.replace("+", "");
                let count = 0;
                const increment = target / 200; // speed of counting

                const updateCount = () => {
                    count += increment;
                    if (count < target) {
                        entry.target.innerText = Math.ceil(count) + "+";
                        requestAnimationFrame(updateCount);
                    } else {
                        entry.target.innerText = target + "+";
                    }
                };
                updateCount();
                entry.target.classList.add("counted");
            }
        });
    }, { threshold: 1 });

    counters.forEach(counter => {
        counterObserver.observe(counter);
    });

    // ================= TESTIMONIALS CAROUSEL =================
    const testimonialSlider = document.querySelector(".testimonial-slider");
    let isDown = false;
    let startX;
    let scrollLeft;

    if (testimonialSlider) {
        testimonialSlider.addEventListener("mousedown", (e) => {
            isDown = true;
            testimonialSlider.classList.add("active");
            startX = e.pageX - testimonialSlider.offsetLeft;
            scrollLeft = testimonialSlider.scrollLeft;
        });
        testimonialSlider.addEventListener("mouseleave", () => {
            isDown = false;
            testimonialSlider.classList.remove("active");
        });
        testimonialSlider.addEventListener("mouseup", () => {
            isDown = false;
            testimonialSlider.classList.remove("active");
        });
        testimonialSlider.addEventListener("mousemove", (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - testimonialSlider.offsetLeft;
            const walk = (x - startX) * 2; // scroll speed
            testimonialSlider.scrollLeft = scrollLeft - walk;
        });
    }

});
