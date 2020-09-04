ingenerator-php-packages
========================

Manages the [composer](https://getcomposer.org) metadata repository for the custom
and internal packages used for inGenerator projects.

The project uses [satis](https://getcomposer.org/doc/articles/handling-private-packages-with-satis.md)
to update each package's metadata and build a static package archive site that can be used 
by end-users to track versions.

It uses https://github.com/FitchLearning/satisfy to dynamically build a satis.json from
two distinct config files:

### satisfy-packagelist.json

If the package to be used is simple, doesn't have its own composer.json, and is in a
git repo with properly-structured branches and version tags, add the reference to
the package and any default composer.json fields to [satisfy-packagelist.json]. 
At build time, satisfy will update the list of available versions, merge with the
defalt config, and output a `package` entry for each distinct published version.

Pros: Ensures newly tagged versions appear immediately in the repository

Cons: Risk of broken packages if a new release has dependencies, autoloader 
changes or other ways in which it needs a different package definition for
different versions.

### satis-explicit-packages.json

If the package has its own composer.json, but isn't published in packagist (or
you don't want to use the packagist version - eg we want to use a fork) then
add to this file as a `vcs` type.

If it is missing a composer.json, and doesn't follow standard version tagging,
then add a `package` entry for **each version** of the package you want to publish.
This is the bit that satisfy will take care of automatically (see above) if the 
package definition is simple.


### Building the package metadata

To update the package metadata run `build-package-repo.sh /path/to/web/dir` where
/path/to/web/dir is either an http document root or a directory you subsequently
sync to your favourite static file hosting service.
