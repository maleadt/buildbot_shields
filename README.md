Buildbot Shield Status
======================

Quick and dirty HTTP status endpoint for generating Buildbot shields.


## Installation

1. Get a hold of composer, and install the [Poser](https://github.com/badges/poser) library
   using `composer install` (based on the `composer.lock` in this repo).

2. Create a MySQL user and database, and create a `private.php` with those details (see the
   `private.php.sample` template).

3. Make your buildbot master push its statuses to the `submit.php` endpoint:

   ```
   sp = reporters.HttpStatusPush(serverUrl="http://$WHEREVER/shields/submit.php")
   c['services'].append(sp)
   ```


## Usage

Use `build.php` to render badges, specifying the following GET parameters:

- `builder` (required)
- `branch` (optional, defaulting to `master`)


You can use `redirect.php` to get a hold of the build result page, which you can use to make
the badges clickable. The parameters are the same as for `build.php`.

For example, you could embed the use the following code in your `README.md`:

```

[builder-img]: http://$WHEREVER/shields/build.php?builder=foo&branch=bar
[builder-url]: http://$WHEREVER/shields/url.php?builder=foo&branch=bar

Build status: [![][builder-img]][builder-url]
```