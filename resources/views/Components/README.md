# Components Directory

This directory contains reusable modular components for the AI Stock Analytics application.

## Components

- **header.blade.php** - Application header with navigation and user dropdown
- **sidebar.blade.php** - Admin sidebar with navigation menu and role-based access
- **footer.blade.php** - Application footer with copyright information
- **admin-scripts.blade.php** - Common JavaScript functionality for admin pages

## Usage

These components are included in standalone admin pages using `@include()` directives:

```php
@include('Components.header')
@include('Components.sidebar') 
@include('Components.footer')
@include('Components.admin-scripts')
```

## Benefits

- **Standalone Pages**: Pages are independent without global layout inheritance
- **No JavaScript Conflicts**: Avoids global scope pollution from app.js
- **Modular**: Components can be reused across different admin pages
- **Maintainable**: Changes to navigation/layout only need to be made in one place
- **Clean HTML**: No bloated inline CSS/JS from layout.blade.php

## Architecture

Each admin page now follows this structure:
1. Complete HTML document with DOCTYPE
2. Page-specific CSS includes in `<head>`
3. @include directives for reusable components
4. Page content in proper semantic structure
5. Page-specific JavaScript includes before `</body>`

This eliminates the need for complex layout inheritance while maintaining consistent navigation and styling.