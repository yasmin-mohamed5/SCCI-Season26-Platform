// Mini navbar change pages
const links = document.querySelectorAll(".miniNav a");
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
}

// prevent link default
links.forEach((link) => {
  link.addEventListener("click", (e) => {
    e.preventDefault();

    const targetId = link.dataset.page;

    // save to localStorage
    localStorage.setItem("activePanel", targetId);

    // activate immediately
    activatePage(targetId);
  });
});

// restore on page load
const savedPanel = localStorage.getItem("activePanel");

if (savedPanel) {
  activatePage(savedPanel);
} else {
  // fallback: first link
  const firstPage = links[0].dataset.page;
  activatePage(firstPage);
}

// ==============================================================
document.addEventListener('DOMContentLoaded', function () {

  /* ================== WEEK TABS SELECTION ================== */
  /* Handles switching between different workshop weeks */
  const weekTabs = document.querySelectorAll('.weekTab');

  weekTabs.forEach(tab => {
    tab.addEventListener('click', function () {
      weekTabs.forEach(t => t.classList.remove('active'));
      this.classList.add('active');

      const weekNumber = this.getAttribute('data-week');
    });
  });

  /* ================== FILE UPLOAD HANDLING ================== */
  /* Manages file selection, drag & drop, and validation */
  const uploadContainer = document.querySelector('.uploadContainer');
  const fileInput = document.getElementById('taskFile');
  const fileMessage = document.getElementById('fileMessage');
  const fileState = document.getElementById('fileUploadState');
  const fileUploadedName = document.getElementById('fileUploadedName');
  const submitForm = document.getElementById('validForm');

  fileMessage.textContent = "";


  /* Event: Regular file input selection */
  /* Updates UI when a user selects a file via the browse dialog */
  fileInput.addEventListener('change', function () {
    if (this.files.length > 0) {
      fileState.textContent = "File Uploaded Successfully!";
      fileState.style.color = "green";
      fileUploadedName.textContent = this.files[0].name;
      fileUploadedName.style.display = "block";
      fileMessage.textContent = "";
    } else {
      fileState.textContent = "Drag and drop or click to browse";
      fileState.style.color = "";
      fileUploadedName.style.display = "none";
    }
  });

  /* Event: Prevent default drag behaviors */
  /* Stops browser from opening files dropped outside the zone */
  ['dragover', 'drop'].forEach(eventName => {
    uploadContainer.addEventListener(eventName, e => {
      e.preventDefault();
      e.stopPropagation();
    });
  });

  /* Event: Drag Over */
  /* Adds visual cue when file is dragged over the drop zone */
  uploadContainer.addEventListener('dragover', () => {
    uploadContainer.classList.add('drag-over');
  });

  /* Event: File Drop */
  /* Handles the file drop, updates input, and triggers change event */
  uploadContainer.addEventListener('drop', (e) => {
    uploadContainer.classList.remove('drag-over');

    const files = e.dataTransfer.files;

    if (files.length > 0) {
      const dataTransfer = new DataTransfer();
      dataTransfer.items.add(files[0]);
      fileInput.files = dataTransfer.files;

      fileInput.dispatchEvent(
        new Event('change', { bubbles: true })
      );
    }
  });

  /* ================== FORM SUBMIT VALIDATION ================== */
  /* Ensures a file is selected before allowing form submission */
  submitForm.addEventListener('submit', (e) => {
    e.preventDefault();

    if (!fileInput.files.length) {
      fileMessage.textContent = "Please upload a file.";
      fileMessage.style.color = "red";
      return;
    }

    submitForm.submit();
  });

});
