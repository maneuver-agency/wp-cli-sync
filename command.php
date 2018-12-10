<?php

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

/**
 * Syncs a remote WP install with the local installation.
 *
 * @when after_wp_load
 */
$do_sync = function($args, $assoc_args) {

  $env = WP_CLI\Utils\get_flag_value($assoc_args, 'env');
  $cwd = getcwd();
  $sqlfile = $cwd . '/sync.sql';
  $force = WP_CLI\Utils\get_flag_value($assoc_args, 'force', false);

  if (empty($env)) {
    WP_CLI::error( 'Please provide an environment. Like --env=staging or --env=production.' );
  }

  $env = '@'.$env;

  $aliasses = WP_CLI::get_configurator()->get_aliases();

  if (!isset($aliasses[$env])) {
    WP_CLI::error( "Environment $env doesn't seem present in your WP-CLI config." );
  }

  $localdomain = WP_CLI::runcommand("option get home", ['return' => true]);
  $remotedomain = WP_CLI::runcommand("$env option get home", ['return' => true]);

  // Ask for confirmation.
  if (!$force) {
    WP_CLI::confirm("Local domain is $localdomain. Remote domain is $remotedomain. Continue?");
  }

  // Generate table names with the correct prefix.
  // @NOTE: Remote and local prefix should match!!!
  $dbprefix = WP_CLI::runcommand('db prefix', ['return' => true]);
  $table_users = $dbprefix . 'users';
  $table_usermeta = $dbprefix . 'usermeta';
  $table_posts = $dbprefix . 'posts';
  $table_postmeta = $dbprefix . 'postmeta';

  // Export DB on remote server.
  WP_CLI::log('- Deleting transients.');
  WP_CLI::runcommand("$env transient delete --all");

  // Export DB on remote server.
  WP_CLI::log('- Exporting database.');
  WP_CLI::runcommand("$env db export --exclude_tables={$table_users},{$table_usermeta} - > \"$sqlfile\"");

  // Import into local DB
  WP_CLI::log('- Importing database.');
  WP_CLI::runcommand("db import \"$sqlfile\"");
  
  // Search and replace domains
  if ($remotedomain && $localdomain) {
    WP_CLI::log('- Replacing domains.');
    WP_CLI::runcommand("search-replace $remotedomain $localdomain --no-report");
  }

  // Remove Ninja Forms personal data
  WP_CLI::log('- Removing Ninja Forms submissions.');
  WP_CLI::runcommand("db query 'delete from {$table_postmeta} where post_id in (select id from {$table_posts} where post_type = \"nf_sub\");delete from {$table_posts} where post_type = \"nf_sub\";'");
  
  // Remove dump file
  unlink($sqlfile);

  $ssh = rtrim($aliasses[$env]['ssh'], '/');
  $uploads = wp_upload_dir();
  $upload_full_path = $uploads['basedir'];
  $upload_rel_path = trim($uploads['relative'], '/');

  // rsync needs a fully qualified remote path with a colon before the path.
  $ssh = preg_replace('/\//', ':/', $ssh, 1);

  $cmd = "rsync -av --delete $ssh/$upload_rel_path/ \"$upload_full_path\"";
  
  // Transfer all uploaded files
  WP_CLI::log('- Transfering uploads folder.');
  passthru($cmd);

	WP_CLI::success( "Sync complete." );
};

WP_CLI::add_command( 'sync', $do_sync );
