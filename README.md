# Course Archiver

A basic plugin for Moodle that allows an administrator to find and queue courses for archival.

The plugins scheduled task will run in the background processing queued items. The courses are fully backed up and moved to the configured 
directory before being deleted. The configured directory must be writable by the web server. The course is only deleted if sucessfully 
backed up and moved to the archive directory from there they can be moved to a cloud-based archive storage provide or some other media.

One advantage of this plugin is it will clean up data in temp directory, the file pool, and trash directory. It also skips the recycle 
bin on course deletion. This is to try as be efficient as possible with storage in particular large sites with large courses. 

## Installation

1. The plugin is installed as any other Moodle plugin.

2. Unzip the source to local/coursearchiver folder on your Moodle server.
   In your Moodle site (as admin) go to Settings > Site administration > Notifications (you should get a message saying
   the plugin is installed).
