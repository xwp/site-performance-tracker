# Contribute

All contributions are welcome! Please create [an issue](https://github.com/xwp/site-performance-tracker/issues) for bugs and feature requests, and use [pull requests](https://github.com/xwp/site-performance-tracker/pulls) for code contributions.

## Project Setup  

- Build artifacts are tracked in the repository until we create a dedicated distribution repository that can be used for storing the Composer packages.

- We use [`wp-env`](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/) for local development environment. See all the `env:*` scripts in `package.json` for supported commands and helpers.

- `webpack.config.js` configures how `@wordpresss/scripts` transforms JS and CSS assets during packaging.

- We use the `@wordpress/eslint-plugin/recommended-with-formatting` ruleset for JS linting since the Prettier integration is [currently unreliable in `@wordpress/scripts`](https://github.com/WordPress/gutenberg/issues/21872).

## Scripts

See the `scripts` section in `package.json` for the list of all the available scripts.

## Releases Guidelines

1. Confirm that the latest build artifacts for JS and CSS are up-to-date and tracked in the repository by running `npm run build` and confirming that git doesn't see any changes.

1. Create a new pull request to `master` for the release specific changes that increment the plugin version and add the release changelog to the README.

1. Follow the Release Checklist in the [pull request template](.github/pull_request_template.md) when preparing the release.

1. Use [semantic versioning](https://semver.org).

1. After merging the release pull request, create a new tag from the `master` branch using the latest plugin version string as the tag name.

1. Copy the latest changelog from README to the release notes.
