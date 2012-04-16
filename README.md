A fork of timewasted's PHP Cloud files batch uploader, with some extra features to (someday) make it a viable backup solution for large directories, similar to rsync but with CloudFiles as a remote target.

Enhancements:
1) by default, creates a new container named like this:
[hostName]_[camelizedCurrentWorkingDirectorry] e.g.
workstation_homeMwicImportantfiles
NB: "importantFiles" has become "Importantfiles" because of its position in the camelized string -- all dir names are lowercased, to make it clear what the original dir name was... so you don't think there's anything called
/home/mwic/important/files/ 

2) Checks whether the resolved object name (see timewasted's README below) before attempting to send it

Todo:
-check modification time of the existing CF object, then delete/re-upload if local file changed more recently
-preflight using the above: interactive command line (141 objects are about to be uploaded? continue?) 
-usage notes on the command line
-package it up so you don't have to set CBU_DIR constant to get correct included code.


Original README from timewasted:

cloudfiles-batch-upload
=======================

This is a quick hack to upload a directory structure to Rackspace's Cloud Files.  It preserves the appearance of a directory structure by including the directory name as part of the file name.  For example, if you were to batch upload `/tmp`, the file `/tmp/store/images/products/1.jpg` would be uploaded to Cloud Files as `store/images/products/1.jpg`.

Usage:
------

Clone both this repository and Rackspace's [php-cloudfiles](https://github.com/rackspace/php-cloudfiles) repository.  Move `cloudfiles-batch-upload.php` into the `php-cloudfiles` directory (optionally change the `require('./cloudfiles.php');` line in `cloudfiles-batch-upload.php` to point to the correct location).  Edit the variables defined at the top of `cloudfiles-batch-upload.php`, then run the script.

