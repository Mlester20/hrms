document.getElementById("SearchInput").addEventListener("keyup", filterTable);
function filterTable() {
    const input = document.getElementById("SearchInput").value.toLowerCase();
    const table = document.getElementById("reservationsTable");
    const rows = table.getElementsByTagName("tr");

    for (let i = 1; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName("td");
        let found = false;

        for (let j = 0; j < cells.length; j++) {
            if (cells[j].textContent.toLowerCase().includes(input)) {
                found = true;
                break;
            }
        }

        rows[i].style.display = found ? "" : "none";
    }
}