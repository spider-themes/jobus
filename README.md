# Jobus – Job Board, Recruitment & Hiring Platform

[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPLv2-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![Version](https://img.shields.io/badge/Version-1.4.0-orange.svg)](https://github.com/spider-themes/jobus)

**Jobus** is a modern, lightweight, and powerful WordPress plugin designed to transform your website into a fully functional **Job Board**, **Recruitment Portal**, or **Hiring Platform**.

Whether you are running a niche job board, a company career page, or a large-scale recruitment site, Jobus provides everything you need to manage jobs, employers, and candidates efficiently—**without writing a single line of code**.

---

## 🚀 Features

### For Employers & Companies

- **Unlimited Job Postings** – Create and manage as many job listings as you need
- **Company Profiles** – Dedicated pages for companies with logos, descriptions, and active jobs
- **Job Specifications** – Add detailed requirements (Salary, Experience, Job Type, etc.)
- **Location-Based Listings** – Assign locations to jobs for easy filtering
- **Employer Dashboard** – A frontend dashboard to manage listings and profile details

### For Candidates & Job Seekers

- **Candidate Profiles** – Users can create professional profiles/resumes
- **Easy Application** – Apply to jobs directly via a built-in form
- **Candidate Dashboard** – Manage applications and profile settings
- **Job Search & Filters** – Find jobs by keyword, location, and category

### Powerful Management Tools

- **Frontend Dashboards** – Separate dashboards for Employers and Candidates
- **Application Tracking** – View and manage job applications from the backend
- **Custom User Roles** – Automatically assigns Employer and Candidate roles
- **Social Sharing** – Built-in social share buttons for job posts
- **Featured Jobs** – Highlight premium listings to boost visibility

### Design & Customization

- **Pre-made Templates** – Ready-to-use templates for Job Lists, Single Jobs, and Company pages
- **Elementor Widgets** – Drag-and-drop widgets for Job Listings, Search Forms, Categories, and more
- **Gutenberg Blocks** – Native WordPress block support for modern editing
- **Customizable Appearance** – Control colors, layouts, and styles via settings

---

## 🛠️ Requirements

| Requirement  | Version |
| ------------ | ------- |
| WordPress    | 6.0+    |
| PHP          | 7.4+    |
| Tested up to | 6.8     |

---

## 📦 Installation

### From WordPress Dashboard

1. Go to your WordPress Dashboard → **Plugins** → **Add New**
2. Search for "**Jobus**"
3. Click **Install Now** and then **Activate**
4. Navigate to the **Jobus** menu in the sidebar to configure settings

### Manual Installation

1. Download the plugin ZIP file
2. Go to **Plugins** → **Add New** → **Upload Plugin**
3. Upload the ZIP file and click **Install Now**
4. Activate the plugin

### From GitHub

```bash
cd wp-content/plugins/
git clone https://github.com/spider-themes/jobus.git
cd jobus
composer install
npm install
npm run build
```

---

## 🏗️ Project Structure

```
jobus/
├── Admin/                  # Admin dashboard classes and settings
│   ├── cpt/               # Custom Post Types (Job, Candidate, Company)
│   ├── csf/               # CodeStar Framework configurations
│   └── templates/         # Admin template files
├── includes/              # Core functionality
│   ├── Classes/           # PHP classes
│   ├── Elementor/         # Elementor widgets
│   └── Frontend/          # Frontend classes and shortcodes
├── templates/             # Frontend template files
│   ├── archive-*.php      # Archive templates
│   ├── single-*.php       # Single post templates
│   ├── dashboard/         # Dashboard templates
│   └── loop/              # Loop templates
├── assets/                # Static assets
│   ├── js/               # JavaScript files
│   └── images/           # Image assets
├── build/                 # Generated assets and block builds
│   └── css/              # Generated frontend/admin CSS
├── src/                   # Gutenberg block source files
├── assets/                # Static assets and SCSS sources
│   ├── scss/             # Tracked SCSS source files
├── languages/             # Translation files
└── vendor/                # Composer dependencies
```

---

## 🔧 Development

### Prerequisites

- Node.js (LTS version recommended)
- Composer
- WordPress local development environment

### Setup

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### Available Scripts

| Command         | Description                               |
| --------------- | ----------------------------------------- |
| `npm run start` | Start development mode with hot reloading |
| `npm run build` | Build Gutenberg blocks and generated CSS  |

### Building Blocks

```bash
# Development mode
npm run start

# Production build
npm run build
```

### SCSS Compilation

```bash
npm run sass
```

---

## 📝 Shortcodes

### Dashboard Shortcode

```php
[jobus_dashboard]
```

Displays the unified dashboard for both Candidates and Employers.

### Additional shortcodes are available for:

- Job listings
- Job search forms
- Category filters
- Company listings
- Candidate listings

---

## 🎨 Elementor Widgets

Jobus includes the following Elementor widgets:

- **Job Listings** – Display job posts with various layouts
- **Job Search** – Search form with filters
- **Job Categories** – Category grid/list display
- **Job Tabs** – Tabbed job listings
- **Company Directory** – Company listings
- **Candidate Directory** – Candidate profiles
- **Filter Widgets** – Various filtering options

---

## 🔌 Hooks & Filters

Jobus provides various hooks and filters for developers to extend functionality:

### Actions

```php
// Fired after plugin loads
do_action( 'jobus_fs_loaded' );
```

### Filters

Refer to `includes/filters.php` for available filters.

---

## 🌐 Theme Compatibility

Jobus is designed to work seamlessly with any standard WordPress theme. Tested and optimized for:

- Astra
- Kadence
- Avada
- OceanWP
- GeneratePress
- Docy
- Twenty Twenty-Five
- And more...

---

## 🔥 Pro Features

Unlock advanced functionality with **[Jobus Pro](https://jobus.spider-themes.net/)**:

- Advanced Frontend Dashboards
- Application Tracking System (ATS)
- Advanced Filters (Salary, Job Type, Experience)
- Job Alerts & Bookmarks
- Custom Email Notifications
- Custom Application Forms
- Analytics & Reporting
- Premium Support

---

## 📚 Documentation

For detailed documentation, visit our [Help Desk](https://helpdesk.spider-themes.net/docs/jobi-wordpress-theme/).

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes following [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Coding Standards

- Follow [WordPress PHP Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/)
- Follow [WordPress JavaScript Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/javascript/)
- Follow [WordPress CSS Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/css/)

---

## 📄 Changelog

### 1.4.0 (13 Dec 2025)

- **New:** Added candidate pagination style and job status styling
- **New:** Allow job applications without login (configurable from settings)
- **New:** Applications page added to the Candidate Dashboard
- **New:** Dashboard customization options added to the Settings page
- **New:** Default company logo option added for missing logos
- **Fixed:** Job list delete button issue resolved
- **Fixed:** Candidate and employer user role issues fixed
- **Tweaked:** Dashboard layout spacing and button padding refined

[View Full Changelog](CHANGELOG.md)

---

## 📞 Support

- **Free Support:** [WordPress Support Forum](https://wordpress.org/support/plugin/jobus/)
- **Pro Support:** [Help Desk](https://helpdesk.spider-themes.net/docs/jobi-wordpress-theme/)
- **Website:** [jobus.spider-themes.net](https://jobus.spider-themes.net/)
- **Demo:** [jobus.spider-themes.net/demos](https://jobus.spider-themes.net/demos)

---

## 📜 License

This plugin is licensed under the [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html).

---

## 👥 Contributors

- [Spider Themes](https://spider-themes.net/)
- [mdjwel](https://github.com/mdjwel)
- [arifrahman1](https://github.com/arifrahman1)
- [delweratjk](https://github.com/delweratjk)
- [alimran01](https://github.com/alimran01)

---

<p align="center">
  Made with ❤️ by <a href="https://spider-themes.net/">Spider Themes</a>
</p>
