document.addEventListener('DOMContentLoaded', function () {
    // 1. Tab Switching (Session Details)
    const tabs = document.querySelectorAll('.nav-tab');
    const contents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Remove active class from all
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));

            // Add active to current
            tab.classList.add('active');
            const targetId = tab.dataset.target;
            document.getElementById(targetId).classList.add('active');
        });
    });

    // 2. Search Functionality (Simple DOM filtering for now)
    const searchInput = document.getElementById('adminSearchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('.admin-table tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // 3. User Filter (Role)
    const roleFilter = document.getElementById('roleFilter');
    if (roleFilter) {
        roleFilter.addEventListener('change', function () {
            const role = this.value.toLowerCase();
            const rows = document.querySelectorAll('.admin-table tbody tr');

            rows.forEach(row => {
                const rowRole = row.getAttribute('data-role').toLowerCase();
                if (role === '' || rowRole === role) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // 4. Confirm Block/Delete
    const blockBtns = document.querySelectorAll('.btn-block-user');
    blockBtns.forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (!confirm('Are you sure you want to block/unblock this user?')) {
                e.preventDefault();
            }
        });
    });
});
