maneuver-agency/wp-cli-sync
===========================

Will copy database and uploaded files.

Quick links: [Installing](#installing) | [Using](#using) | [Plugin Integrations](#plugin-integrations) | [Contributing](#contributing) | [Support](#support)

## Installing

Make sure WP-CLI is installed on the remote server and you have SSH access.  
Rsync needs to be installed to transfer the uploads folder.

Installing this package requires WP-CLI v1.5.0 or greater. Update to the latest stable release with `wp cli update`.

Once you've done so, you can install this package with:

    wp package install git@github.com:maneuver-agency/wp-cli-sync.git

## Using

Create a wp-cli.local.yml file in your local WordPress project.

Example:

    url: http://example.local.dev

    @staging:
      ssh: root@<ip>/path/to/staging/wp-root
      url: http://staging.example.com

    @production:
      ssh: root@<ip>/path/to/production/wp-root
      url: https://www.example.com

Now you can sync your local development environment with one of the defined environments:

    wp sync --env=staging

or

    wp sync --env=production

Use the --force attribute to bypass the confirmation prompt.

### Uploads

The uploads folder is synced by default. You can ignore it with:

    wp sync --env=production --no-uploads

### Users

User data is not synced by default. Having all this data on many local machines wouldn't be a good idea considering privacy laws.

Include user data with:

    wp sync --env=production --users

## Plugin Integrations

### Ninja Forms

All Ninja Forms subscriptions will be removed from the export. Syncing sensitive information of visitors to your local machine is not recommended.

There's no switch.

## Contributing

We appreciate you taking the initiative to contribute to this project.

Contributing isn’t limited to just code. We encourage you to contribute in the way that best fits your abilities, by writing tutorials, giving a demo at your local meetup, helping other users with their support questions, or revising our documentation.

For a more thorough introduction, [check out WP-CLI's guide to contributing](https://make.wordpress.org/cli/handbook/contributing/). This package follows those policy and guidelines.

### Reporting a bug

Think you’ve found a bug? We’d love for you to help us get it fixed.

Before you create a new issue, you should [search existing issues](https://github.com/maneuver-agency/sync/issues?q=label%3Abug%20) to see if there’s an existing resolution to it, or if it’s already been fixed in a newer version.

Once you’ve done a bit of searching and discovered there isn’t an open or fixed issue for your bug, please [create a new issue](https://github.com/maneuver-agency/sync/issues/new). Include as much detail as you can, and clear steps to reproduce if possible. For more guidance, [review our bug report documentation](https://make.wordpress.org/cli/handbook/bug-reports/).

### Creating a pull request

Want to contribute a new feature? Please first [open a new issue](https://github.com/maneuver-agency/sync/issues/new) to discuss whether the feature is a good fit for the project.

Once you've decided to commit the time to seeing your pull request through, [please follow our guidelines for creating a pull request](https://make.wordpress.org/cli/handbook/pull-requests/) to make sure it's a pleasant experience. See "[Setting up](https://make.wordpress.org/cli/handbook/pull-requests/#setting-up)" for details specific to working on this package locally.

## Support

Github issues aren't for general support questions, but there are other venues you can try: https://wp-cli.org/#support


*This README.md is generated dynamically from the project's codebase using `wp scaffold package-readme` ([doc](https://github.com/wp-cli/scaffold-package-command#wp-scaffold-package-readme)). To suggest changes, please submit a pull request against the corresponding part of the codebase.*
