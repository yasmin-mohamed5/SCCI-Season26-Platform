document.addEventListener("DOMContentLoaded", () => {

    /* =================Event ================= */
    const eventsData = {
        opening: createEvent("opening"),
        ushering: createEvent("ushering"),
        firstSession: createEvent("firstSession"),
        "57357": createEvent("57357"),
        outing: createEvent("outing"),
        midYear: createEvent("midYear"),
        comptition: createEvent("comptition"),
        league: createEvent("league"),
        academic: createEvent("academic"),
        confrence: createEvent("confrence"),
        closing: createEvent("closing")
    };

    function createEvent(name) {
        return {
            book: {
                left: `assets/img/${name}/book1.jpg`,
                right: `assets/img/${name}/book2.jpg`
            },
            slider: Array.from({ length: 6 }, (_, i) =>
                `assets/img/${name}/slider${i + 1}.jpg`
            ),
            cards: Array.from({ length: 6 }, (_, i) =>
                `assets/img/${name}/card${i + 1}.jpg`
            )
        };
    }

    /* ================= Elements ================= */
    const papers = document.querySelectorAll(".paper");
    const bookSection = document.querySelector(".bookSection");
    const bookLeft = document.getElementById("bookImageLeft");
    const bookRight = document.getElementById("bookImageRight");

    const eventsSection = document.querySelector(".eventsSection");
    const cardsContainer = document.getElementById("cardsContainer");
    const slots = document.querySelectorAll(".sliderCard");
    const prevBtn = document.querySelector(".sliderBtn.prev");
    const nextBtn = document.querySelector(".sliderBtn.next");

    let currentEventKey = null;
    let images = [];
    let currentIndex = 0;
    let isAnimating = false;

    /* ================= PAPER CLICK ================= */
    papers.forEach(paper => {
        paper.addEventListener("click", e => {
            e.preventDefault();
            const key = paper.dataset.event;
            const data = eventsData[key];
            if (!data) return;

            currentEventKey = key;

            //  صور الكتاب
            bookLeft.src = data.book.left;
            bookRight.src = data.book.right;
            bookSection.classList.add("bookVisible");

            images = [];
            currentIndex = 0;
            slots.forEach(slot => {
                slot.querySelector(".imgPrimary").src = "";
                slot.querySelector(".imgSecondary").src = "";
            });
            cardsContainer.innerHTML = "";

            eventsSection.classList.remove("eventsVisible");
        });
    });

    /* ================= BOOK CLICK ================= */
    [bookLeft, bookRight].forEach(img => {
        img.addEventListener("click", () => {
            if (!currentEventKey) return;
            const data = eventsData[currentEventKey];

            images = data.slider;
            currentIndex = 0;
            updateImages();

            updateCards(currentEventKey);

            // يظهر الـ Events Section
            eventsSection.classList.add("eventsVisible");
            eventsSection.scrollIntoView({ behavior: "smooth" });
        });
    });

    /* ================= UPDATE CARDS ================= */
    function updateCards(eventKey) {
        const data = eventsData[eventKey];
        if (!data || !data.cards) return;

        cardsContainer.innerHTML = "";
        data.cards.forEach(src => {
            const figure = document.createElement("figure");
            figure.classList.add("polaroidItem");
            figure.style.setProperty("--rotation", `${Math.floor(Math.random() * 13 - 6)}deg`);

            const img = document.createElement("img");
            img.src = src;
            img.alt = "Event Card";
            img.loading = "lazy";

            figure.appendChild(img);
            cardsContainer.appendChild(figure);
        });
    }

    /* ================= SLIDER ================= */
    function updateImages(direction = null) {
        if (!images.length) return;
        const offsets = [-2, -1, 0, 1, 2];

        slots.forEach((slot, i) => {
            const primary = slot.querySelector(".imgPrimary");
            const secondary = slot.querySelector(".imgSecondary");

            let index = (currentIndex + offsets[i] + images.length) % images.length;
            const src = images[index];

            if (!direction) {
                primary.src = src;
                return;
            }

            secondary.src = src;
            secondary.style.display = "block";
            secondary.style.transform = direction === "next" ? "translateX(100%)" : "translateX(-100%)";

            requestAnimationFrame(() => {
                primary.style.transform = direction === "next" ? "translateX(-100%)" : "translateX(100%)";
                secondary.style.transform = "translateX(0)";
            });

            setTimeout(() => {
                primary.src = src;
                primary.style.transform = "translateX(0)";
                secondary.style.display = "none";
            }, 400);
        });
    }

    /* ================= card ================= */
    nextBtn.addEventListener("click", () => {
        if (isAnimating || !images.length) return;
        isAnimating = true;
        currentIndex = (currentIndex + 1) % images.length;
        updateImages("next");
        setTimeout(() => isAnimating = false, 400);
    });

    prevBtn.addEventListener("click", () => {
        if (isAnimating || !images.length) return;
        isAnimating = true;
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        updateImages("prev");
        setTimeout(() => isAnimating = false, 400);
    });

});
