<?php
/**
 * Copyright (C) 2014-2023 ServMask Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ███████╗███████╗██████╗ ██╗   ██╗███╗   ███╗ █████╗ ███████╗██╗  ██╗
 * ██╔════╝██╔════╝██╔══██╗██║   ██║████╗ ████║██╔══██╗██╔════╝██║ ██╔╝
 * ███████╗█████╗  ██████╔╝██║   ██║██╔████╔██║███████║███████╗█████╔╝
 * ╚════██║██╔══╝  ██╔══██╗╚██╗ ██╔╝██║╚██╔╝██║██╔══██║╚════██║██╔═██╗
 * ███████║███████╗██║  ██║ ╚████╔╝ ██║ ╚═╝ ██║██║  ██║███████║██║  ██╗
 * ╚══════╝╚══════╝╚═╝  ╚═╝  ╚═══╝  ╚═╝     ╚═╝╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Kangaroos cannot jump here' );
}

// ================
// = Plugin Debug =
// ================
define( 'AI1WM_DEBUG', false );

// ==================
// = Plugin Version =
// ==================
define( 'AI1WM_VERSION', '7.83' );

// ===============
// = Plugin Name =
// ===============
define( 'AI1WM_PLUGIN_NAME', 'all-in-one-wp-migration' );

// ================
// = Storage Path =
// ================
define( 'AI1WM_STORAGE_PATH', AI1WM_PATH . DIRECTORY_SEPARATOR . 'storage' );

// ==================
// = Error Log Path =
// ==================
define( 'AI1WM_ERROR_FILE', AI1WM_STORAGE_PATH . DIRECTORY_SEPARATOR . 'error.log' );

// ===============
// = Status Path =
// ===============
define( 'AI1WM_STATUS_FILE', AI1WM_STORAGE_PATH . DIRECTORY_SEPARATOR . 'status.js' );

// ============
// = Lib Path =
// ============
define( 'AI1WM_LIB_PATH', AI1WM_PATH . DIRECTORY_SEPARATOR . 'lib' );

// ===================
// = Controller Path =
// ===================
define( 'AI1WM_CONTROLLER_PATH', AI1WM_LIB_PATH . DIRECTORY_SEPARATOR . 'controller' );

// ==============
// = Model Path =
// ==============
define( 'AI1WM_MODEL_PATH', AI1WM_LIB_PATH . DIRECTORY_SEPARATOR . 'model' );

// ===============
// = Export Path =
// ===============
define( 'AI1WM_EXPORT_PATH', AI1WM_MODEL_PATH . DIRECTORY_SEPARATOR . 'export' );

// ===============
// = Import Path =
// ===============
define( 'AI1WM_IMPORT_PATH', AI1WM_MODEL_PATH . DIRECTORY_SEPARATOR . 'import' );

// =============
// = View Path =
// =============
define( 'AI1WM_TEMPLATES_PATH', AI1WM_LIB_PATH . DIRECTORY_SEPARATOR . 'view' );

// ===================
// = Set Bandar Path =
// ===================
define( 'BANDAR_TEMPLATES_PATH', AI1WM_TEMPLATES_PATH );

// ===============
// = Vendor Path =
// ===============
define( 'AI1WM_VENDOR_PATH', AI1WM_LIB_PATH . DIRECTORY_SEPARATOR . 'vendor' );

// =========================
// = ServMask Feedback URL =
// =========================
define( 'AI1WM_FEEDBACK_URL', 'https://servmask.com/ai1wm/feedback/create' );

// ==============================
// = ServMask Archive Tools URL =
// ==============================
define( 'AI1WM_ARCHIVE_TOOLS_URL', 'https://servmask.com/archive/tools' );

// =========================
// = ServMask Table Prefix =
// =========================
define( 'AI1WM_TABLE_PREFIX', 'SERVMASK_PREFIX_' );

// ========================
// = Archive Backups Name =
// ========================
define( 'AI1WM_BACKUPS_NAME', 'ai1wm-backups' );

// =========================
// = Archive Database Name =
// =========================
define( 'AI1WM_DATABASE_NAME', 'database.sql' );

// ========================
// = Archive Package Name =
// ========================
define( 'AI1WM_PACKAGE_NAME', 'package.json' );

// ==========================
// = Archive Multisite Name =
// ==========================
define( 'AI1WM_MULTISITE_NAME', 'multisite.json' );

// ======================
// = Archive Blogs Name =
// ======================
define( 'AI1WM_BLOGS_NAME', 'blogs.json' );

// =========================
// = Archive Settings Name =
// =========================
define( 'AI1WM_SETTINGS_NAME', 'settings.json' );

// ==========================
// = Archive Multipart Name =
// ==========================
define( 'AI1WM_MULTIPART_NAME', 'multipart.list' );

// =============================
// = Archive Content List Name =
// =============================
define( 'AI1WM_CONTENT_LIST_NAME', 'content.list' );

// ===========================
// = Archive Media List Name =
// ===========================
define( 'AI1WM_MEDIA_LIST_NAME', 'media.list' );

// =============================
// = Archive Plugins List Name =
// =============================
define( 'AI1WM_PLUGINS_LIST_NAME', 'plugins.list' );

// ============================
// = Archive Themes List Name =
// ============================
define( 'AI1WM_THEMES_LIST_NAME', 'themes.list' );

// ============================
// = Archive Tables List Name =
// ============================
define( 'AI1WM_TABLES_LIST_NAME', 'tables.list' );

// =================================
// = Incremental Content List Name =
// =================================
define( 'AI1WM_INCREMENTAL_CONTENT_LIST_NAME', 'incremental.content.list' );

// ===============================
// = Incremental Media List Name =
// ===============================
define( 'AI1WM_INCREMENTAL_MEDIA_LIST_NAME', 'incremental.media.list' );

// =================================
// = Incremental Plugins List Name =
// =================================
define( 'AI1WM_INCREMENTAL_PLUGINS_LIST_NAME', 'incremental.plugins.list' );

// ================================
// = Incremental Themes List Name =
// ================================
define( 'AI1WM_INCREMENTAL_THEMES_LIST_NAME', 'incremental.themes.list' );

// =================================
// = Incremental Backups List Name =
// =================================
define( 'AI1WM_INCREMENTAL_BACKUPS_LIST_NAME', 'incremental.backups.list' );

// =============================
// = Archive Cookies Text Name =
// =============================
define( 'AI1WM_COOKIES_NAME', 'cookies.txt' );

// =================================
// = Archive Must-Use Plugins Name =
// =================================
define( 'AI1WM_MUPLUGINS_NAME', 'mu-plugins' );

// ========================
// = Less Cache Extension =
// ========================
define( 'AI1WM_LESS_CACHE_EXTENSION', '.less.cache' );

// =============================
// = SQLite Database Extension =
// =============================
define( 'AI1WM_SQLITE_DATABASE_EXTENSION', '.sqlite' );

// ============================
// = Elementor CSS Cache Name =
// ============================
define( 'AI1WM_ELEMENTOR_CSS_NAME', 'uploads' . DIRECTORY_SEPARATOR . 'elementor' . DIRECTORY_SEPARATOR . 'css' );

// =========================
// = Themes Functions Name =
// =========================
define( 'AI1WM_THEMES_FUNCTIONS_NAME', 'themes' . DIRECTORY_SEPARATOR . 'functions.php' );

// =============================
// = Endurance Page Cache Name =
// =============================
define( 'AI1WM_ENDURANCE_PAGE_CACHE_NAME', 'endurance-page-cache.php' );

// ===========================
// = Endurance PHP Edge Name =
// ===========================
define( 'AI1WM_ENDURANCE_PHP_EDGE_NAME', 'endurance-php-edge.php' );

// ================================
// = Endurance Browser Cache Name =
// ================================
define( 'AI1WM_ENDURANCE_BROWSER_CACHE_NAME', 'endurance-browser-cache.php' );

// =========================
// = GD System Plugin Name =
// =========================
define( 'AI1WM_GD_SYSTEM_PLUGIN_NAME', 'gd-system-plugin.php' );

// =======================
// = WP Stack Cache Name =
// =======================
define( 'AI1WM_WP_STACK_CACHE_NAME', 'wp-stack-cache.php' );

// ===========================
// = WP.com Site Loader Name =
// ===========================
define( 'AI1WM_WP_COMSH_LOADER_NAME', 'wpcomsh-loader.php' );

// ===========================
// = WP.com Site Helper Name =
// ===========================
define( 'AI1WM_WP_COMSH_HELPER_NAME', 'wpcomsh' );

// ====================================
// = SQLite Database Integration Name =
// ====================================
define( 'AI1WM_SQLITE_DATABASE_INTEGRATION_NAME', 'sqlite-database-integration' );

// =============================
// = SQLite Database Zero Name =
// =============================
define( 'AI1WM_SQLITE_DATABASE_ZERO_NAME', '0-sqlite.php' );

// ================================
// = WP Engine System Plugin Name =
// ================================
define( 'AI1WM_WP_ENGINE_SYSTEM_PLUGIN_NAME', 'mu-plugin.php' );

// ===========================
// = WPE Sign On Plugin Name =
// ===========================
define( 'AI1WM_WPE_SIGN_ON_PLUGIN_NAME', 'wpe-wp-sign-on-plugin.php' );

// ===================================
// = WP Engine Security Auditor Name =
// ===================================
define( 'AI1WM_WP_ENGINE_SECURITY_AUDITOR_NAME', 'wpengine-security-auditor.php' );

// ===========================
// = WP Cerber Security Name =
// ===========================
define( 'AI1WM_WP_CERBER_SECURITY_NAME', 'aaa-wp-cerber.php' );

// ===============================
// = W3TC config file to exclude =
// ===============================
define( 'AI1WM_W3TC_CONFIG_FILE', 'w3tc-config' . DIRECTORY_SEPARATOR . 'master.php' );

// ==================
// = Error Log Name =
// ==================
define( 'AI1WM_ERROR_NAME', 'error.log' );

// ==============
// = Secret Key =
// ==============
define( 'AI1WM_SECRET_KEY', 'ai1wm_secret_key' );

// =============
// = Auth User =
// =============
define( 'AI1WM_AUTH_USER', 'ai1wm_auth_user' );

// =================
// = Auth Password =
// =================
define( 'AI1WM_AUTH_PASSWORD', 'ai1wm_auth_password' );

// ===============
// = Auth Header =
// ===============
define( 'AI1WM_AUTH_HEADER', 'ai1wm_auth_header' );

// ============
// = Site URL =
// ============
define( 'AI1WM_SITE_URL', 'siteurl' );

// ============
// = Home URL =
// ============
define( 'AI1WM_HOME_URL', 'home' );

// ================
// = Uploads Path =
// ================
define( 'AI1WM_UPLOADS_PATH', 'upload_path' );

// ====================
// = Uploads URL Path =
// ====================
define( 'AI1WM_UPLOADS_URL_PATH', 'upload_url_path' );

// ==================
// = Active Plugins =
// ==================
define( 'AI1WM_ACTIVE_PLUGINS', 'active_plugins' );

// ===========================
// = Active Sitewide Plugins =
// ===========================
define( 'AI1WM_ACTIVE_SITEWIDE_PLUGINS', 'active_sitewide_plugins' );

// ==========================
// = Jetpack Active Modules =
// ==========================
define( 'AI1WM_JETPACK_ACTIVE_MODULES', 'jetpack_active_modules' );

// ====================================
// = Swift Optimizer Plugin Organizer =
// ====================================
define( 'AI1WM_SWIFT_OPTIMIZER_PLUGIN_ORGANIZER', 'swift_performance_plugin_organizer' );

// ======================
// = MS Files Rewriting =
// ======================
define( 'AI1WM_MS_FILES_REWRITING', 'ms_files_rewriting' );

// ===================
// = Active Template =
// ===================
define( 'AI1WM_ACTIVE_TEMPLATE', 'template' );

// =====================
// = Active Stylesheet =
// =====================
define( 'AI1WM_ACTIVE_STYLESHEET', 'stylesheet' );

// ==============
// = DB Version =
// ==============
define( 'AI1WM_DB_VERSION', 'db_version' );

// ======================
// = Initial DB Version =
// ======================
define( 'AI1WM_INITIAL_DB_VERSION', 'initial_db_version' );

// ============
// = Cron Key =
// ============
define( 'AI1WM_CRON', 'cron' );

// =======================
// = Backups Path Option =
// =======================
define( 'AI1WM_BACKUPS_PATH_OPTION', 'ai1wm_backups_path' );

// ===================
// = Backups Labels  =
// ===================
define( 'AI1WM_BACKUPS_LABELS', 'ai1wm_backups_labels' );

// ===============
// = Sites Links =
// ===============
define( 'AI1WM_SITES_LINKS', 'ai1wm_sites_links' );

// ==============================
// = Last Check For Updates Key =
// ==============================
define( 'AI1WM_LAST_CHECK_FOR_UPDATES', 'ai1wm_last_check_for_updates' );

// ===============
// = Updater Key =
// ===============
define( 'AI1WM_UPDATER', 'ai1wm_updater' );

// ==============
// = Status Key =
// ==============
define( 'AI1WM_STATUS', 'ai1wm_status' );

// ================
// = Messages Key =
// ================
define( 'AI1WM_MESSAGES', 'ai1wm_messages' );

// =================
// = Support Email =
// =================
define( 'AI1WM_SUPPORT_EMAIL', 'support@servmask.com' );

// ==================
// = Max Chunk Size =
// ==================
define( 'AI1WM_MAX_CHUNK_SIZE', 5 * 1024 * 1024 );

// =====================
// = Max Chunk Retries =
// =====================
define( 'AI1WM_MAX_CHUNK_RETRIES', 10 );

// ===============
// = CIPHER NAME =
// ===============
define( 'AI1WM_CIPHER_NAME', 'AES-256-CBC' );

// =============
// = SIGN TEXT =
// =============
define( 'AI1WM_SIGN_TEXT', '"How long do you want these messages to remain secret? I want them to remain secret for as long as men are capable of evil." - Neal Stephenson' );

// ===========================
// = Max Transaction Queries =
// ===========================
if ( ! defined( 'AI1WM_MAX_TRANSACTION_QUERIES' ) ) {
	define( 'AI1WM_MAX_TRANSACTION_QUERIES', 1000 );
}

// ======================
// = Max Select Records =
// ======================
if ( ! defined( 'AI1WM_MAX_SELECT_RECORDS' ) ) {
	define( 'AI1WM_MAX_SELECT_RECORDS', 1000 );
}

// =======================
// = Max Storage Cleanup =
// =======================
define( 'AI1WM_MAX_STORAGE_CLEANUP', 24 * 60 * 60 );

// =====================
// = Disk Space Factor =
// =====================
define( 'AI1WM_DISK_SPACE_FACTOR', 2 );

// ====================
// = Disk Space Extra =
//=====================
define( 'AI1WM_DISK_SPACE_EXTRA', 300 * 1024 * 1024 );

// ===========================
// = WP_CONTENT_DIR Constant =
// ===========================
if ( ! defined( 'WP_CONTENT_DIR' ) ) {
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
}

// ========================
// = Backups Default Path =
// ========================
if ( ! defined( 'AI1WM_DEFAULT_BACKUPS_PATH' ) ) {
	define( 'AI1WM_DEFAULT_BACKUPS_PATH', WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'ai1wm-backups' );
}

// ================
// = Backups Path =
// ================
define( 'AI1WM_BACKUPS_PATH', get_option( AI1WM_BACKUPS_PATH_OPTION, AI1WM_DEFAULT_BACKUPS_PATH ) );

// ==========================
// = Storage index.php File =
// ==========================
define( 'AI1WM_STORAGE_INDEX_PHP', AI1WM_STORAGE_PATH . DIRECTORY_SEPARATOR . 'index.php' );

// ===========================
// = Storage index.html File =
// ===========================
define( 'AI1WM_STORAGE_INDEX_HTML', AI1WM_STORAGE_PATH . DIRECTORY_SEPARATOR . 'index.html' );

// ==========================
// = Backups index.php File =
// ==========================
define( 'AI1WM_BACKUPS_INDEX_PHP', AI1WM_BACKUPS_PATH . DIRECTORY_SEPARATOR . 'index.php' );

// ===========================
// = Backups index.html File =
// ===========================
define( 'AI1WM_BACKUPS_INDEX_HTML', AI1WM_BACKUPS_PATH . DIRECTORY_SEPARATOR . 'index.html' );

// ===========================
// = Backups robots.txt File =
// ===========================
define( 'AI1WM_BACKUPS_ROBOTS_TXT', AI1WM_BACKUPS_PATH . DIRECTORY_SEPARATOR . 'robots.txt' );

// ==========================
// = Backups .htaccess File =
// ==========================
define( 'AI1WM_BACKUPS_HTACCESS', AI1WM_BACKUPS_PATH . DIRECTORY_SEPARATOR . '.htaccess' );

// ===========================
// = Backups web.config File =
// ===========================
define( 'AI1WM_BACKUPS_WEBCONFIG', AI1WM_BACKUPS_PATH . DIRECTORY_SEPARATOR . 'web.config' );

// ============================
// = WordPress .htaccess File =
// ============================
define( 'AI1WM_WORDPRESS_HTACCESS', ABSPATH . DIRECTORY_SEPARATOR . '.htaccess' );

// =============================
// = WordPress web.config File =
// =============================
define( 'AI1WM_WORDPRESS_WEBCONFIG', ABSPATH . DIRECTORY_SEPARATOR . 'web.config' );

// ================================
// = WP Migration Plugin Base Dir =
// ================================
if ( defined( 'AI1WM_PLUGIN_BASENAME' ) ) {
	define( 'AI1WM_PLUGIN_BASEDIR', dirname( AI1WM_PLUGIN_BASENAME ) );
} else {
	define( 'AI1WM_PLUGIN_BASEDIR', 'all-in-one-wp-migration' );
}

// ======================================
// = Microsoft Azure Extension Base Dir =
// ======================================
if ( defined( 'AI1WMZE_PLUGIN_BASENAME' ) ) {
	define( 'AI1WMZE_PLUGIN_BASEDIR', dirname( AI1WMZE_PLUGIN_BASENAME ) );
} else {
	define( 'AI1WMZE_PLUGIN_BASEDIR', 'all-in-one-wp-migration-azure-storage-extension' );
}

// ===================================
// = Microsoft Azure Extension Title =
// ===================================
if ( ! defined( 'AI1WMZE_PLUGIN_TITLE' ) ) {
	define( 'AI1WMZE_PLUGIN_TITLE', 'Microsoft Azure Storage Extension' );
}

// ===================================
// = Microsoft Azure Extension About =
// ===================================
if ( ! defined( 'AI1WMZE_PLUGIN_ABOUT' ) ) {
	define( 'AI1WMZE_PLUGIN_ABOUT', 'https://plugin-updates.wp-migration.com/microsoft-azure-storage-extension.json' );
}

// ===================================
// = Microsoft Azure Extension Check =
// ===================================
if ( ! defined( 'AI1WMZE_PLUGIN_CHECK' ) ) {
	define( 'AI1WMZE_PLUGIN_CHECK', 'https://redirect.wp-migration.com/v1/check/microsoft-azure-storage-extension' );
}

// =================================
// = Microsoft Azure Extension Key =
// =================================
if ( ! defined( 'AI1WMZE_PLUGIN_KEY' ) ) {
	define( 'AI1WMZE_PLUGIN_KEY', 'ai1wmze_plugin_key' );
}

// ===================================
// = Microsoft Azure Extension Short =
// ===================================
if ( ! defined( 'AI1WMZE_PLUGIN_SHORT' ) ) {
	define( 'AI1WMZE_PLUGIN_SHORT', 'azure-storage' );
}

// ===================================
// = Backblaze B2 Extension Base Dir =
// ===================================
if ( defined( 'AI1WMAE_PLUGIN_BASENAME' ) ) {
	define( 'AI1WMAE_PLUGIN_BASEDIR', dirname( AI1WMAE_PLUGIN_BASENAME ) );
} else {
	define( 'AI1WMAE_PLUGIN_BASEDIR', 'all-in-one-wp-migration-b2-extension' );
}

// ================================
// = Backblaze B2 Extension Title =
// ================================
if ( ! defined( 'AI1WMAE_PLUGIN_TITLE' ) ) {
	define( 'AI1WMAE_PLUGIN_TITLE', 'Backblaze B2 Extension' );
}

// ================================
// = Backblaze B2 Extension About =
// ================================
if ( ! defined( 'AI1WMAE_PLUGIN_ABOUT' ) ) {
	define( 'AI1WMAE_PLUGIN_ABOUT', 'https://plugin-updates.wp-migration.com/backblaze-b2-extension.json' );
}

// ================================
// = Backblaze B2 Extension Check =
// ================================
if ( ! defined( 'AI1WMAE_PLUGIN_CHECK' ) ) {
	define( 'AI1WMAE_PLUGIN_CHECK', 'https://redirect.wp-migration.com/v1/check/backblaze-b2-extension' );
}

// ==============================
// = Backblaze B2 Extension Key =
// ==============================
if ( ! defined( 'AI1WMAE_PLUGIN_KEY' ) ) {
	define( 'AI1WMAE_PLUGIN_KEY', 'ai1wmae_plugin_key' );
}

// ================================
// = Backblaze B2 Extension Short =
// ================================
if ( ! defined( 'AI1WMAE_PLUGIN_SHORT' ) ) {
	define( 'AI1WMAE_PLUGIN_SHORT', 'b2' );
}

// ==========================
// = Backup Plugin Base Dir =
// ==========================
if ( defined( 'AI1WMVE_PLUGIN_BASENAME' ) ) {
	define( 'AI1WMVE_PLUGIN_BASEDIR', dirname( AI1WMVE_PLUGIN_BASENAME ) );
} else {
	define( 'AI1WMVE_PLUGIN_BASEDIR', 'all-in-one-wp-migration-backup' );
}

// =======================
// = Backup Plugin Title =
// =======================
if ( ! defined( 'AI1WMVE_PLUGIN_TITLE' ) ) {
	define( 'AI1WMVE_PLUGIN_TITLE', 'Backup Plugin' );
}

// =======================
// = Backup Plugin About =
// =======================
if ( ! defined( 'AI1WMVE_PLUGIN_ABOUT' ) ) {
	define( 'AI1WMVE_PLUGIN_ABOUT', 'https://plugin-updates.wp-migration.com/backup-plugin.json' );
}

// =======================
// = Backup Plugin Check =
// =======================
if ( ! defined( 'AI1WMVE_PLUGIN_CHECK' ) ) {
	define( 'AI1WMVE_PLUGIN_CHECK', 'https://redirect.wp-migration.com/v1/check/backup-plugin' );
}

// =====================
// = Backup Plugin Key =
// =====================
if ( ! defined( 'AI1WMVE_PLUGIN_KEY' ) ) {
	define( 'AI1WMVE_PLUGIN_KEY', 'ai1wmve_plugin_key' );
}

// =======================
// = Backup Plugin Short =
// =======================
if ( ! defined( 'AI1WMVE_PLUGIN_SHORT' ) ) {
	define( 'AI1WMVE_PLUGIN_SHORT', 'backup' );
}

// ==========================
// = Box Extension Base Dir =
// ==========================
if ( defined( 'AI1WMBE_PLUGIN_BASENAME' ) ) {
	define( 'AI1WMBE_PLUGIN_BASEDIR', dirname( AI1WMBE_PLUGIN_BASENAME ) );
} else {
	define( 'AI1WMBE_PLUGIN_BASEDIR', 'all-in-one-wp-migration-box-extension' );
}

// =======================
// = Box Extension Title =
// =======================
if ( ! defined( 'AI1WMBE_PLUGIN_TITLE' ) ) {
	define( 'AI1WMBE_PLUGIN_TITLE', 'Box Extension' );
}

// =======================
// = Box Extension About =
// =======================
if ( ! defined( 'AI1WMBE_PLUGIN_ABOUT' ) ) {
	define( 'AI1WMBE_PLUGIN_ABOUT', 'https://plugin-updates.wp-migration.com/box-extension.json' );
}

// =======================
// = Box Extension Check =
// =======================
if ( ! defined( 'AI1WMBE_PLUGIN_CHECK' ) ) {
	define( 'AI1WMBE_PLUGIN_CHECK', 'https://redirect.wp-migration.com/v1/check/box-extension' );
}

// =====================
// = Box Extension Key =
// =====================
if ( ! defined( 'AI1WMBE_PLUGIN_KEY' ) ) {
	define( 'AI1WMBE_PLUGIN_KEY', 'ai1wmbe_plugin_key' );
}

// =======================
// = Box Extension Short =
// =======================
if ( ! defined( 'AI1WMBE_PLUGIN_SHORT' ) ) {
	define( 'AI1WMBE_PLUGIN_SHORT', 'box' );
}

// ==========================================
// = DigitalOcean Spaces Extension Base Dir =
// ==========================================
if ( defined( 'AI1WMIE_PLUGIN_BASENAME' ) ) {
	define( 'AI1WMIE_PLUGIN_BASEDIR', dirname( AI1WMIE_PLUGIN_BASENAME ) );
} else {
	define( 'AI1WMIE_PLUGIN_BASEDIR', 'all-in-one-wp-migration-digitalocean-extension' );
}

// =======================================
// = DigitalOcean Spaces Extension Title =
// =======================================
if ( ! defined( 'AI1WMIE_PLUGIN_TITLE' ) ) {
	define( 'AI1WMIE_PLUGIN_TITLE', 'DigitalOcean Spaces Extension' );
}

// =======================================
// = DigitalOcean Spaces Extension About =
// =======================================
if ( ! defined( 'AI1WMIE_PLUGIN_ABOUT' ) ) {
	define( 'AI1WMIE_PLUGIN_ABOUT', 'https://plugin-updates.wp-migration.com/digitalocean-spaces-extension.json' );
}

// =======================================
// = DigitalOcean Spaces Extension Check =
// =======================================
if ( ! defined( 'AI1WMIE_PLUGIN_CHECK' ) ) {
	define( 'AI1WMIE_PLUGIN_CHECK', 'https://redirect.wp-migration.com/v1/check/digitalocean-spaces-extension' );
}

// =====================================
// = DigitalOcean Spaces Extension Key =
// =====================================
if ( ! defined( 'AI1WMIE_PLUGIN_KEY' ) ) {
	define( 'AI1WMIE_PLUGIN_KEY', 'ai1wmie_plugin_key' );
}

// =======================================
// = DigitalOcean Spaces Extension Short =
// =======================================
if ( ! defined( 'AI1WMIE_PLUGIN_SHORT' ) ) {
	define( 'AI1WMIE_PLUGIN_SHORT', 'digitalocean' );
}

// =============================
// = Direct Extension Base Dir =
// =============================
if ( defined( 'AI1WMXE_PLUGIN_BASENAME' ) ) {
	define( 'AI1WMXE_PLUGIN_BASEDIR', dirname( AI1WMXE_PLUGIN_BASENAME ) );
} else {
	define( 'AI1WMXE_PLUGIN_BASEDIR', 'all-in-one-wp-migration-direct-extension' );
}
// ==========================
// = Direct Extension Title =
// ==========================
if ( ! defined( 'AI1WMXE_PLUGIN_TITLE' ) ) {
	define( 'AI1WMXE_PLUGIN_TITLE', 'Direct Extension' );
}
// ==========================
// = Direct Extension About =
// ==========================
if ( ! defined( 'AI1WMXE_PLUGIN_ABOUT' ) ) {
	define( 'AI1WMXE_PLUGIN_ABOUT', 'https://plugin-updates.wp-migration.com/direct-extension.json' );
}

// ==========================
// = Direct Extension Check =
// ==========================
if ( ! defined( 'AI1WMXE_PLUGIN_CHECK' ) ) {
	define( 'AI1WMXE_PLUGIN_CHECK', 'https://redirect.wp-migration.com/v1/check/direct-extension' );
}

// ========================
// = Direct Extension Key =
// ========================
if ( ! defined( 'AI1WMXE_PLUGIN_KEY' ) ) {
	define( 'AI1WMXE_PLUGIN_KEY', 'ai1wmxe_plugin_key' );
}
// ==========================
// = Direct Extension Short =
// ==========================
if ( ! defined( 'AI1WMXE_PLUGIN_SHORT' ) ) {
	define( 'AI1WMXE_PLUGIN_SHORT', 'direct' );
}

// ==============================
// = Dropbox Extension Base Dir =
// ==============================
if ( defined( 'AI1WMDE_PLUGIN_BASENAME' ) ) {
	define( 'AI1WMDE_PLUGIN_BASEDIR', dirname( AI1WMDE_PLUGIN_BASENAME ) );
} else {
	define( 'AI1WMDE_PLUGIN_BASEDIR', 'all-in-one-wp-migration-dropbox-extension' );
}

// ===========================
// = Dropbox Extension Title =
// ===========================
if ( ! defined( 'AI1WMDE_PLUGIN_TITLE' ) ) {
	define( 'AI1WMDE_PLUGIN_TITLE', 'Dropbox Extension' );
}

// ===========================
// = Dropbox Extension About =
// ===========================
if ( ! defined( 'AI1WMDE_PLUGIN_ABOUT' ) ) {
	define( 'AI1WMDE_PLUGIN_ABOUT', 'https://plugin-updates.wp-migration.com/dropbox-extension.json' );
}

// ===========================
// = Dropbox Extension Check =
// ===========================
if ( ! defined( 'AI1WMDE_PLUGIN_CHECK' ) ) {
	define( 'AI1WMDE_PLUGIN_CHECK', 'https://redirect.wp-migration.com/v1/check/dropbox-extension' );
}

// =========================
// = Dropbox Extension Key =
// =========================
if ( ! defined( 'AI1WMDE_PLUGIN_KEY' ) ) {
	define( 'AI1WMDE_PLUGIN_KEY', 'ai1wmde_plugin_key' );
}

// ===========================
// = Dropbox Extension Short =
// ===========================
if ( ! defined( 'AI1WMDE_PLUGIN_SHORT' ) ) {
	define( 'AI1WMDE_PLUGIN_SHORT', 'dropbox' );
}

// ===========================
// = File Extension Base Dir =
// ===========================
if ( defined( 'AI1WMTE_PLUGIN_BASENAME' ) ) {
	define( 'AI1WMTE_PLUGIN_BASEDIR', dirname( AI1WMTE_PLUGIN_BASENAME ) );
} else {
	define( 'AI1WMTE_PLUGIN_BASEDIR', 'all-in-one-wp-migration-file-extension' );
}

// ========================
// = File Extension Title =
// ========================
if ( ! defined( 'AI1WMTE_PLUGIN_TITLE' ) ) {
	define( 'AI1WMTE_PLUGIN_TITLE', 'File Extension' );
}

// ========================
// = File Extension About =
// ========================
if ( ! defined( 'AI1WMTE_PLUGIN_ABOUT' ) ) {
	define( 'AI1WMTE_PLUGIN_ABOUT', 'https://import.wp-migration.com/file-extension.json' );
}

// ========================
// = File Extension Check =
// ========================
if ( ! defined( 'AI1WMTE_PLUGIN_CHECK' ) ) {
	define( 'AI1WMTE_PLUGIN_CHECK', 'https://redirect.wp-migration.com/v1/check/file-extension' );
}

// ======================
// = File Extension Key =
// ======================
if ( ! defined( 'AI1WMTE_PLUGIN_KEY' ) ) {
	define( 'AI1WMTE_PLUGIN_KEY', 'ai1wmte_plugin_key' );
}

// ========================
// = File Extension Short =
// ========================
if ( ! defined( 'AI1WMTE_PLUGIN_SHORT' ) ) {
	define( 'AI1WMTE_PLUGIN_SHORT', 'file' );
}

// ==========================
// = FTP Extension Base Dir =
// ==========================
if ( defined( 'AI1WMFE_PLUGIN_BASENAME' ) ) {
	define( 'AI1WMFE_PLUGIN_BASEDIR', dirname( AI1WMFE_PLUGIN_BASENAME ) );
} else {
	define( 'AI1WMFE_PLUGIN_BASEDIR', 'all-in-one-wp-migration-ftp-extension' );
}

// =======================
// = FTP Extension Title =
// =======================
if ( ! defined( 'AI1WMFE_PLUGIN_TITLE' ) ) {
	define( 'AI1WMFE_PLUGIN_TITLE', 'FTP Extension' );
}

// =======================
// = FTP Extension About =
// =======================
if ( ! defined( 'AI1WMFE_PLUGIN_ABOUT' ) ) {
	define( 'AI1WMFE_PLUGIN_ABOUT', 'https://plugin-updates.wp-migration.com/ftp-extension.json' );
}

// =======================
// = FTP Extension Check =
// =======================
if ( ! defined( 'AI1WMFE_PLUGIN_CHECK' ) ) {
	define( 'AI1WMFE_PLUGIN_CHECK', 'https://redirect.wp-migration.com/v1/check/ftp-extension' );
}

// =====================
// = FTP Extension Key =
// =====================
if ( ! defined( 'AI1WMFE_PLUGIN_KEY' ) ) {
	define( 'AI1WMFE_PLUGIN_KEY', 'ai1wmfe_plugin_key' );
}

// =======================
// = FTP Extension Short =
// =======================
if ( ! defined( 'AI1WMFE_PLUGIN_SHORT' ) ) {
	define( 'AI1WMFE_PLUGIN_SHORT', 'ftp' );
}

// ===========================================
// = Google Cloud Storage Extension Base Dir =
// ===========================================
if ( defined( 'AI1WMCE_PLUGIN_BASENAME' ) ) {
	define( 'AI1WMCE_PLUGIN_BASEDIR', dirname( AI1WMCE_PLUGIN_BASENAME ) );
} else {
	define( 'AI1WMCE_PLUGIN_BASEDIR', 'all-in-one-wp-migration-gcloud-storage-extension' );
}

// ========================================
// = Google Cloud Storage Extension Title =
// ========================================
if ( ! defined( 'AI1WMCE_PLUGIN_TITLE' ) ) {
	define( 'AI1WMCE_PLUGIN_TITLE', 'Google Cloud Storage Extension' );
}

// ========================================
// = Google Cloud Storage Extension About =
// ========================================
if ( ! defined( 'AI1WMCE_PLUGIN_ABOUT' ) ) {
	define( 'AI1WMCE_PLUGIN_ABOUT', 'https://plugin-updates.wp-migration.com/google-cloud-storage-extension.json' );
}

// ========================================
// = Google Cloud Storage Extension Check =
// ========================================
if ( ! defined( 'AI1WMCE_PLUGIN_CHECK' ) ) {
	define( 'AI1WMCE_PLUGIN_CHECK', 'https://redirect.wp-migration.com/v1/check/google-cloud-storage-extension' );
}

// ======================================
// = Google Cloud Storage Extension Key =
// ======================================
if ( ! defined( 'AI1WMCE_PLUGIN_KEY' ) ) {
	define( 'AI1WMCE_PLUGIN_KEY', 'ai1wmce_plugin_key' );
}

// ========================================
// = Google Cloud Storage Extension Short =
// ========================================
if ( ! defined( 'AI1WMCE_PLUGIN_SHORT' ) ) {
	define( 'AI1WMCE_PLUGIN_SHORT', 'gcloud-storage' );
}

// ===================================
// = Google Drive Extension Base Dir =
// ===================================
if ( defined( 'AI1WMGE_PLUGIN_BASENAME' ) ) {
	define( 'AI1WMGE_PLUGIN_BASEDIR', dirname( AI1WMGE_PLUGIN_BASENAME ) );
} else {
	define( 'AI1WMGE_PLUGIN_BASEDIR', 'all-in-one-wp-migration-gdrive-extension' );
}

// ================================
// = Google Drive Extension Title =
// ================================
if ( ! defined( 'AI1WMGE_PLUGIN_TITLE' ) ) {
	define( 'AI1WMGE_PLUGIN_TITLE', 'Google Drive Extension' );
}

// ================================
// = Google Drive Extension About =
// ================================
if ( ! defined( 'AI1WMGE_PLUGIN_ABOUT' ) ) {
	define( 'AI1WMGE_PLUGIN_ABOUT', 'https://plugin-updates.wp-migration.com/google-drive-extension.json' );
}

// ================================
// = Google Drive Extension Check =
// ================================
if ( ! defined( 'AI1WMGE_PLUGIN_CHECK' ) ) {
	define( 'AI1WMGE_PLUGIN_CHECK', 'https://redirect.wp-migration.com/v1/check/google-drive-extension' );
}

// ==============================
// = Google Drive Extension Key =
// ==============================
if ( ! defined( 'AI1WMGE_PLUGIN_KEY' ) ) {
	define( 'AI1WMGE_PLUGIN_KEY', 'ai1wmge_plugin_key' );
}

// ================================
// = Google Drive Extension Short =
// ================================
if ( ! defined( 'AI1WMGE_PLUGIN_SHORT' ) ) {
	define( 'AI1WMGE_PLUGIN_SHORT', 'gdrive' );
}

// =====================================
// = Amazon Glacier Extension Base Dir =
// =====================================
if ( defined( 'AI1WMRE_PLUGIN_BASENAME' ) ) {
	define( 'AI1WMRE_PLUGIN_BASEDIR', dirname( AI1WMRE_PLUGIN_BASENAME ) );
} else {
	define( 'AI1WMRE_PLUGIN_BASEDIR', 'all-in-one-wp-migration-glacier-extension' );
}

// ==================================
// = Amazon Glacier Extension Title =
// ==================================
if ( ! defined( 'AI1WMRE_PLUGIN_TITLE' ) ) {
	define( 'AI1WMRE_PLUGIN_TITLE', 'Amazon Glacier Extension' );
}

// ==================================
// = Amazon Glacier Extension About =
// ==================================
if ( ! defined( 'AI1WMRE_PLUGIN_ABOUT' ) ) {
	define( 'AI1WMRE_PLUGIN_ABOUT', 'https://plugin-updates.wp-migration.com/amazon-glacier-extension.json' );
}

// ==================================
// = Amazon Glacier Extension Check =
// ==================================
if ( ! defined( 'AI1WMRE_PLUGIN_CHECK' ) ) {
	define( 'AI1WMRE_PLUGIN_CHECK', 'https://redirect.wp-migration.com/v1/check/amazon-glacier-extension' );
}

// ================================
// = Amazon Glacier Extension Key =
// ================================
if ( ! defined( 'AI1WMRE_PLUGIN_KEY' ) ) {
	define( 'AI1WMRE_PLUGIN_KEY', 'ai1wmre_plugin_key' );
}

// ==================================
// = Amazon Glacier Extension Short =
// ==================================
if ( ! defined( 'AI1WMRE_PLUGIN_SHORT' ) ) {
	define( 'AI1WMRE_PLUGIN_SHORT', 'glacier' );
}

// ===========================
// = Mega Extension Base Dir =
// ===========================
if ( defined( 'AI1WMEE_PLUGIN_BASENAME' ) ) {
	define( 'AI1WMEE_PLUGIN_BASEDIR', dirname( AI1WMEE_PLUGIN_BASENAME ) );
} else {
	define( 'AI1WMEE_PLUGIN_BASEDIR', 'all-in-one-wp-migration-mega-extension' );
}

// ========================
// = Mega Extension Title =
// ========================
if ( ! defined( 'AI1WMEE_PLUGIN_TITLE' ) ) {
	define( 'AI1WMEE_PLUGIN_TITLE', 'Mega Extension' );
}

// ========================
// = Mega Extension About =
// ========================
if ( ! defined( 'AI1WMEE_PLUGIN_ABOUT' ) ) {
	define( 'AI1WMEE_PLUGIN_ABOUT', 'https://plugin-updates.wp-migration.com/mega-extension.json' );
}

// ========================
// = Mega Extension Check =
// ========================
if ( ! defined( 'AI1WMEE_PLUGIN_CHECK' ) ) {
	define( 'AI1WMEE_PLUGIN_CHECK', 'https://redirect.wp-migration.com/v1/check/mega-extension' );
}

// ======================
// = Mega Extension Key =
// ======================
if ( ! defined( 'AI1WMEE_PLUGIN_KEY' ) ) {
	define( 'AI1WMEE_PLUGIN_KEY', 'ai1wmee_plugin_key' );
}

// ========================
// = Mega Extension Short =
// ========================
if ( ! defined( 'AI1WMEE_PLUGIN_SHORT' ) ) {
	define( 'AI1WMEE_PLUGIN_SHORT', 'mega' );
}

// ================================
// = Multisite Extension Base Dir =
// ================================
if ( defined( 'AI1WMME_PLUGIN_BASENAME' ) ) {
	define( 'AI1WMME_PLUGIN_BASEDIR', dirname( AI1WMME_PLUGIN_BASENAME ) );
} else {
	define( 'AI1WMME_PLUGIN_BASEDIR', 'all-in-one-wp-migration-multisite-extension' );
}

// =============================
// = Multisite Extension Title =
// =============================
if ( ! defined( 'AI1WMME_PLUGIN_TITLE' ) ) {
	define( 'AI1WMME_PLUGIN_TITLE', 'Multisite Extension' );
}

// =============================
// = Multisite Extension About =
// =============================
if ( ! defined( 'AI1WMME_PLUGIN_ABOUT' ) ) {
	define( 'AI1WMME_PLUGIN_ABOUT', 'https://plugin-updates.wp-migration.com/multisite-extension.json' );
}

// =============================
// = Multisite Extension Check =
// =============================
if ( ! defined( 'AI1WMME_PLUGIN_CHECK' ) ) {
	define( 'AI1WMME_PLUGIN_CHECK', 'https://redirect.wp-migration.com/v1/check/multisite-extension' );
}

// ===========================
// = Multisite Extension Key =
// ===========================
if ( ! defined( 'AI1WMME_PLUGIN_KEY' ) ) {
	define( 'AI1WMME_PLUGIN_KEY', 'ai1wmme_plugin_key' );
}

// =============================
// = Multisite Extension Short =
// =============================
if ( ! defined( 'AI1WMME_PLUGIN_SHORT' ) ) {
	define( 'AI1WMME_PLUGIN_SHORT', 'multisite' );
}

// ===============================
// = OneDrive Extension Base Dir =
// ===============================
if ( defined( 'AI1WMOE_PLUGIN_BASENAME' ) ) {
	define( 'AI1WMOE_PLUGIN_BASEDIR', dirname( AI1WMOE_PLUGIN_BASENAME ) );
} else {
	define( 'AI1WMOE_PLUGIN_BASEDIR', 'all-in-one-wp-migration-onedrive-extension' );
}

// ============================
// = OneDrive Extension Title =
// ============================
if ( ! defined( 'AI1WMOE_PLUGIN_TITLE' ) ) {
	define( 'AI1WMOE_PLUGIN_TITLE', 'OneDrive Extension' );
}

// ============================
// = OneDrive Extension About =
// ============================
if ( ! defined( 'AI1WMOE_PLUGIN_ABOUT' ) ) {
	define( 'AI1WMOE_PLUGIN_ABOUT', 'https://plugin-updates.wp-migration.com/onedrive-extension.json' );
}

// ============================
// = OneDrive Extension Check =
// ============================
if ( ! defined( 'AI1WMOE_PLUGIN_CHECK' ) ) {
	define( 'AI1WMOE_PLUGIN_CHECK', 'https://redirect.wp-migration.com/v1/check/onedrive-extension' );
}

// ==========================
// = OneDrive Extension Key =
// ==========================
if ( ! defined( 'AI1WMOE_PLUGIN_KEY' ) ) {
	define( 'AI1WMOE_PLUGIN_KEY', 'ai1wmoe_plugin_key' );
}

// ============================
// = OneDrive Extension Short =
// ============================
if ( ! defined( 'AI1WMOE_PLUGIN_SHORT' ) ) {
	define( 'AI1WMOE_PLUGIN_SHORT', 'onedrive' );
}

// =============================
// = pCloud Extension Base Dir =
// =============================
if ( defined( 'AI1WMPE_PLUGIN_BASENAME' ) ) {
	define( 'AI1WMPE_PLUGIN_BASEDIR', dirname( AI1WMPE_PLUGIN_BASENAME ) );
} else {
	define( 'AI1WMPE_PLUGIN_BASEDIR', 'all-in-one-wp-migration-pcloud-extension' );
}

// ==========================
// = pCloud Extension Title =
// ==========================
if ( ! defined( 'AI1WMPE_PLUGIN_TITLE' ) ) {
	define( 'AI1WMPE_PLUGIN_TITLE', 'pCloud Extension' );
}

// ==========================
// = pCloud Extension About =
// ==========================
if ( ! defined( 'AI1WMPE_PLUGIN_ABOUT' ) ) {
	define( 'AI1WMPE_PLUGIN_ABOUT', 'https://plugin-updates.wp-migration.com/pcloud-extension.json' );
}

// ==========================
// = pCloud Extension Check =
// ==========================
if ( ! defined( 'AI1WMPE_PLUGIN_CHECK' ) ) {
	define( 'AI1WMPE_PLUGIN_CHECK', 'https://redirect.wp-migration.com/v1/check/pcloud-extension' );
}

// ========================
// = pCloud Extension Key =
// ========================
if ( ! defined( 'AI1WMPE_PLUGIN_KEY' ) ) {
	define( 'AI1WMPE_PLUGIN_KEY', 'ai1wmpe_plugin_key' );
}

// ==========================
// = pCloud Extension Short =
// ==========================
if ( ! defined( 'AI1WMPE_PLUGIN_SHORT' ) ) {
	define( 'AI1WMPE_PLUGIN_SHORT', 'pcloud' );
}

// =======================
// = Pro Plugin Base Dir =
// =======================
if ( defined( 'AI1WMKE_PLUGIN_BASENAME' ) ) {
	define( 'AI1WMKE_PLUGIN_BASEDIR', dirname( AI1WMKE_PLUGIN_BASENAME ) );
} else {
	define( 'AI1WMKE_PLUGIN_BASEDIR', 'all-in-one-wp-migration-pro' );
}

// ====================
// = Pro Plugin Title =
// ====================
if ( ! defined( 'AI1WMKE_PLUGIN_TITLE' ) ) {
	define( 'AI1WMKE_PLUGIN_TITLE', 'Pro Plugin' );
}

// ====================
// = Pro Plugin About =
// ====================
if ( ! defined( 'AI1WMKE_PLUGIN_ABOUT' ) ) {
	define( 'AI1WMKE_PLUGIN_ABOUT', 'https://plugin-updates.wp-migration.com/pro-plugin.json' );
}

// ====================
// = Pro Plugin Check =
// ====================
if ( ! defined( 'AI1WMKE_PLUGIN_CHECK' ) ) {
	define( 'AI1WMKE_PLUGIN_CHECK', 'https://redirect.wp-migration.com/v1/check/pro-plugin' );
}

// ==================
// = Pro Plugin Key =
// ==================
if ( ! defined( 'AI1WMKE_PLUGIN_KEY' ) ) {
	define( 'AI1WMKE_PLUGIN_KEY', 'ai1wmke_plugin_key' );
}

// ====================
// = Pro Plugin Short =
// ====================
if ( ! defined( 'AI1WMKE_PLUGIN_SHORT' ) ) {
	define( 'AI1WMKE_PLUGIN_SHORT', 'pro' );
}

// ================================
// = S3 Client Extension Base Dir =
// ================================
if ( defined( 'AI1WMNE_PLUGIN_BASENAME' ) ) {
	define( 'AI1WMNE_PLUGIN_BASEDIR', dirname( AI1WMNE_PLUGIN_BASENAME ) );
} else {
	define( 'AI1WMNE_PLUGIN_BASEDIR', 'all-in-one-wp-migration-s3-client-extension' );
}

// =============================
// = S3 Client Extension Title =
// =============================
if ( ! defined( 'AI1WMNE_PLUGIN_TITLE' ) ) {
	define( 'AI1WMNE_PLUGIN_TITLE', 'S3 Client Extension' );
}

// =============================
// = S3 Client Extension About =
// =============================
if ( ! defined( 'AI1WMNE_PLUGIN_ABOUT' ) ) {
	define( 'AI1WMNE_PLUGIN_ABOUT', 'https://plugin-updates.wp-migration.com/s3-client-extension.json' );
}

// =============================
// = S3 Client Extension Check =
// =============================
if ( ! defined( 'AI1WMNE_PLUGIN_CHECK' ) ) {
	define( 'AI1WMNE_PLUGIN_CHECK', 'https://redirect.wp-migration.com/v1/check/s3-client-extension' );
}

// ===========================
// = S3 Client Extension Key =
// ===========================
if ( ! defined( 'AI1WMNE_PLUGIN_KEY' ) ) {
	define( 'AI1WMNE_PLUGIN_KEY', 'ai1wmne_plugin_key' );
}

// =============================
// = S3 Client Extension Short =
// =============================
if ( ! defined( 'AI1WMNE_PLUGIN_SHORT' ) ) {
	define( 'AI1WMNE_PLUGIN_SHORT', 's3-client' );
}

// ================================
// = Amazon S3 Extension Base Dir =
// ================================
if ( defined( 'AI1WMSE_PLUGIN_BASENAME' ) ) {
	define( 'AI1WMSE_PLUGIN_BASEDIR', dirname( AI1WMSE_PLUGIN_BASENAME ) );
} else {
	define( 'AI1WMSE_PLUGIN_BASEDIR', 'all-in-one-wp-migration-s3-extension' );
}

// =============================
// = Amazon S3 Extension Title =
// =============================
if ( ! defined( 'AI1WMSE_PLUGIN_TITLE' ) ) {
	define( 'AI1WMSE_PLUGIN_TITLE', 'Amazon S3 Extension' );
}

// =============================
// = Amazon S3 Extension About =
// =============================
if ( ! defined( 'AI1WMSE_PLUGIN_ABOUT' ) ) {
	define( 'AI1WMSE_PLUGIN_ABOUT', 'https://plugin-updates.wp-migration.com/amazon-s3-extension.json' );
}

// =============================
// = Amazon S3 Extension Check =
// =============================
if ( ! defined( 'AI1WMSE_PLUGIN_CHECK' ) ) {
	define( 'AI1WMSE_PLUGIN_CHECK', 'https://redirect.wp-migration.com/v1/check/amazon-s3-extension' );
}

// ===========================
// = Amazon S3 Extension Key =
// ===========================
if ( ! defined( 'AI1WMSE_PLUGIN_KEY' ) ) {
	define( 'AI1WMSE_PLUGIN_KEY', 'ai1wmse_plugin_key' );
}

// =============================
// = Amazon S3 Extension Short =
// =============================
if ( ! defined( 'AI1WMSE_PLUGIN_SHORT' ) ) {
	define( 'AI1WMSE_PLUGIN_SHORT', 's3' );
}

// ================================
// = Unlimited Extension Base Dir =
// ================================
if ( defined( 'AI1WMUE_PLUGIN_BASENAME' ) ) {
	define( 'AI1WMUE_PLUGIN_BASEDIR', dirname( AI1WMUE_PLUGIN_BASENAME ) );
} else {
	define( 'AI1WMUE_PLUGIN_BASEDIR', 'all-in-one-wp-migration-unlimited-extension' );
}

// =============================
// = Unlimited Extension Title =
// =============================
if ( ! defined( 'AI1WMUE_PLUGIN_TITLE' ) ) {
	define( 'AI1WMUE_PLUGIN_TITLE', 'Unlimited Extension' );
}

// =============================
// = Unlimited Extension About =
// =============================
if ( ! defined( 'AI1WMUE_PLUGIN_ABOUT' ) ) {
	define( 'AI1WMUE_PLUGIN_ABOUT', 'https://plugin-updates.wp-migration.com/unlimited-extension.json' );
}

// =============================
// = Unlimited Extension Check =
// =============================
if ( ! defined( 'AI1WMUE_PLUGIN_CHECK' ) ) {
	define( 'AI1WMUE_PLUGIN_CHECK', 'https://redirect.wp-migration.com/v1/check/unlimited-extension' );
}

// ===========================
// = Unlimited Extension Key =
// ===========================
if ( ! defined( 'AI1WMUE_PLUGIN_KEY' ) ) {
	define( 'AI1WMUE_PLUGIN_KEY', 'ai1wmue_plugin_key' );
}

// =============================
// = Unlimited Extension Short =
// =============================
if ( ! defined( 'AI1WMUE_PLUGIN_SHORT' ) ) {
	define( 'AI1WMUE_PLUGIN_SHORT', 'unlimited' );
}

// ==========================
// = URL Extension Base Dir =
// ==========================
if ( defined( 'AI1WMLE_PLUGIN_BASENAME' ) ) {
	define( 'AI1WMLE_PLUGIN_BASEDIR', dirname( AI1WMLE_PLUGIN_BASENAME ) );
} else {
	define( 'AI1WMLE_PLUGIN_BASEDIR', 'all-in-one-wp-migration-url-extension' );
}

// =======================
// = URL Extension Title =
// =======================
if ( ! defined( 'AI1WMLE_PLUGIN_TITLE' ) ) {
	define( 'AI1WMLE_PLUGIN_TITLE', 'URL Extension' );
}

// =======================
// = URL Extension About =
// =======================
if ( ! defined( 'AI1WMLE_PLUGIN_ABOUT' ) ) {
	define( 'AI1WMLE_PLUGIN_ABOUT', 'https://plugin-updates.wp-migration.com/url-extension.json' );
}

// =======================
// = URL Extension Check =
// =======================
if ( ! defined( 'AI1WMLE_PLUGIN_CHECK' ) ) {
	define( 'AI1WMLE_PLUGIN_CHECK', 'https://redirect.wp-migration.com/v1/check/url-extension' );
}

// =====================
// = URL Extension Key =
// =====================
if ( ! defined( 'AI1WMLE_PLUGIN_KEY' ) ) {
	define( 'AI1WMLE_PLUGIN_KEY', 'ai1wmle_plugin_key' );
}

// =======================
// = URL Extension Short =
// =======================
if ( ! defined( 'AI1WMLE_PLUGIN_SHORT' ) ) {
	define( 'AI1WMLE_PLUGIN_SHORT', 'url' );
}

// =============================
// = WebDAV Extension Base Dir =
// =============================
if ( defined( 'AI1WMWE_PLUGIN_BASENAME' ) ) {
	define( 'AI1WMWE_PLUGIN_BASEDIR', dirname( AI1WMWE_PLUGIN_BASENAME ) );
} else {
	define( 'AI1WMWE_PLUGIN_BASEDIR', 'all-in-one-wp-migration-webdav-extension' );
}

// ==========================
// = WebDAV Extension Title =
// ==========================
if ( ! defined( 'AI1WMWE_PLUGIN_TITLE' ) ) {
	define( 'AI1WMWE_PLUGIN_TITLE', 'WebDAV Extension' );
}

// ==========================
// = WebDAV Extension About =
// ==========================
if ( ! defined( 'AI1WMWE_PLUGIN_ABOUT' ) ) {
	define( 'AI1WMWE_PLUGIN_ABOUT', 'https://plugin-updates.wp-migration.com/webdav-extension.json' );
}

// ==========================
// = WebDAV Extension Check =
// ==========================
if ( ! defined( 'AI1WMWE_PLUGIN_CHECK' ) ) {
	define( 'AI1WMWE_PLUGIN_CHECK', 'https://redirect.wp-migration.com/v1/check/webdav-extension' );
}

// ========================
// = WebDAV Extension Key =
// ========================
if ( ! defined( 'AI1WMWE_PLUGIN_KEY' ) ) {
	define( 'AI1WMWE_PLUGIN_KEY', 'ai1wmwe_plugin_key' );
}

// ==========================
// = WebDAV Extension Short =
// ==========================
if ( ! defined( 'AI1WMWE_PLUGIN_SHORT' ) ) {
	define( 'AI1WMWE_PLUGIN_SHORT', 'webdav' );
}
