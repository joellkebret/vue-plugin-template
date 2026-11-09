# Vue WordPress Plugin Template

A starter template for building WordPress plugins with Vue.js and Vuetify. Provides a basic admin interface and examples of communicating between a Vue frontend and a PHP backend through WordPress AJAX.

Originally developed during my first software development internship to simplify the setup of WordPress admin interfaces.

## Features

- WordPress admin page with a Vue 2 and Vuetify 2 interface
- Sample form with client-side validation
- AJAX communication between Vue and PHP
- WordPress nonce verification and server-side input validation
- Loading indicators and response handling
- WooCommerce dependency checking
- Example WordPress database connectivity check

## Tech Stack

- **Frontend:** Vue 2, Vuetify 2, JavaScript, CSS
- **Backend:** PHP, WordPress AJAX API
- **Platform:** WordPress, WooCommerce

## Project Structure

```text
vue-plugin-template/
├── hss-vue-template.php
└── includes/
    ├── css/
    │   └── admin-style.css
    └── js/
        └── vue-app.js
```

## Setup

1. Download or clone the repository.
2. Copy the project folder into `wp-content/plugins/` in an existing WordPress installation.
3. Ensure WooCommerce is installed and activated.
4. Activate the plugin from the WordPress Plugins page.
5. Open the **HSS Vue** page in the WordPress admin menu.

The template loads Vue and Vuetify through a CDN, so no separate frontend build step is required.

## How It Works

The PHP plugin registers a WordPress admin page and loads the Vue application along with its styles and dependencies.

The sample interface contains a form that validates user input before submitting an AJAX request to the WordPress backend.

The PHP handler verifies the request nonce, sanitizes and validates the input, checks database connectivity, and returns a JSON response to the frontend.

## Notes

This repository is an early development template rather than a production-ready plugin. It uses Vue 2 and Vuetify 2 and is preserved as an example of integrating Vue into WordPress.
