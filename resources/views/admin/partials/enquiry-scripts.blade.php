<script>
    (() => {
        const bulkForm = document.getElementById("bulk-form");

        if (bulkForm) {
            const selectAll = document.getElementById("select-all");
            const toolbar = document.getElementById("bulk-toolbar");
            const countLabel = document.getElementById("selected-count");
            const rows = () => Array.from(bulkForm.querySelectorAll(".row-checkbox"));

            const sync = () => {
                const checked = rows().filter((row) => row.checked);
                countLabel.textContent = checked.length;
                toolbar.classList.toggle("hidden", checked.length === 0);
                toolbar.classList.toggle("flex", checked.length > 0);
                selectAll.checked = checked.length > 0 && checked.length === rows().length;
                selectAll.indeterminate = checked.length > 0 && checked.length < rows().length;
            };

            selectAll.addEventListener("change", () => {
                rows().forEach((row) => (row.checked = selectAll.checked));
                sync();
            });

            bulkForm.addEventListener("change", (event) => {
                if (event.target.classList.contains("row-checkbox")) sync();
            });

            bulkForm.querySelector("[data-bulk-delete]")?.addEventListener("click", (event) => {
                const count = rows().filter((row) => row.checked).length;
                if (!window.confirm(`Delete ${count} selected enquiry(ies) permanently? This cannot be undone.`)) {
                    event.preventDefault();
                }
            });

            sync();
        }

        const modal = document.getElementById("enquiry-modal");

        if (modal) {
            const deleteForm = modal.querySelector("[data-delete-form]");
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

            const closeModal = () => {
                modal.classList.add("hidden");
                modal.classList.remove("flex");
                document.body.style.overflow = "";
            };

            const openModal = (data) => {
                modal.querySelectorAll("[data-field]").forEach((el) => {
                    el.textContent = data[el.dataset.field] ?? "";
                });
                if (deleteForm) deleteForm.action = data.deleteUrl;
                modal.classList.remove("hidden");
                modal.classList.add("flex");
                document.body.style.overflow = "hidden";
            };

            const markRead = (row, data) => {
                if (!data.readUrl || row.dataset.read === "1") return;
                fetch(data.readUrl, {
                    method: "PATCH",
                    headers: { Accept: "application/json", "X-CSRF-TOKEN": csrf },
                })
                    .then((r) => (r.ok ? r.json() : null))
                    .then((counts) => {
                        row.dataset.read = "1";
                        row.querySelector("[data-unread-dot]")?.remove();
                        if (window.applyEnquiryCounts) window.applyEnquiryCounts(counts);
                    })
                    .catch(() => {});
            };

            document.querySelectorAll("[data-view-enquiry]").forEach((button) => {
                button.addEventListener("click", () => {
                    const row = button.closest("[data-enquiry]");
                    if (!row) return;
                    const data = JSON.parse(row.dataset.enquiry);
                    openModal(data);
                    markRead(row, data);
                });
            });

            modal.querySelectorAll("[data-modal-close]").forEach((el) => el.addEventListener("click", closeModal));
            document.addEventListener("keydown", (event) => {
                if (event.key === "Escape" && !modal.classList.contains("hidden")) closeModal();
            });
        }
    })();
</script>
