# JoomCCK Project Context

## Project Overview

JoomCCK is a Content Construction Kit (CCK) for Joomla CMS that provides advanced capabilities for building websites with custom content types. It allows users to create complex web applications including:

- File Downloads
- Galleries
- Blogs
- Forums
- Real Estate and Auto Markets
- Job Boards
- E-Commerce Product Catalogs
- Support Desks

The project is designed for Joomla 4.2+ and requires PHP 8.1+.

## Project Structure

```
JoomCCK/
├── administrator/com_joomcck         # Admin components
├── components/              # Frontend components
│   └── com_joomcck/         # Main JoomCCK component
├── libraries/               # Core libraries and frameworks
│   ├── mint/                # Core MVC framework
├── modules/                 # Joomla modules
│   ├── mod_joomcck_category/
│   ├── mod_joomcck_filters/
│   ├── mod_joomcck_records/
│   ├── mod_joomcck_submitbutton/
│   └── mod_joomcck_tagcloud/
├── plugins/                 # Joomla plugins
│   ├── content/joomcck
│   ├── content/glossary
│   ├── system/
│   └── finder/
└── templates/               # Template overrides
```

## Key Components

### Main Component (com_joomcck)
- Entry point: `components/com_joomcck/joomcck.php`
- Admin entry point: `administrator/components/com_joomcck/joomcck.php`
- Uses a custom MVC framework based on "mint"
- Dependencies managed via Composer in `components/com_joomcck/libraries/`

### Core Libraries
- **Mint Framework**: Custom MVC framework located in `libraries/mint/`
- **Composer Dependencies**: 
  - `gumlet/php-image-resize`: Image resizing library
  - `flowjs/flow-php-server`: File upload handling

### Modules
Several frontend modules provide specific functionality:
- Records display
- Category navigation
- Filter interfaces
- Submission buttons
- Tag clouds

### Plugins
Content and system plugins that integrate JoomCCK with Joomla core functionality:
- Content integration plugin
- System plugin for core functionality
- Finder plugin for search integration

## Development Environment

### Requirements
- PHP 8.1+
- Joomla 4.2+
- Bootstrap 5+

### Dependencies
Managed through Composer in the main component:
```
{
    "require": {
        "php": ">=8.1",
        "gumlet/php-image-resize": "2.1.*",
        "flowjs/flow-php-server": "1.*"
    }
}
```

## Building and Packaging

### Build Process
The project uses Apache Ant for building distribution packages:
- Build script: `buildextended.xml`
- Version: 5.15.0 (as of the build file)
- Creates ZIP package for Joomla installation

### Installation Package
- Main package file: `pkg_joomcck.xml`
- Installation script: `pkg_joomcck.script.php`
- Update server: `update.xml`

## Development Conventions

### Architecture
- Custom MVC pattern based on Mint framework
- PSR-4 autoloading for namespaced classes
- Component-based architecture following Joomla conventions
- Separation of frontend (site) and backend (administrator) code

### Code Organization
- Controllers, Models, Views in respective directories
- Custom fields implemented as separate components
- Template overrides for frontend presentation
- Language files for internationalization

### Testing and Quality
- No explicit testing framework identified in the core files
- Relies on Joomla's extension installation process for validation
- Manual testing through Joomla admin interface

## Deployment

### Installation
1. Install via Joomla Extension Manager
2. Package automatically enables required plugins
3. Configuration available in Joomla admin panel

### Updates
- Update server configured in `update.xml`
- Current version: 4.0.6
- Supports Joomla 4 and 5 platforms

## Important Notes

### Joomla Compatibility
- Requires Joomla 4.2 or higher
- Not compatible with Joomla 3.x
- Tested with Joomla 5.x compatibility

### PHP Requirements
- Minimum PHP 8.1
- Uses modern PHP features and type declarations

### Security
- Follows Joomla security practices
- Input validation through Joomla framework
- Access control based on Joomla user permissions