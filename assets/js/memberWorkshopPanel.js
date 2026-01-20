/* ==========================================================
   SCCI Panel JS - Fixed Tabs + Upload + Validation + Popups
   ========================================================== */

document.addEventListener("DOMContentLoaded", () => {
  /* ===========================
     Tabs (Mini Navbar)
     - Priority: URL ?tab=  -> localStorage -> first tab
     - Sync URL without reload
     =========================== */
  const links = document.querySelectorAll(".miniNav a[data-page]");
  const pages = document.querySelectorAll(".panelSection");

  function activatePage(pageId) {
    // activate nav link
    links.forEach((link) => {
      link.classList.toggle("activePanelLine", link.dataset.page === pageId);
    });

    // activate panel
    pages.forEach((page) => {
      page.classList.toggle("panelSectionActive", page.id === pageId);
    });

    // save to localStorage
    localStorage.setItem("activePanel", pageId);

    // sync URL tab without reload
    const url = new URL(window.location.href);
    url.searchParams.set("tab", pageId);
    history.replaceState({}, "", url.toString());
  }

  links.forEach((link) => {
    link.addEventListener("click", (e) => {
      e.preventDefault();
      activatePage(link.dataset.page);
    });
  });

  const urlTab = new URLSearchParams(window.location.search).get("tab");
  const savedPanel = localStorage.getItem("activePanel");
  const defaultTab = links.length ? links[0].dataset.page : null;
  const candidate = urlTab || savedPanel || defaultTab;

  if (candidate && document.getElementById(candidate)) {
    activatePage(candidate);
  } else if (defaultTab) {
    activatePage(defaultTab);
  }

  /* ===========================
     Open popup (Feedback Modal)
     - Set submission_id inside modal
     =========================== */
  document.querySelectorAll(".evaluateFeedback").forEach((btn) => {
    btn.addEventListener("click", () => {
      const popupId = btn.dataset.popup;
      const popup = document.getElementById(popupId);
      if (!popup) return;

      // ✅ set submission id inside the modal (if exists)
      const submissionId = btn.dataset.submissionId || 0;
      const input = document.getElementById("submissionIdInput");
      if (input) input.value = submissionId;

      popup.classList.add("active");
      document.body.classList.add("no-scroll");
    });
  });

  // Close popup (X button or Cancel)
  document.addEventListener("click", (e) => {
    if (e.target.closest(".closeFeedback") || e.target.closest(".modalCancelBtn")) {
      const popup = e.target.closest(".reviewFeedbackPopup");
      if (!popup) return;

      popup.classList.remove("active");
      document.body.classList.remove("no-scroll");
    }
  });

  // Close when clicking overlay
  document.querySelectorAll(".reviewFeedbackPopup").forEach((popup) => {
    popup.addEventListener("click", (e) => {
      if (e.target === popup) {
        popup.classList.remove("active");
        document.body.classList.remove("no-scroll");
      }
    });
  });

  // Escape key closes popups
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      document.querySelectorAll(".reviewFeedbackPopup.active").forEach((popup) => {
        popup.classList.remove("active");
      });
      document.body.classList.remove("no-scroll");
    }
  });

  /* ===========================
     Sessions UI (ONLY UI)
     =========================== */
  (function () {
    const ACTIVE_COLOR = "#1f184e";
    const DEFAULT_FILL = "var(--color-white-gradient)";
    const sessionButtons = document.querySelectorAll(".sessionBtn");

    if (!sessionButtons.length) return;

    function deactivateAllSessions() {
      sessionButtons.forEach((btn) => {
        btn.classList.remove("sessionActive");

        const body = btn.querySelector(".panelBody");
        if (body) {
          body.classList.remove("sessionBlue");
          body.classList.add("sessionWhite");
        }

        btn.querySelectorAll("svg path").forEach((path) => {
          path.setAttribute("fill", DEFAULT_FILL);
          path.setAttribute("stroke", DEFAULT_FILL);
        });
      });
    }

    function activateSession(button) {
      button.classList.add("sessionActive");

      const body = button.querySelector(".panelBody");
      if (body) {
        body.classList.remove("sessionWhite");
        body.classList.add("sessionBlue");
      }

      button.querySelectorAll("svg path").forEach((path) => {
        path.setAttribute("fill", ACTIVE_COLOR);
        path.setAttribute("stroke", ACTIVE_COLOR);
      });
    }

    sessionButtons.forEach((btn) => {
      btn.addEventListener("click", function () {
        deactivateAllSessions();
        activateSession(this);
        // DO NOT preventDefault (because it's <a href>)
      });
    });
  })();

  /* ===========================
     Attendance Auto-Submit
     =========================== */
  document.addEventListener("change", (e) => {
    if (e.target && e.target.type === "radio" && e.target.name === "status") {
      const form = e.target.closest("form");
      if (form) form.submit();
    }
  });

  /* ===========================
     Feedback Stars (rating)
     =========================== */
  document.addEventListener("click", (e) => {
    const star = e.target.closest(".feedbackStars");
    if (!star) return;

    const rating = Number(star.dataset.rating || 0);
    const starsWrapper = star.closest(".feedbackStarsInput");
    if (!starsWrapper) return;

    const stars = starsWrapper.querySelectorAll(".feedbackStars");

    const ratingInput = document.getElementById("ratingValue");
    if (ratingInput) ratingInput.value = rating;

    stars.forEach((s) => {
      const value = Number(s.dataset.rating || 0);
      if (value <= rating) {
        s.classList.remove("fa-regular");
        s.classList.add("fa-solid");
      } else {
        s.classList.remove("fa-solid");
        s.classList.add("fa-regular");
      }
    });
  });

  /* ===========================
     Feedback Form Validation
     - supports #feedback_text OR #addFeedback
     =========================== */
  const feedbackForm = document.getElementById("feedbackForm");
  if (feedbackForm) {
    feedbackForm.addEventListener("submit", (event) => {
      event.preventDefault();

      const msg = document.getElementById("addFeedbackMessage");

      // ✅ support both ids
      const textEl =
        document.getElementById("feedback_text") ||
        document.getElementById("addFeedback");

      const text = (textEl?.value || "").trim();

      if (msg) {
        msg.textContent = "";
        msg.style.color = "";
      }

      if (!text) {
        if (msg) {
          msg.textContent = "feedback is required";
          msg.style.color = "red";
          msg.style.fontSize = "12px";
        }
        return;
      }

      // optional: ensure rating selected
      const ratingVal = Number(document.getElementById("ratingValue")?.value || 0);
      if (ratingVal <= 0) {
        if (msg) {
          msg.textContent = "Please choose a rating.";
          msg.style.color = "red";
          msg.style.fontSize = "12px";
        }
        return;
      }

      feedbackForm.submit();
    });
  }

  /* ===========================
     File Upload Handling
     =========================== */
  document.querySelectorAll(".fileUpload").forEach((container) => {
    const fileInput = container.querySelector(".taskFileInput");
    const fileState = container.querySelector(".uploadText");
    const fileUploadedName = container.querySelector(".fileUploadedName");
    const fileMessage = container.querySelector(".fileMessage");
    const uploadBtn = container.querySelector(".uploadBtn");

    if (uploadBtn && fileInput) {
      uploadBtn.addEventListener("click", (e) => {
        e.preventDefault();
        fileInput.click();
      });
    }

    if (fileInput) {
      fileInput.addEventListener("change", function () {
        const file = this.files && this.files[0];

        if (file) {
          if (fileUploadedName) {
            fileUploadedName.textContent = file.name;
            fileUploadedName.style.display = "block";
          }

          if (fileState) {
            fileState.textContent = "File Uploaded Successfully!";
            fileState.style.color = "green";
          }

          if (fileMessage) fileMessage.textContent = "";
        } else {
          if (fileUploadedName) {
            fileUploadedName.textContent = "";
            fileUploadedName.style.display = "none";
          }

          if (fileState) {
            fileState.textContent = "Drag and drop or click to browse";
            fileState.style.color = "";
          }
        }
      });
    }
  });

  /* ===========================
     Form Validation
     - Supports Task form + Material form
     =========================== */
  document.querySelectorAll("form.validForm").forEach((form) => {
    form.addEventListener("submit", function (event) {
      event.preventDefault();

      let isValid = true;

      const nameInput =
        form.querySelector("input[name='taskName']") ||
        form.querySelector("input[name='materialName']") ||
        form.querySelector("input[type='text']");

      const nameValue = (nameInput?.value || "").trim();
      const nameMsg = form.querySelector("#taskNameMessage");

      const descriptionEl = form.querySelector("textarea[name='description']");
      const deadlineEl = form.querySelector("input[name='dueDate']");

      const descriptionMsg = form.querySelector("#descriptionMessage");
      const deadlineMsg = form.querySelector("#dueDateMessage");

      const fileEl = form.querySelector(".taskFileInput");
      const file = fileEl?.files?.[0];
      const fileMsg = form.querySelector(".fileMessage");

      // reset
      if (nameMsg) nameMsg.textContent = "";
      if (descriptionMsg) descriptionMsg.textContent = "";
      if (deadlineMsg) deadlineMsg.textContent = "";
      if (fileMsg) fileMsg.textContent = "";

      if (!nameValue) {
        if (nameMsg) {
          nameMsg.textContent = "This field is required.";
          nameMsg.style.color = "red";
          nameMsg.style.fontSize = "12px";
        }
        isValid = false;
      }

      if (!file) {
        if (fileMsg) {
          fileMsg.textContent = "Please upload a file.";
          fileMsg.style.color = "red";
          fileMsg.style.fontSize = "12px";
        }
        isValid = false;
      }

      if (deadlineEl && !deadlineEl.value) {
        if (deadlineMsg) {
          deadlineMsg.textContent = "Deadline is required.";
          deadlineMsg.style.color = "red";
          deadlineMsg.style.fontSize = "12px";
        }
        isValid = false;
      }

      if (descriptionEl && !descriptionEl.value.trim()) {
        if (descriptionMsg) {
          descriptionMsg.textContent = "Description is required.";
          descriptionMsg.style.color = "red";
          descriptionMsg.style.fontSize = "12px";
        }
        isValid = false;
      }

      if (isValid) form.submit();
    });
  });

  /* ===========================
     Delete Task
     =========================== */
  window.deleteTask = function(taskId) {
    if (confirm('Are you sure you want to delete this task?')) {
      window.location.href = '?session_id=' + getCurrentSessionId() + '&tab=addTask&delete_task_id=' + taskId;
    }
  };

  /* ===========================
     Delete Material
     =========================== */
  window.deleteMaterial = function(materialId) {
    if (confirm('Are you sure you want to delete this material?')) {
      window.location.href = '?session_id=' + getCurrentSessionId() + '&tab=addMaterial&delete_material_id=' + materialId;
    }
  };

  function getCurrentSessionId() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('session_id') || '0';
  }

  /* ===========================
     Material Filtering
     =========================== */
  document.querySelectorAll('.materialTypeButton').forEach(button => {
    button.addEventListener('click', function() {
      const filter = this.getAttribute('data-filter');
      const techMaterials = document.getElementById('techMaterials');
      const softMaterials = document.getElementById('softMaterials');

      // Remove active class from all buttons
      document.querySelectorAll('.materialTypeButton').forEach(btn => btn.classList.remove('active'));

      // Add active class to clicked button
      this.classList.add('active');

      if (filter === 'technical') {
        if (techMaterials) techMaterials.style.display = 'block';
        if (softMaterials) softMaterials.style.display = 'none';
      } else if (filter === 'soft') {
        if (techMaterials) techMaterials.style.display = 'none';
        if (softMaterials) softMaterials.style.display = 'block';
      }
    });
  });

  console.log("JS Loaded ✅");
});

