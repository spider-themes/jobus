# Jobus – Job Board, Recruitment & Hiring Platform

[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-blue.svg)](https://wordpress.org/)
[![Tested up to](https://img.shields.io/badge/Tested%20up%20to-7.1%2B-brightgreen.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-purple.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPLv2-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![Version](https://img.shields.io/badge/Version-1.15.0-orange.svg)](https://jobus.spiderthemes.net/)

**Jobus** is the ultimate WordPress Job Board plugin. Create a recruitment website with unlimited job listings, candidate profiles, and company pages.

Whether you need a clean **corporate careers page** to showcase your company's open positions, a niche **recruitment directory**, or a full-scale **job marketplace**, Jobus delivers an effortless, modern hiring solution out of the box. It connects employers and job seekers with intuitive dashboards, rich job specifications, radius distance filtering, and direct application workflows—**without writing a single line of code**.

Built with usability, theme compatibility, and SEO in mind, Jobus integrates smoothly with standard WordPress themes and offers native **Elementor** widgets and **Gutenberg** blocks, allowing you to design clean job layouts with drag-and-drop ease.

### Why Choose Jobus?

* **Standard Theme Compatibility:** Works seamlessly with standard WordPress themes (Astra, Kadence, GeneratePress, Hello Elementor, Divi, OceanWP) with zero theme lock-in.
* **Zero Coding Required:** Set up a complete job board in minutes using the built-in Setup Wizard.
* **Elementor & Gutenberg Ready:** Includes custom Elementor widgets and native Gutenberg blocks for Jobs, Search, Categories, and Tabs.
* **Modern & Responsive:** Pixel-perfect layout across desktop, tablet, and mobile devices.
* **Fast & Lightweight:** Built for speed with optimized database queries and clean template architecture.
* **SEO Optimized:** Built-in Schema.org structured data (`JobPosting`) to help your jobs rank on Google for Jobs search results.

---

## 🚀 Features

### For Employers & Companies

- **Unlimited Job Postings** – Create, manage, and categorize unlimited job listings from the WordPress admin
- **Job Specifications** – Add rich specifications including salary ranges, job type (Full-Time, Part-Time, Remote), experience level, and deadlines
- **Location-Based Listings** – Assign countries, cities, or custom locations to jobs for smooth filtering
- **External Applications** – Redirect candidates directly to company career pages, LinkedIn, or external ATS forms
- **Featured & Urgent Badges** – Highlight priority job posts to maximize applicant reach
- **Monetization & Paid Packages** *(Pro)* – Sell job posting packages, featured listing badges, and tiered listings with custom job limits and duration via WooCommerce
- **Company Profiles & Directory** *(Pro)* – Dedicated public company pages with logos and active listings
- **Frontend Job Submission** *(Pro)* – Allow employers to submit, edit, and manage jobs directly from the frontend dashboard

### For Candidates & Job Seekers

- **Direct Modal Applications** – Apply to jobs directly via an interactive, lightweight AJAX application modal
- **Guest Applications** – Allow applicants to submit resumes and contact details with or without registering for an account
- **Job Search & Filters** – Fast, smooth job filtering by keyword, location, category, job type, and salary
- **Radius & Geolocation Search** – Help candidates locate nearby job openings using distance-based location coordinates
- **Candidate Profiles & Resumes** *(Pro)* – Public candidate resume profiles with skills, experience, and attachments
- **Job Alerts & Bookmarks** *(Pro)* – Save jobs to bookmarks and receive automated email alerts for new matching positions

### Powerful Management Tools

- **Setup Wizard** – Guided 4-step setup wizard (**Jobs → Setup Wizard**) to configure global currency and default settings
- **Auto-Created Core Pages** – Automatically generates required pages for Job Archive (`/jobus-job-archive/`) and Dashboard
- **Google Jobs Integration** – Built-in JSON-LD structured data (Schema.org `JobPosting`) for organic Google for Jobs visibility
- **Social Sharing** – Built-in social share buttons for job posts (LinkedIn, Twitter, Facebook, Email)
- **Custom User Roles** – Automatically registers and assigns dedicated Employer and Candidate roles
- **Social Login** – Optional one-click login and signup using Google, Facebook, and LinkedIn
- **Demo Importer** – One-click demo setup with sample jobs to test functionality instantly
- **Frontend Dashboards** – Unified user portal for employers and candidates
- **WooCommerce Monetization** *(Pro)* – Charge employers for job listings using custom WooCommerce Job Package products with custom limits and duration
- **Application Tracking System (ATS)** *(Pro)* – Move candidates through hiring stages (Applied, Screening, Interview, Hired, Rejected)
- **Direct In-App Messaging** *(Pro)* – Secure communication channel between employers and applicants

### Design & Customization

- **Pre-made Templates** – Ready-to-use templates for Job Lists, Single Job details, and taxonomy pages
- **Elementor Widgets** – Dedicated drag-and-drop widgets for Job Listings, Search Forms, Categories, and Job Tabs
- **Gutenberg Blocks** – Native WordPress block support including Job Archive and Category Tabs blocks
- **Customizable Appearance** – Control brand colors, typography accents, and button borders via **Jobs → Settings → Appearance**
- **Modern Design & Layouts** – Enjoy consistent sidebars and modern design across Job, Company, and Candidate pages

---

## 🛠️ Requirements

| Requirement  | Version        |
| ------------ | -------------- |
| WordPress    | 6.0+           |
| Tested up to | WordPress 7.1+ |
| PHP          | 8.0+           |

---

## 📦 Installation

### From WordPress Dashboard

1. Go to your WordPress Dashboard → **Plugins** → **Add New**
2. Search for "**Jobus**"
3. Click **Install Now** and then **Activate**
4. Run the guided **Setup Wizard** under **Jobs → Setup Wizard** to configure currency and defaults
5. **Crucial Step:** Go to **Settings → Permalinks** and click **Save Changes** to flush rewrite rules and prevent 404 errors on job listings
6. Add the Job Archive and Dashboard pages to your navigation menu under **Appearance → Menus**

### Manual Installation

1. Download the `jobus.zip` file
2. Go to **Plugins** → **Add New** → **Upload Plugin**
3. Upload the ZIP file and click **Install Now**
4. Activate the plugin
5. Resave permalinks under **Settings → Permalinks**

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
├── Admin/                 # Admin dashboard classes and settings
│   ├── cpt/               # Custom Post Types (Job, Candidate, Company, Application)
│   ├── csf/               # CodeStar Framework configurations
│   └── templates/         # Admin template files
├── includes/              # Core functionality
│   ├── Classes/           # PHP core classes and business logic
│   ├── Elementor/         # Elementor widgets and controls
│   └── Frontend/          # Frontend classes, templates, and shortcodes
├── templates/             # Frontend template files
│   ├── archive-*.php      # Archive templates
│   ├── single-*.php       # Single post templates
│   ├── dashboard/         # Dashboard templates
│   └── loop/              # Card loop templates
├── assets/                # Static assets
│   ├── css/               # Compiled stylesheets
│   ├── js/                # JavaScript logic
│   └── images/            # Static image assets
├── build/                 # Generated assets and block builds
├── src/                   # Gutenberg block source files (React/ESNext)
├── languages/             # Translation files (.pot and .po/.mo)
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

Jobus provides clean, lightweight WordPress shortcodes:

### Job Archive Shortcode

```php
[jobus_job_archive]
```
Displays the complete, filterable job listings directory with pagination. Supports layout attribute `job_layout="grid"` or `"list"`.

### Dashboard Shortcode

```php
[jobus_dashboard]
```
Displays the unified portal for Candidates and Employers (login form for guests, account overview for logged-in users).

### Authentication Shortcodes

```php
[jobus_login_form]
```
Displays a clean frontend user authentication and login card.

```php
[jobus_logout_form]
```
Displays a frontend logout button with redirect handling.

### Pro Shortcodes

```php
[jobus_company_archive]
```
Displays the searchable directory of registered companies (*Requires Jobus Pro*).

```php
[jobus_candidate_archive]
```
Displays the searchable directory of candidate profiles and resumes (*Requires Jobus Pro*).

*Note: Standalone search bars, category grids, and tabbed listings are provided as Elementor Widgets and Gutenberg Blocks.*

---

## 🎨 Elementor Widgets

Jobus includes dedicated widgets within the **Jobus** Elementor category:

- **Job Listings** – Display job posts in modern grid or list layouts with custom query controls
- **Job Search** – Interactive search form with keyword, location, and category dropdowns
- **Job Categories** – Category grid/list display highlighting active job counts
- **Job Tabs** – Tabbed job listings by status (featured, recent, urgent)
- **Company Directory** – Showcase hiring partners and company logos *(Pro)*

---

## 🔌 Hooks & Filters

Jobus provides hooks and filters for developers to extend functionality:

### Actions

```php
// Fired on WordPress init after Jobus core initialization
add_action( 'jobus_init', function() {
    // Custom initialization logic
} );

// Fired after Freemius SDK finishes loading
add_action( 'jobus_fs_loaded', function() {
    // Freemius-dependent custom logic
} );
```

### Filters

Filter names and template hooks are defined throughout `includes/` and `templates/`. You can override frontend templates in your child theme by placing files under `your-theme/jobus/`.

---

## 🌐 Theme Compatibility

Jobus is designed to work seamlessly with any standard WordPress theme with zero theme lock-in. Tested and optimized for:

- Astra
- Kadence
- GeneratePress
- Hello Elementor
- Divi
- OceanWP
- Avada
- Twenty Twenty-Five
- And more...

---

## 🔥 Pro Features

Unlock the complete monetization, automation, and recruitment engine with **[Jobus Pro](https://jobus.spiderthemes.net/)**:

- **Frontend Job Submission** – Allow employers to submit, edit, and manage jobs directly from the frontend
- **Monetization & Paid Packages (WooCommerce)** – Sell job posting packages, featured listing badges, and tiered posting packages via WooCommerce. Set custom job limits, listing duration (days), and accept payments through any gateway (Stripe, PayPal, etc.)
- **Application Tracking System (ATS)** – Structured hiring stages: *Applied → Screening → Interview → Hired / Rejected*
- **Smart Candidate Matching** – Automatically match applicants to jobs by distance, availability, and skills with email alerts
- **Company Profiles & Directory** – Public company profiles, active job listings, and verified trust badges
- **Candidate Profiles & Resumes** – Professional candidate resumes and searchable candidate directory
- **Job Alerts & Bookmarks** – Email notifications for new matching jobs and saved job bookmarks
- **In-App Direct Messaging** – Real-time messaging between employers and applicants
- **Recruitment Analytics** – Track job views, clicks, and application conversion rates with visual charts
- **Premium Priority Support** – Direct help from our dedicated support engineering team

---

## 📚 Documentation

For detailed guides, tutorials, and setup instructions, visit our [Help Desk](https://helpdesk.spiderthemes.net/docs/jobus-wordpress-plugin/).

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

### 1.15.0 (11 November 2026)

- **New:** Added a Candidate Skills filter widget option for candidate archive sidebars.
- **New:** Redesigned the Demo Sandbox login picker with Employer/Candidate role cards.
- **Improved:** Unified taxonomy archive and sidebar filtering for jobs, candidates, and companies.
- **Fixed:** Prevented WordPress canonical redirects from breaking filtered archive requests.
- **Fixed:** Resolved the Continue as Guest button and application success message display.

### 1.14.0 (3 November 2026)

- **New:** Added configurable Candidate Matching settings with per-criterion Required/Preferred mode and ranking weight controls.
- **New:** Added Job Date, start/end time, and candidate available days/hours fields for Candidate Matching.
- **New:** Added Specific Available Dates field for candidate availability.
- **Improved:** Candidate Max Travel Distance calculation and preset support.

### 1.13.0 (October 21, 2026)

- **New:** Added padding controls for Job, Company, and Candidate archive blocks.
- **New:** Added Candidate Matching form fields and Pro dashboard integration.
- **Improved:** Enhanced premium feature detection with fallback to version constant.

### 1.12.0 (October 13, 2026)

- **New:** Added the Job Category Tabs block.
- **New:** Added an option to show or hide the employer email address.
- **Updated:** Updated Freemius SDK to the latest version.

### 1.11.0 (October 5, 2026)

- **New:** Added page section padding options for Candidate, Job, and Company details pages.
- **New:** Added Job Expiration settings, Application Deadline field, and extensibility hooks for automatic job expiration.
- **New:** Added Docs Support Chat to Jobus admin pages for easier access to documentation and support.
- **Enhanced:** Prefixed Candidate Profile Layout 2 CSS classes with the `jbs-` namespace to prevent theme and plugin class conflicts.
- **Enhanced:** Added automatic daily job expiration based on the Application Deadline, reducing manual job management.
- **Fixed:** Resolved the Job sidebar dark mode issue and filter search results issue.
- **Fixed:** Resolved the Testimonials slider design breaking issue.
- **Fixed:** Resolved dark mode issues.
- **Fixed:** Resolved a WordPress critical error.

### 1.10.1 (June 19, 2026)

- **Enhanced:** Improved filter performance with optimized meta key usage count caching.
- **Enhanced:** Reduced database queries in filter calculations for better archive page performance.
- **Fixed:** Resolved filter issues.

### 1.10.0 (June 1, 2026)

- **New:** Application Details page added to Employer Dashboard with comprehensive candidate information and application status.
- **Enhanced:** Improved avatar display and styling for Candidates and Companies.
- **Enhanced:** Added AJAX loader for job submission in Employer Dashboard.
- **Enhanced:** Improved Saved Candidates page with AJAX search, smart empty state, and better layout.
- **Enhanced:** Improved candidate management experience in Saved Candidates section.
- **Fixed:** Fixed job status handling (Draft, Pending, Publish) based on admin settings.
- **Fixed:** Ensured proper submission behavior according to monetization and job approval rules.

### 1.9.0 (24 April 2026)

- **New:** Easy login and signup using Google, Facebook, and LinkedIn.
- **New:** Find jobs based on your nearby location.
- **New:** One-click demo setup with sample jobs.
- **New:** Apply for jobs without creating an account (Sign In / Register / Guest options).
- **New:** Verified badge added for trusted companies.
- **New:** Option to redirect users to external websites for job applications.
- **New:** Improved visibility on Google Jobs search.
- **New:** Simple and consistent messaging system.
- **Enhanced:** Improved sidebar design for Candidate, Job, and Company pages.
- **Enhanced:** Faster and smoother job filtering experience.
- **Enhanced:** More consistent design across all pages.
- **Enhanced:** Easier-to-read applicant details in admin panel.
- **Enhanced:** Faster and more accurate search results.
- **Fixed:** Fixed issues with registration and job application process.
- **Fixed:** Fixed job application popup design and functionality.
- **Fixed:** Fixed sidebar and loading design saving issue.
- **Fixed:** Fixed popup search results for jobs, candidates, and companies.

[View Full Changelog](changelog.txt)

---

## 📞 Support

- **Free Support:** [WordPress Support Forum](https://wordpress.org/support/plugin/jobus/)
- **Pro Support:** [Help Desk](https://helpdesk.spiderthemes.net/docs/jobus-wordpress-plugin/)
- **Website:** [jobus.spiderthemes.net](https://jobus.spiderthemes.net/)
- **Demo:** [jobus.spiderthemes.net/job-list-width/](https://jobus.spiderthemes.net/job-list-width/)

---

## 📜 License

This plugin is licensed under the [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html).

---

## 👥 Contributors

- [Spider Themes](https://spiderthemes.net/)
- [mdjwel](https://github.com/mdjwel)
- [arifrahman1](https://github.com/arifrahman1)
- [delweratjk](https://github.com/delweratjk)
- [alimran01](https://github.com/alimran01)

---

<p align="center">
  Made with ❤️ by <a href="https://spiderthemes.net/">Spider Themes</a>
</p>
