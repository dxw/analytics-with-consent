# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Fixed

- When ACF is not available the plugin does not produce an error

### Changed

- GitHub actions run on PHP 7 and 8
- Kahlan updated to v6 to add PHP 8 support

## [1.5.5] - 2024-12-12

### Changed

- Check variable before trim to prevent PHP warning

## [1.5.4] - 2024-11-28

### Added

- Settings link appears in plugins page

## [1.5.3] - 2024-11-25

### Added

- PR template
- Validation to options form

### Changed

- FIX typo in marketing cookie text
- Analytics options are trimmed before adding to HTML output

## [1.5.2] - 2024-07-08

### Changed

- Upgrade `dxw/iguana` version to `v1.3.2`

## [1.5.1] - 2024-06-20

### Changed

- Added Hotjar option

## [1.5.0] - 2024-03-06

### Changed

- All consent types are denied by default

## [1.4.0] - 2022-11-03

### Changed

- `wp-postpass_*` cookies (used to access WordPress password-protected pages) are marked as necessary

## [1.3.0] - 2022-10-11

### Added

- Option to include marketing scripts via GTM

## [1.2.0] - 2022-09-13

- Google Tag Manager functionality

## [1.1.0] - 2022-05-05

### Added

- Google Analytics 4 functionality

### Removed

- Support for PHP versions below 7.4

## [1.0.0] - 2021-05-25

### Added

- `awc_civic_cookie_control_config` filter, to allow default config to be added to or over-ridden

## [0.2.2] - 2021-04-12

### Added

- Fix to remove `rem` font size on headings - some sites the headings were inheriting a much smaller size
- Fix to overlapping header over pixel-based size custom appearance checkbox

## [0.2.1] - 2021-03-03

### Added

- Fix to address reflow issues at 400% zoom level using improved layout styling to fill the page width and a more compact checkbox instead of slider control

## [0.2.0] - 2021-02-26

## Added

- Accessible default styles for focus states on buttons & links in the cookie panel

## [0.1.0] - 2020-11-13

- Initial release
