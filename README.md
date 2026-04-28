# Jobus – Job Board, Recruitment & Hiring Platform

[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPLv2-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![Version](https://img.shields.io/badge/Version-1.9.0-orange.svg)](https://github.com/spider-themes/jobus)

**Jobus** is a modern, lightweight, and powerful WordPress plugin designed to transform your website into a fully functional **Job Board**, **Recruitment Portal**, or **Hiring Platform**.

Whether you are running a niche job board, a company career page, or a large-scale recruitment site, Jobus provides everything you need to manage jobs, employers, and candidates efficiently—**without writing a single line of code**.

---

## 🚀 Features

### For Employers & Companies

- **Unlimited Job Postings** – Create and manage as many job listings as you need
- **Company Profiles** – Dedicated pages for companies with logos, descriptions, and active jobs
- **Verified Badges** – Highlight trusted companies with verified badges
- **Job Specifications** – Add detailed requirements (Salary, Experience, Job Type, etc.)
- **Location-Based Listings** – Assign locations to jobs for easy filtering
- **External Applications** – Redirect candidates to external websites for job applications
- **Employer Dashboard** – A frontend dashboard to manage listings and profile details

### For Candidates & Job Seekers

- **Candidate Profiles** – Users can create professional profiles/resumes
- **Easy Application** – Apply to jobs directly via a built-in form, or apply as a guest without an account
- **Candidate Dashboard** – Manage applications and profile settings
- **Job Search & Filters** – Fast, accurate, and smooth job filtering by keyword, location, and category
- **Radius Search** – Find jobs based on your nearby location using geolocation

### Powerful Management Tools

- **Frontend Dashboards** – Separate dashboards for Employers and Candidates
- **Social Login** – Easy login and signup using Google, Facebook, and LinkedIn
- **Messaging System** – Built-in messaging system for communication between employers and candidates
- **Application Tracking** – View and manage job applications with easy-to-read applicant details from the backend
- **Custom User Roles** – Automatically assigns Employer and Candidate roles
- **Social Sharing** – Built-in social share buttons for job posts
- **Featured Jobs** – Highlight premium listings to boost visibility
- **Google Jobs Integration** – Built-in Schema markup for improved visibility on Google Jobs search
- **Demo Importer** – One-click demo setup with sample jobs to get started instantly

### Design & Customization

- **Pre-made Templates** – Ready-to-use templates for Job Lists, Single Jobs, and Company pages
- **Elementor Widgets** – Drag-and-drop widgets for Job Listings, Search Forms, Categories, and more
- **Gutenberg Blocks** – Native WordPress block support for modern editing
- **Customizable Appearance** – Control colors, layouts, and styles via settings
- **Modern Design & Layouts** – Enjoy improved sidebars and a consistent, modern design across Candidate, Job, and Company pages

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

### 1.9.0 (24 April 2026)

- **New:** Easy login and signup using Google, Facebook, and LinkedIn
- **New:** Find jobs based on your nearby location
- **New:** One-click demo setup with sample jobs
- **New:** Apply for jobs without creating an account (Sign In / Register / Guest options)
- **New:** Verified badge added for trusted companies
- **New:** Option to redirect users to external websites for job applications
- **New:** Improved visibility on Google Jobs search
- **New:** Simple and consistent messaging system
- **Enhanced:** Improved sidebar design for Candidate, Job, and Company pages
- **Enhanced:** Faster and smoother job filtering experience
- **Enhanced:** More consistent design across all pages
- **Enhanced:** Easier-to-read applicant details in admin panel
- **Enhanced:** Faster and more accurate search results
- **Fixed:** Fixed issues with registration and job application process
- **Fixed:** Fixed job application popup design and functionality
- **Fixed:** Fixed sidebar and loading design saving issue
- **Fixed:** Fixed popup search results for jobs, candidates, and companies

[View Full Changelog](changelog.txt)

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
