<script>
    (() => {
        const bulkForm = document.getElementById("bulk-form");
        if (!bulkForm) return;
        const selectAll = document.getElementById("select-all");
        const toolbar = document.getElementById("bulk-toolbar");
        const countLabel = document.getElementById("selected-count");
        const rows = () => Array.from(bulkForm.querySelectorAll(".row-checkbox"));
        const sync = () => {
            const checked = rows().filter((r) => r.checked);
            countLabel.textContent = checked.length;
            toolbar.classList.toggle("hidden", checked.length === 0);
            toolbar.classList.toggle("flex", checked.length > 0);
            selectAll.checked = checked.length > 0 && checked.length === rows().length;
            selectAll.indeterminate = checked.length > 0 && checked.length < rows().length;
        };
        selectAll.addEventListener("change", () => { rows().forEach((r) => (r.checked = selectAll.checked)); sync(); });
        bulkForm.addEventListener("change", (e) => { if (e.target.classList.contains("row-checkbox")) sync(); });
        bulkForm.querySelector("[data-bulk-delete]")?.addEventListener("click", (e) => {
            const count = rows().filter((r) => r.checked).length;
            if (!window.confirm(`Delete ${count} selected {{ $noun ?? 'item' }}(s) permanently?`)) e.preventDefault();
        });
        sync();
    })();
</script>
