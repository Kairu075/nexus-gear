# Fix Hamburger Menu Visibility & Functionality - Progress Tracker

## Current Status
✅ Plan approved by user  
✅ TODO.md created  

## Remaining Steps (to be checked off as completed):

### 1. Update css/main.css (Hamburger + Auth buttons visibility) ✅
- [x] Wrap `.hamburger` styles in `@media (max-width:900px)`
- [x] Add `#authBtns { display: none; }` in same media query  
- [x] Verify `.mobile-nav` hidden by default

**Next:** Step 3 - Clean up index.php

### 2. Add mobile-nav drawer to includes/header.php ✅
- [x] Insert complete `#mobileNav` HTML after `</nav>`
- [x] Include nav links, mobile search, auth/user sections

### 3. Clean up index.php
- [ ] Remove duplicate `<header>` block  
- [ ] Remove redundant inline `<style>` media queries
- [ ] Add `<?php include 'includes/header.php'; ?>`

### 4. Testing & Verification
- [ ] Test desktop: hamburger hidden, auth buttons visible
- [ ] Test mobile: hamburger visible, drawer toggles, auth hidden
- [ ] Verify across pages (shop.php, profile.php, etc.)

---

**Next:** Implement Step 1 (css/main.css updates)
