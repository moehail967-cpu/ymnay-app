
    // main.js → optional (can be empty or used later)
// Example: column highlight on hover (progressive enhancement)

document.addEventListener('DOMContentLoaded', () => {
  const table = document.querySelector('table');
  if (!table) return;

  const headers = table.querySelectorAll('thead th:not(:first-child)');
  const rows = table.querySelectorAll('tbody tr');

  function clearHighlights() {
    headers.forEach(h => h.classList.remove('bg-teal-700/20'));
    rows.forEach(row => {
      row.querySelectorAll('td:not(:first-child)').forEach(td => {
        td.classList.remove('bg-teal-50/60');
      });
    });
  }

  headers.forEach((header, colIndex) => {
    header.addEventListener('mouseenter', () => {
      clearHighlights();
      header.classList.add('bg-teal-700/20');
      rows.forEach(row => {
        const cells = row.querySelectorAll('td:not(:first-child)');
        if (cells[colIndex - 1]) {
          cells[colIndex - 1].classList.add('bg-teal-50/60');
        }
      });
    });

    header.addEventListener('mouseleave', clearHighlights);
  });
});
