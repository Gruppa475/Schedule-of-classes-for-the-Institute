#
#<?php die('Forbidden.'); ?>
#Date: 2019-12-11 10:42:11 UTC
#Software: Joomla Platform 13.1.0 Stable [ Curiosity ] 24-Apr-2013 00:00 GMT

#Fields: datetime	priority clientip	category	message
2019-12-11T10:42:11+00:00	INFO 188.243.0.242	update	Update started by user Super User (267). Old version is 3.9.0.
2019-12-11T10:42:14+00:00	INFO 188.243.0.242	update	Downloading update file from https://s3-us-west-2.amazonaws.com/joomla-official-downloads/joomladownloads/joomla3/Joomla_3.9.13-Stable-Update_Package.zip?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=AKIAIZ6S3Q3YQHG57ZRA%2F20191211%2Fus-west-2%2Fs3%2Faws4_request&X-Amz-Date=20191211T104208Z&X-Amz-Expires=60&X-Amz-SignedHeaders=host&X-Amz-Signature=37e3ce1d7d060e30b4a32d8bf040dbc33ef50a01cae8326048614f58c8e9319c.
2019-12-11T10:42:18+00:00	INFO 188.243.0.242	update	File Joomla_3.9.13-Stable-Update_Package.zip downloaded.
2019-12-11T10:42:18+00:00	INFO 188.243.0.242	update	Starting installation of new version.
2019-12-11T10:42:30+00:00	INFO 188.243.0.242	update	Finalising installation.
2019-12-11T10:42:30+00:00	INFO 188.243.0.242	update	Ran query from file 3.9.3-2019-01-12. Query text: UPDATE `#__extensions`  SET `params` = REPLACE(`params`, '"com_categories",', '".
2019-12-11T10:42:30+00:00	INFO 188.243.0.242	update	Ran query from file 3.9.3-2019-01-12. Query text: INSERT INTO `#__action_logs_extensions` (`extension`) VALUES ('com_checkin');.
2019-12-11T10:42:30+00:00	INFO 188.243.0.242	update	Ran query from file 3.9.3-2019-02-07. Query text: INSERT INTO `#__postinstall_messages` (`extension_id`, `title_key`, `description.
2019-12-11T10:42:31+00:00	INFO 188.243.0.242	update	Ran query from file 3.9.7-2019-04-23. Query text: ALTER TABLE `#__session` ADD INDEX `client_id_guest` (`client_id`, `guest`);.
2019-12-11T10:42:31+00:00	INFO 188.243.0.242	update	Ran query from file 3.9.7-2019-04-26. Query text: UPDATE `#__content_types` SET `content_history_options` = REPLACE(`content_histo.
2019-12-11T10:42:31+00:00	INFO 188.243.0.242	update	Ran query from file 3.9.8-2019-06-11. Query text: UPDATE #__users SET params = REPLACE(params, '",,"', '","');.
2019-12-11T10:42:31+00:00	INFO 188.243.0.242	update	Ran query from file 3.9.8-2019-06-15. Query text: ALTER TABLE `#__template_styles` DROP INDEX `idx_home`;.
2019-12-11T10:42:31+00:00	INFO 188.243.0.242	update	Ran query from file 3.9.8-2019-06-15. Query text: ALTER TABLE `#__template_styles` ADD INDEX `idx_client_id` (`client_id`);.
2019-12-11T10:42:31+00:00	INFO 188.243.0.242	update	Ran query from file 3.9.8-2019-06-15. Query text: ALTER TABLE `#__template_styles` ADD INDEX `idx_client_id_home` (`client_id`, `h.
2019-12-11T10:42:31+00:00	INFO 188.243.0.242	update	Ran query from file 3.9.10-2019-07-09. Query text: ALTER TABLE `#__template_styles` MODIFY `home` char(7) NOT NULL DEFAULT '0';.
2019-12-11T10:42:31+00:00	INFO 188.243.0.242	update	Deleting removed files and folders.
2019-12-11T10:42:32+00:00	INFO 188.243.0.242	update	Cleaning up after installation.
2019-12-11T10:42:32+00:00	INFO 188.243.0.242	update	Update to version 3.9.13 is complete.
