// document.addEventListener('DOMContentLoaded', function() {
// // ========================================
// // Popover Logic
// // ========================================
//     const popover = document.getElementById('globalPopover');
//     const closeBtn = document.getElementById('closePopover');
//     const visitBtn = document.getElementById('popoverVisitBtn');
//     const adminBtn = document.getElementById('popoverAdminBtn');
//     let activeBtn = null;
//
//     if (popover) {
//         document.querySelectorAll('.btnAction').forEach(btn => {
//             btn.addEventListener('click', function (e) {
//                 e.stopPropagation();
//
//                 if (activeBtn === btn && !popover.classList.contains('hidden')) {
//                     popover.classList.add('hidden');
//                     activeBtn = null;
//                     return;
//                 }
//
//                 activeBtn = btn;
//
//                 // Update popover links from button data attributes
//                 if (visitBtn) visitBtn.setAttribute('href', btn.getAttribute('data-visit-url') || '#');
//                 if (adminBtn) adminBtn.setAttribute('href', btn.getAttribute('data-admin-url') || '#');
//
//                 const container = btn.closest('.relative');
//                 const containerRect = container.getBoundingClientRect();
//                 const btnRect = btn.getBoundingClientRect();
//
//                 const allRows = Array.from(document.querySelectorAll('#tableBody tr'));
//                 const currentRow = btn.closest('tr');
//                 const rowIndex = allRows.indexOf(currentRow);
//                 const totalRows = allRows.length;
//
//                 popover.classList.remove('hidden');
//                 const popoverHeight = popover.offsetHeight;
//                 popover.classList.add('hidden');
//
//                 const isLastTwo = rowIndex >= totalRows - 2;
//
//                 // Commented out to allow CSS positioning with -bottom-10
//                 // if (isLastTwo) {
//                 //     popover.style.top = (btnRect.top - containerRect.top - popoverHeight - 8) + 'px';
//                 // } else {
//                 //     popover.style.top = (btnRect.bottom - containerRect.top + 8) + 'px';
//                 // }
//
//                 // popover.style.right = '12px'; // Commented out to allow CSS positioning
//                 popover.classList.remove('hidden');
//             });
//         });
//
//         if (closeBtn) {
//             closeBtn.addEventListener('click', () => {
//                 popover.classList.add('hidden');
//                 activeBtn = null;
//             });
//         }
//
//         document.addEventListener('click', (e) => {
//             if (!popover.contains(e.target) && !e.target.closest('.btnAction')) {
//                 popover.classList.add('hidden');
//                 activeBtn = null;
//             }
//         });
//     }
// });
//
// // ========================================
// // Page Load Animations
// // ========================================
// window.addEventListener('load', () => {
//     const animatedElements = document.querySelectorAll('.will-animate');
//     animatedElements.forEach(el => {
//         el.classList.remove('will-animate');
//     });
// });
