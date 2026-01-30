function setupPagination(tableScrollId, paginationId, rowsPerPage = 10) {
    const tableScroll = document.getElementById(tableScrollId);
    const pagination = document.getElementById(paginationId);
    if (!tableScroll || !pagination) return null;

    const tableBody = tableScroll.querySelector('tbody');
    if (!tableBody) return null;

    const allRows = Array.from(tableBody.querySelectorAll('tr'));

    // Create elements if not present in HTML, but here we expect them to be there
    const prevBtn = pagination.querySelector('.prev-btn');
    const nextBtn = pagination.querySelector('.next-btn');
    const pageInfo = pagination.querySelector('.page-info');

    let currentPage = 1;

    function showPage(page) {
        // Filter out rows that are hidden by other filters (e.g. review filters)
        // We use the custom attribute data-hidden-by-filter="true" set in memberWorkshopPanel.js
        const visibleRows = allRows.filter(row => {
            return row.getAttribute('data-hidden-by-filter') !== 'true';
        });

        const totalPages = Math.ceil(visibleRows.length / rowsPerPage);
        if (page > totalPages && totalPages > 0) page = totalPages;
        if (page < 1) page = 1;
        currentPage = page;

        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        // Hide ALL rows first
        allRows.forEach(row => row.style.display = 'none');

        // Show the slice of visible rows
        visibleRows.forEach((row, index) => {
            if (index >= start && index < end) {
                row.style.display = '';
            }
        });

        // Update Text
        if (pageInfo) {
            pageInfo.textContent = `Page ${currentPage} of ${Math.max(1, totalPages)}`;
        }

        // Disable buttons
        if (prevBtn) prevBtn.disabled = currentPage <= 1;
        if (nextBtn) nextBtn.disabled = currentPage >= totalPages || totalPages === 0;

        // Show pagination only if there are enough rows
        if (visibleRows.length > rowsPerPage) {
            pagination.style.display = 'flex';
        } else {
            // If you want to see "Page 1 of 1" even for few rows, change this to 'flex'
            // But usually, hiding it is cleaner.
            pagination.style.display = (visibleRows.length > 0) ? 'none' : 'none';
        }
    }

    // Event Listeners
    if (prevBtn) {
        const newPrev = prevBtn.cloneNode(true);
        prevBtn.parentNode.replaceChild(newPrev, prevBtn);
        newPrev.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                showPage(currentPage);
            }
        });
    }

    if (nextBtn) {
        const newNext = nextBtn.cloneNode(true);
        nextBtn.parentNode.replaceChild(newNext, nextBtn);
        newNext.addEventListener('click', () => {
            const visibleRows = allRows.filter(row => row.getAttribute('data-hidden-by-filter') !== 'true');
            const totalPages = Math.ceil(visibleRows.length / rowsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                showPage(currentPage);
            }
        });
    }

    // Initial show
    showPage(1);

    return {
        update: () => showPage(1)
    };
}
