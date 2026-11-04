document.addEventListener("DOMContentLoaded", () => {
  
  // 1. DROPDOWN MENU TOGGLE 
  document.addEventListener('click', (e) => {
    if (e.target.closest('.menu-btn')) {
      e.stopPropagation();
      const btn = e.target.closest('.menu-btn');
      const dropdown = btn.nextElementSibling;
      
      // Close others first
      document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (menu !== dropdown) menu.classList.remove('show');
      });
      
      if (dropdown) dropdown.classList.toggle('show');
    } else {
      // Close dropdowns when clicking outside
      document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.classList.remove('show');
      });
    }
  });

  // 2. PINNING/UNPINNING LOGIC
  document.addEventListener('click', (e) => {
    const pinBtn = e.target.closest('.pin-list');
    if (pinBtn) {
      e.preventDefault(); 
      
      const card = pinBtn.closest('.list-card');
      const pinnedContainer = document.getElementById('pinned-container');
      const myListsContainer = document.getElementById('my-lists-container');
      
      const isPinned = card.parentElement === pinnedContainer;
      
      if (isPinned) {
        // Move back to My Lists
        myListsContainer.appendChild(card);
        pinBtn.innerHTML = '<img src="images/pin.svg" alt="Pin" class="icon-sm"> Pin';
      } else {
        // Move to Pinned Section
        pinnedContainer.appendChild(card);
        pinBtn.innerHTML = '<img src="images/unpin.svg" alt="Unpin" class="icon-sm"> Unpin';
      }
      
      const dropdown = pinBtn.closest('.dropdown-menu');
      if (dropdown) dropdown.classList.remove('show');

      updateListVisibility();
    }
  });

  // 3. MODAL LOGIC (OPEN & CLOSE)
  const modal = document.getElementById('edit-modal');
  const closeModalBtn = document.getElementById('close-modal');
  const updateBtn = document.getElementById('btn-update');

  function openModal() {
    modal.classList.add('active');
  }

  function closeModal() {
    modal.classList.remove('active');
  }

  document.addEventListener('click', (e) => {
    if (e.target.closest('.edit-list')) {
      openModal();
    }
  });

  if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
  if (updateBtn) updateBtn.addEventListener('click', closeModal);

  modal.addEventListener('click', (e) => {
    if (e.target === modal) closeModal();
  });

  // 4. EMPTY STATE & SECTION TOGGLE LOGIC
  const myListsContainer = document.getElementById('my-lists-container');
  const pinnedContainer = document.getElementById('pinned-container');
  const pinnedSection = document.getElementById('pinned-section');
  const emptyState = document.getElementById('empty-state');
  const listCountBadge = document.getElementById('list-count-badge');

  function updateListVisibility() {
    if (!myListsContainer || !pinnedContainer || !emptyState || !listCountBadge) return;
    
    const normalListCount = myListsContainer.querySelectorAll('.list-card').length;
    const pinnedListCount = pinnedContainer.querySelectorAll('.list-card').length;
    const totalCount = normalListCount + pinnedListCount;

    listCountBadge.textContent = `${totalCount} List${totalCount !== 1 ? 's' : ''}`;

    if (totalCount === 0) {
      emptyState.classList.remove('hidden');
      myListsContainer.classList.add('hidden');
    } else {
      emptyState.classList.add('hidden');
      myListsContainer.classList.remove('hidden');
    }

    if (pinnedListCount === 0) {
      pinnedSection.style.display = 'none';
    } else {
      pinnedSection.style.display = 'block';
    }
  }

  updateListVisibility();
  window.updateListVisibility = updateListVisibility;

  // 5. EMPTY STATE ICON FOCUS ACTION
  const emptyIcon = document.querySelector('.empty-icon');
  const listNameInput = document.getElementById('list-name'); 

  if (emptyIcon && listNameInput) {
    emptyIcon.addEventListener('click', () => {
      listNameInput.focus();
      listNameInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
  }
});