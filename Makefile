update_hook_files_from_wp:
	if [ -f src/includes/wordpress/class-wp-hook.php ]; then rm src/includes/wordpress/class-wp-hook.php; fi;
	if [ -f src/includes/wordpress/plugin.php ]; then rm src/includes/wordpress/plugin.php; fi;
	svn export https://core.svn.wordpress.org/trunk/wp-includes/class-wp-hook.php src/includes/wordpress/class-wp-hook.php
	svn export https://core.svn.wordpress.org/trunk/wp-includes/plugin.php src/includes/wordpress/plugin.php

regen_wp_env:
	php ./function-mocker update-env --name=wp --source=vendor/wordpress/src --destination=src/includes/envs/wp
