// get table section
const userTableScroll = document.getElementById('userTableScroll');

// get pagination controls
const itPagination = document.getElementById('itPagination');

// get search input
const searchInput = document.getElementById('searchInput');

// get workshop filter
const workshopFilter = document.getElementById('workshopFilter');

let userManager;

// Function to handle pagination AND search
function setupPagination(tableScrollId, paginationId, rowsPerPage = 10) {
    const tableScroll = document.getElementById(tableScrollId);
    const pagination = document.getElementById(paginationId);
    if (!tableScroll || !pagination) return null;

    const tableBody = tableScroll.querySelector('tbody');
    // Store original rows for filtering
    const originalRows = Array.from(tableBody.querySelectorAll('tr'));

    const prevBtn = pagination.querySelector('.prev-btn');
    const nextBtn = pagination.querySelector('.next-btn');
    const pageInfo = pagination.querySelector('.page-info');

    let currentPage = 1;
    // Current rows starts as all rows
    let currentRows = [...originalRows];
    let totalPages = Math.ceil(currentRows.length / rowsPerPage);

    function showPage(page) {
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        // Hide ALL original rows first to be safe
        originalRows.forEach(row => row.style.display = 'none');

        // Show the slice of CURRENT (filtered) rows
        currentRows.forEach((row, index) => {
            if (index >= start && index < end) {
                row.style.display = '';
            }
        });

        // Update PAGINATION INFO
        if (pageInfo) pageInfo.textContent = `Page ${page} of ${Math.max(1, totalPages)}`;

        // Update BUTTON STATES
        if (prevBtn) prevBtn.disabled = page <= 1;
        if (nextBtn) nextBtn.disabled = page >= totalPages || totalPages === 0;
    }

    // Filter Function
    function filter(searchQuery, workshopValue) {
        searchQuery = searchQuery ? searchQuery.toLowerCase().trim() : '';
        workshopValue = workshopValue ? workshopValue.trim() : '';

        currentRows = originalRows.filter(row => {
            // Text Search Check
            const matchesSearch = !searchQuery || row.textContent.toLowerCase().includes(searchQuery);

            // Workshop Check
            let matchesWorkshop = true;
            if (workshopValue) {
                const rowWorkshop = row.getAttribute('data-workshop') || '';
                matchesWorkshop = rowWorkshop.trim() === workshopValue;
            }

            return matchesSearch && matchesWorkshop;
        });

        // Reset to page 1 after filter
        currentPage = 1;
        totalPages = Math.ceil(currentRows.length / rowsPerPage);
        showPage(currentPage);
    }

    // Event Listeners for Pagination
    if (prevBtn) {
        prevBtn.onclick = () => {
            if (currentPage > 1) {
                currentPage--;
                showPage(currentPage);
            }
        };
    }

    if (nextBtn) {
        nextBtn.onclick = () => {
            if (currentPage < totalPages) {
                currentPage++;
                showPage(currentPage);
            }
        };
    }

    // Initial show
    showPage(1);
    
    // Show/Hide pagination based on results
    if (currentRows.length > 0) {
        pagination.style.display = 'flex';
    } else {
        pagination.style.display = 'none';
    }

    return { filter };
}

// Initialize pagination logic
document.addEventListener('DOMContentLoaded', () => {
    userManager = setupPagination('userTableScroll', 'itPagination');

    function applyFilters() {
        const query = searchInput ? searchInput.value : '';
        const workshop = workshopFilter ? workshopFilter.value : '';

        if (userManager) {
            userManager.filter(query, workshop);
        }
    }

    // Listeners
    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }
    if (workshopFilter) {
        workshopFilter.addEventListener('change', applyFilters);
    }
});
